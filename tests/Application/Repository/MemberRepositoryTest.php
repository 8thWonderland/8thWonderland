<?php

namespace Wonderland\Test\Application\Repository;

use Wonderland\Test\WonderlandTestCase;
use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Country;
use Wonderland\Application\Model\Region;
use Wonderland\Application\Repository\MemberRepository;

class MemberRepositoryTest extends WonderlandTestCase
{
    /** @var \Wonderland\Application\Repository\MemberRepository **/
    protected $repository;

    public function setUp()
    {
        $this->loadFixture('countries');
        $this->loadFixture('regions');
        $this->loadFixture('group_types');
        $this->loadFixture('groups');
        $this->loadFixture('users');
        $this->loadFixture('citizen_groups');

        $this->repository = new MemberRepository($this->getConnection());
    }

    public function testCreate()
    {
        $this->repository->create($this->getMemberMock());

        $this->assertEquals($this->repository->findOneBy([
            'login' => 'toto',
            'password' => 'totor12',
        ]), $this->getMemberMock());
    }

    public function testUpdate()
    {
        $member = $this->repository->findOneBy(['login' => 'john_doe']);

        $this->assertEquals('avatar.png', $member->getAvatar());
        $this->assertFalse($member->getIsBanned());

        $member
            ->setAvatar('trollface.jpg')
            ->setIsBanned(true)
        ;
        $this->repository->update($member);

        $member = $this->repository->findOneBy(['login' => 'john_doe']);

        $this->assertEquals('trollface.jpg', $member->getAvatar());
        $this->assertTrue($member->getIsBanned());
    }

    public function testFindOneByEmail()
    {
        $this->assertFindByOneSuccess($this->repository->findOneBy([
            'email' => 'john_doe@gmail.com',
        ]));
    }

    public function testFindOneByLoginAndPassword()
    {
        $this->assertFindByOneSuccess($this->repository->findOneBy([
            'login' => 'john_doe',
            'password' => 'toronto789321',
        ]));
    }

    public function testGetContactGroups()
    {
        $contacts = $this->repository->getContactGroups();

        $this->assertCount(3, $contacts);
        $this->assertEquals([
            [
                'id' => '1',
                'name' => 'Développeurs',
                'identity' => 'John Doe',
            ],
            [
                'id' => '2',
                'name' => 'Normandie',
                'identity' => 'Alexander',
            ],
            [
                'id' => '1',
                'name' => 'Île-de-France',
                'identity' => 'John Doe',
            ],
        ], $contacts);
    }

    public function testCountMember()
    {
        $this->assertEquals(2, $this->repository->countMembers());
    }

    protected function assertFindByOneSuccess(Member $member)
    {
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Member', $member);
        $this->assertEquals(1, $member->getId());
        $this->assertEquals('john_doe', $member->getLogin());
        $this->assertEquals('toronto789321', $member->getPassword());
        $this->assertEquals('fq6b1yr5fq1q1v87h', $member->getSalt());
        $this->assertEquals('john_doe@gmail.com', $member->getEmail());
        $this->assertTrue($member->getIsEnabled());
        $this->assertFalse($member->getIsBanned());

        $country = $member->getCountry();
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Country', $country);
        $this->assertEquals(1, $country->getId());
        $this->assertEquals('fr', $country->getCode());
        $this->assertEquals('countries.france', $country->getLabel());

        $region = $member->getRegion();
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Region', $region);
        $this->assertEquals(1, $region->getId());
        $this->assertEquals('Île-de-France', $region->getName());
        $this->assertEquals(75.366, $region->getLatitude());
        $this->assertEquals(125.84, $region->getLongitude());
        $this->assertInstanceOf('DateTime', $region->getCreatedAt());
        $this->assertEquals($country, $region->getCountry());

        $groups = $member->getGroups();
        $this->assertCount(2, $groups);
        $this->assertEquals(1, $groups[1]->getId());
        $this->assertEquals('Développeurs', $groups[1]->getName());
        $this->assertInstanceOf('DateTime', $groups[1]->getCreatedAt());
        $this->assertInstanceOf('DateTime', $groups[1]->getUpdatedAt());
    }

    /**
     * @return Member
     */
    public function getMemberMock()
    {
        return
            (new Member())
            // The Id is not used in the repository create function
            // This setter meaning is just to perform the assertEquals on the whole object
            ->setId(3)
            ->setLogin('toto')
            ->setPassword('totor12')
            ->setSalt('s9h41b3sv1zh48kytj')
            ->setCountry($this->getCountryMock())
            ->setRegion($this->getRegionMock())
            ->setIsEnabled(true)
            ->setIsBanned(false)
            ->setEmail('toto@example.com')
            ->setIdentity('Totor the Great')
            ->setLanguage('fr')
            ->setCreatedAt(new \DateTime())
            ->setLastConnectedAt(new \DateTime())
        ;
    }

    /**
     * @return Country
     */
    public function getCountryMock()
    {
        return
            (new Country())
            ->setId(1)
            ->setCode('fr')
            ->setLabel('countries.france')
        ;
    }

    /**
     * @return Region
     */
    public function getRegionMock()
    {
        return
            (new Region())
            ->setId(1)
            ->setCountry($this->getCountryMock())
            ->setLatitude(75.366)
            ->setLongitude(125.84)
            ->setName('Île-de-France')
            ->setCreatedAt(new \DateTime('2010-04-17 15:20:36'))
        ;
    }
}

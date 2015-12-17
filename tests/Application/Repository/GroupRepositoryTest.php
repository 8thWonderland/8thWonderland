<?php

namespace Wonderland\Test\Application\Repository;

use Wonderland\Test\WonderlandTestCase;

use Wonderland\Application\Model\Group;
use Wonderland\Application\Model\GroupType;
use Wonderland\Application\Model\Member;

use Wonderland\Application\Repository\GroupRepository;

class GroupRepositoryTest extends WonderlandTestCase {
    /** @var \Wonderland\Application\Repository\GroupRepository **/
    protected $repository;
    
    public function setUp() {
        $this->loadFixture('countries');
        $this->loadFixture('regions');
        $this->loadFixture('users');
        $this->loadFixture('group_types');
        $this->loadFixture('groups');
        $this->loadFixture('citizen_groups');
        
        $this->repository = new GroupRepository($this->getConnection());
    }
    
    public function testFind() {
        $this->assertEquals([
            'id' => '1',
            'name' => 'Développeurs',
            'description' => 'Equipe de développement du site',
            'contact_id' => '1',
            'identity' => 'John Doe',
            'created_at' => '2015-08-22 15:30:00',
            'updated_at' => '2015-09-01 23:54:32',
            'label' => 'groups.thematic'
        ], $this->repository->find(1));
    }
    
    public function testFindGroups() {
        $groups = $this->repository->findGroups();
        
        $this->assertCount(3, $groups);
        $this->assertEquals([
            [
                'id' => '1',
                'name' => 'Développeurs',
                'description' => 'Equipe de développement du site',
                'identity' => 'John Doe',
                'created_at' => '2015-08-22 15:30:00',
                'updated_at' => '2015-09-01 23:54:32',
                'label' => 'groups.thematic'
            ],[
                'id' => '3',
                'name' => 'Normandie',
                'description' => 'Groupe régional de Normandie',
                'identity' => 'Alexander',
                'created_at' => '2014-05-17 10:30:00',
                'updated_at' => '2014-12-15 17:52:50',
                'label' => 'groups.regional'
            ], [
                'id' => '2',
                'name' => 'Île-de-France',
                'description' => "Groupe régional d'Île-de-France",
                'identity' => 'John Doe',
                'created_at' => '2013-10-13 14:07:00',
                'updated_at' => '2013-10-13 14:07:00',
                'label' => 'groups.regional'
            ]
        ], $groups);
    }
    
    public function testFindRegionalGroups() {
        $groups = $this->repository->findRegionalGroups();
        
        $this->assertCount(2, $groups);
        $this->assertEquals([
            [
                'id' => '3',
                'name' => 'Normandie',
                'description' => 'Groupe régional de Normandie',
                'identity' => 'Alexander',
                'created_at' => '2014-05-17 10:30:00',
                'updated_at' => '2014-12-15 17:52:50',
                'label' => 'groups.regional',
                'longitude' => '113.252',
                'latitude' => '63.465'
            ], [
                'id' => '2',
                'name' => 'Île-de-France',
                'description' => "Groupe régional d'Île-de-France",
                'identity' => 'John Doe',
                'created_at' => '2013-10-13 14:07:00',
                'updated_at' => '2013-10-13 14:07:00',
                'label' => 'groups.regional',
                'longitude' => '125.84',
                'latitude' => '75.366'
            ]
        ], $groups);
    }
    
    public function testCountGroupMembers() {
        $this->assertEquals(2, $this->repository->countGroupMembers(1));
    }
    
    public function testFindGroupMembers() {
        $members = $this->repository->findGroupMembers(1);
        
        $this->assertCount(2, $members);
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Member', $members[0]);
        $this->assertEquals('2', $members[0]->getId());
        $this->assertEquals('Alexander', $members[0]->getIdentity());
        $this->assertInstanceOf('DateTime', $members[0]->getLastConnectedAt());
    }
    
    public function testUpdate() {
        $group =
            (new Group())
            ->setId(1)
            ->setName('Support')
            ->setDescription('Equipe de support')
            ->setType(
                (new GroupType())
                ->setId(1)
            )
            ->setContact(
                (new Member())
                ->setId(1)
            )
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
        ;
        $this->repository->update($group);
        
        $data = $this->repository->find(1);
        
        $this->assertEquals('Support', $data['name']);
        $this->assertEquals('Equipe de support', $data['description']);
    }
}
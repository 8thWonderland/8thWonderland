<?php

namespace Wonderland\Test\Application\Model;

use Wonderland\Application\Model\MotionTheme;

class MotionThemeTest extends \PHPUnit_Framework_TestCase {
    public function testModel() {
        $motionTheme =
            (new MotionTheme())
            ->setId(1)
            ->setLabel('motions.constitutional')
            ->setDuration(8)
        ;
        $this->assertEquals(1, $motionTheme->getId());
        $this->assertEquals('motions.constitutional', $motionTheme->getLabel());
        $this->assertEquals(8, $motionTheme->getDuration());
    }
}
<?php

namespace iFlair\LetsBonusAdminBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

class LoadFrontUserIncrement extends LoadContainerFixtures
{
    public function load(ObjectManager $manager)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $sql = 'ALTER TABLE lb_front_user AUTO_INCREMENT = 12000000';
        $stmt = $em->getConnection()->prepare($sql);
        $result = $stmt->execute();
    }
}

<?php

namespace iFlair\LetsBonusAdminBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

class LoadShopIncrement extends LoadContainerFixtures
{
    public function load(ObjectManager $manager)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $sql = 'ALTER TABLE lb_shop AUTO_INCREMENT = 1001';
        $stmt = $em->getConnection()->prepare($sql);
        $result = $stmt->execute();
    }
}

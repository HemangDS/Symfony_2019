<?php

namespace iFlair\LetsBonusAdminBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use iFlair\LetsBonusAdminBundle\Entity\ZanoxConfig;

class LoadZanoxConfigData extends LoadContainerFixtures
{
    public function load(ObjectManager $manager)
    {
        if (!$ZanoxConfig = $this->checkIFZanoxConfigExists($this->getContainer()->getParameter('zenox_connect_id'))) {
            $ZanoxConfig = new ZanoxConfig();
            $ZanoxConfig->setConnectId($this->getContainer()->getParameter('zenox_connect_id'));
            $ZanoxConfig->setSecretKey($this->getContainer()->getParameter('zenox_secret_key'));
            $manager->persist($ZanoxConfig);
            $manager->flush();
        }

        $this->addReference('ZanoxConfig', $ZanoxConfig);
    }

    protected function checkIFZanoxConfigExists($connectId)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('iFlairLetsBonusAdminBundle:ZanoxConfig')
            ->findOneByConnectId($connectId);
    }
}

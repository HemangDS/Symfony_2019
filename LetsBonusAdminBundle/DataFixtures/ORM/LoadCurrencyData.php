<?php

namespace iFlair\LetsBonusAdminBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use iFlair\LetsBonusAdminBundle\Entity\Currency;

class LoadCurrencyData extends LoadContainerFixtures
{
    public function load(ObjectManager $manager)
    {
        if (!$Currency = $this->checkIFCurrencyExists(Currency::DEFAULTCURRENCY)) {
            $Currency = new Currency();
            $Currency->setCode(Currency::DEFAULTCURRENCY);
            $manager->persist($Currency);
            $manager->flush();
        }

        $this->addReference('Currency', $Currency);
    }

    protected function checkIFCurrencyExists($code)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('iFlairLetsBonusAdminBundle:Currency')
            ->findOneByCode($code);
    }
}

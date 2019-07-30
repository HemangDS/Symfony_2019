<?php

namespace iFlair\LetsBonusAdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use iFlair\LetsBonusAdminBundle\Entity\Companies;
use iFlair\LetsBonusAdminBundle\Entity\Currency;

class LoadCompanyData extends LoadContainerFixtures implements DependentFixtureInterface
{
    public function getDependencies()
    {
        // fixture classes that this fixture is dependent on
        return array(
            'iFlair\LetsBonusAdminBundle\DataFixtures\ORM\LoadCurrencyData',
            'iFlair\LetsBonusAdminBundle\DataFixtures\ORM\LoadLanguageData',
        );
    }

    public function load(ObjectManager $manager)
    {
        if (!$Company = $this->checkIFCompanyExists($this->getContainer()->getParameter('default_company_name'))) {
            $Company = new Companies();
            $Company->setName($this->getContainer()->getParameter('default_company_name'));
            $Company->setCurrency($this->getReference('Currency'));
            $Company->setIsoCode($this->getContainer()->getParameter('default_companay_iso_code'));
            $Company->setLang($this->getReference('Language'));
            $Company->setCommonConditions('Nothing');
            $Company->setHoursOffset(0);
            $Company->setTimeZone(0); //load dynamic timezone or default timezone
            $manager->persist($Company);
            $manager->flush();
        }

        $this->addReference('Company', $Company);
    }

    protected function checkIFCompanyExists($name)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Companies')
            ->findOneByName($name);
    }
}

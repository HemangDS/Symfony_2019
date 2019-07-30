<?php

namespace iFlair\LetsBonusAdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use iFlair\LetsBonusAdminBundle\Entity\VoucherTradeDoublerSiteToken;

class LoadVoucherTradeDoublerSiteTokenData extends LoadContainerFixtures implements DependentFixtureInterface
{
    public function getDependencies()
    {
        // fixture classes that this fixture is dependent on
        return array(
            'iFlair\LetsBonusAdminBundle\DataFixtures\ORM\LoadCompanyData',
        );
    }

    public function load(ObjectManager $manager)
    {
        if (!$VoucherTradeDoublerSiteToken = $this->checkIFVoucherTradeDoublerSiteTokenExists($this->getContainer()->getParameter('site_id'))) {
            $VoucherTradeDoublerSiteToken = new VoucherTradeDoublerSiteToken();
            $VoucherTradeDoublerSiteToken->setSiteId($this->getContainer()->getParameter('site_id'));
            $VoucherTradeDoublerSiteToken->setSiteName($this->getContainer()->getParameter('site_name'));
            $VoucherTradeDoublerSiteToken->setSiteToken($this->getContainer()->getParameter('site_token'));
            $VoucherTradeDoublerSiteToken->setCompany($this->getReference('Company'));
            $manager->persist($VoucherTradeDoublerSiteToken);
            $manager->flush();
        }

        $this->addReference('VoucherTradeDoublerSiteToken', $VoucherTradeDoublerSiteToken);
    }

    protected function checkIFVoucherTradeDoublerSiteTokenExists($siteid)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:VoucherTradeDoublerSiteToken')
            ->findOneBySiteId($siteid);
    }
}

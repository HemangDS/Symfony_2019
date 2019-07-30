<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\VoucherTradeDoublerSiteToken;
use iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms;
use iFlair\LetsBonusAdminBundle\Entity\Language;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Voucher;
use iFlair\LetsBonusAdminBundle\Entity\Network;
use iFlair\LetsBonusAdminBundle\Entity\Shop;

class TDVoucherFeedsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:tdvoucherfeeds')->setDescription('TradeDoubler voucher feeds for site id 2389266 i.e. Letsbonus España');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $maxStartDate = strtotime(date('Y-m-d H:i:s', strtotime(date('Y-m-d').' 00:00:00'))).'000'; //Converted to epoch time
        $minEndDate = strtotime(date('Y-m-d H:i:s', strtotime(date('Y-m-d').' 23:59:59'.'-30 days'))).'999';    //Converted to epoch time

        $em = $this->getContainer()->get('doctrine')->getManager();
        $tradeDoublerSite = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherTradeDoublerSiteToken')->findOneBy(array('siteId' => trim($this->getContainer()->getParameter('site_id'))));

        //To check if site added
        if (count($tradeDoublerSite) > 0) {
            $voucherFeedsJSON = file_get_contents("http://api.tradedoubler.com/1.0/vouchers.json;minEndDate=$minEndDate;maxStartDate=$maxStartDate?token=".$tradeDoublerSite->getSiteToken());
            $voucherFeeds = json_decode($voucherFeedsJSON);

            foreach ($voucherFeeds as $voucherFeed) {
                $refVoucherId = $voucherFeed->id;
                if (!$this->checkIFVoucherExists($refVoucherId)) {
                    $voucher = new Voucher();
                    $programId = $voucherFeed->programId;
                    $voucherPrograms = $this->checkIFVoucherProgramExists($programId);
                    if (!$voucherPrograms) {
                        //create new voucher program
                        $voucherPrograms = new VoucherPrograms();
                        $voucherPrograms->setNprogramId($programId);
                        $voucherPrograms->setProgramName($voucherFeed->programName);
                        $voucherPrograms->setLogoPath($voucherFeed->logoPath);
                        $voucherPrograms->setNetwork($this->getTradeDoublerNetwork());
                        $em->persist($voucherPrograms);
                        $em->flush();
                    }

                    $languageId = $voucherFeed->languageId;
                    $language = $this->checkIFLanguageExists($languageId); //Check for the existing language
                    if (!$language) {
                        //If not found return default language						
                        $language = $this->checkIFLanguageExists(Voucher::VOUCHERDEFAULTLANGCODE);
                    }

                    $currencyId = $voucherFeed->currencyId;
                    $currency = $this->checkIFCurrencyExists($currencyId); //Check for the existing language
                    if (!$currencyId) {
                        //If not found return default currency						
                        $currency = $this->checkIFCurrencyExists(Voucher::VOUCHERDEFAULTCURRENCYCODE);
                    }

                    $voucher->setRefVoucherId($refVoucherId);
                    $voucher->setCode($voucherFeed->code);
                    $voucher->setPublishStartDate(new \DateTime(date('Y-m-d H:i:s', ($voucherFeed->publishStartDate) / 1000))); //Converted from epic time
                    $voucher->setPublishEndDate(new \DateTime(date('Y-m-d H:i:s', ($voucherFeed->publishEndDate) / 1000))); ////Converted from epic time
                    $voucher->setTitle($voucherFeed->title);
                    $voucher->setShortDescription($voucherFeed->shortDescription);
                    $voucher->setDescription($voucherFeed->description);
                    $voucher->setVoucherTypeId($voucherFeed->voucherTypeId);
                    $voucher->setDefaultTrackUri($voucherFeed->defaultTrackUri);
                    $voucher->setSiteSpecific(($voucherFeed->siteSpecific) ? Voucher::YES : Voucher::NO);
                    $voucher->setLandingUrl((isset($voucherFeed->landingUrl) && !empty($voucherFeed->landingUrl)) ? $voucherFeed->landingUrl : '');
                    $voucher->setDiscountAmount($voucherFeed->discountAmount);
                    $voucher->setIsPercentage(($voucherFeed->isPercentage) ? Voucher::YES : Voucher::NO);
                    $voucher->setPublisherInfo($voucherFeed->publisherInformation);
                    $voucher->setExclusive(($voucherFeed->exclusive) ? Voucher::YES : Voucher::NO);
                    $voucher->setIsNew(Voucher::NO);
                    $voucher->setProgram($voucherPrograms);
                    $voucher->setLanguage($language);
                    $voucher->setCurrency($currency);
                    $voucher->setNetwork($this->getTradeDoublerNetwork());
                    $voucher->setStatus(Voucher::VOUCHERACTIVE); //Default status active
                    $voucher->setIsDisplayOnFront(Voucher::YES);
                    $em->persist($voucher);
                    $em->flush();

                    $shops = $this->getVoucherProgramSpecificShops($voucherPrograms);
                    if($shops) {
                        foreach($shops as $shop) {
                            $shop->addVoucher($voucher);
                            $em->persist($shop);
                            $em->flush();
                        }
                    }
                }
            }
        }
    }

    protected function checkIFVoucherProgramExists($programId)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms')
            //->findOneByNprogramId($programId);
            ->findOneBy(array('nprogramId' => $programId, 'network' => $this->getTradeDoublerNetwork()));
    }

    protected function checkIFVoucherExists($voucherId)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Voucher')
            ->findOneByRefVoucherId($voucherId);
    }

    protected function checkIFLanguageExists($code)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Language')
            ->findOneByCode($code);
    }

    protected function checkIFCurrencyExists($code)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Currency')
            ->findOneByCode($code);
    }
    //Assuming name=TradeDoubler
    protected function getTradeDoublerNetwork()
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName(Network::TRADEDOUBLER);
    }

    protected function getVoucherProgramSpecificShops($voucherProgram)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Shop')
            ->findBy(array(
                'vprogram' => $voucherProgram
            ));
    }
}

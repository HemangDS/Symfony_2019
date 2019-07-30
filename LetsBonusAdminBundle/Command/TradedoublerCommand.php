<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Network;

require_once 'AppShell.php';

class TradedoublerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:tradedoubler')->setDescription('Use to Store Sales Data to Database Of TradeDoubler');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $AFID_LETSBONUS=2389266;
        $AFID_SHOPPIDAY=2830790;
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');

        $containerEmailObject = $this->getContainer()->get('mailer');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $tradedoubler_obj = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findByName(Network::TRADEDOUBLER);
        $tradedoubler_id = '';
        foreach ($tradedoubler_obj as $tradedoubler) {
            $tradedoubler_id = $tradedoubler->getId();
        }
        if (!empty($tradedoubler_id)):
            $networkAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:networkCredentials')->findByNetwork(trim($tradedoubler_id));
            foreach ($networkAvailable as $credentials) {
                $tradedoubler_affiliate_id = $credentials->getTradedoublerAffiliateId();
                $tradedoubler_key = $credentials->getTradedoublerKey();
                $tradedoubler_url = $credentials->getTradedoublerUrl();

                $containerEmailObject = $this->getContainer()->get('mailer');
                $sendEmail = new \AppShell();
                /*$startDate = date('d/m/Y', time() - 60 * 60 * 24 * 10);
                $endDate = date('d/m/Y', time() - 60 * 60 * 24);
                $endDate = date('d/m/Y');*/

                $endDate = date('d/m/Y');
                $startDate = Date('d/m/Y', strtotime('-90 days'));
                $tradeURL='';
                //old api
                /*$tradeURL = $tradedoubler_url.'?reportName=aAffiliateEventBreakdownReport&columns=timeOfVisit&columns=productName&columns=timeOfEvent&columns=programId&
                columns=eventId&columns=timeInSession&columns=lastModified&columns=epi1&columns=epi2&columns=epi3&columns=eventName&columns=pendingStatus&
                columns=siteName&columns=graphicalElementName&columns=productName&columns=productNrOf&columns=productValue&columns=open_product_feeds_id&
                columns=open_product_feeds_name&columns=voucher_code&columns=deviceType&columns=os&columns=browser&columns=vendor&columns=device&columns=affiliateCommission&
                columns=link&columns=leadNR&columns=orderNR&columns=pendingReason&columns=orderValue&startDate='.$startDate.'&endDate='.$endDate.'&metric1.lastOperator=/&currencyId=EUR&
                event_id=0&pending_status=1&organizationId=1905507&includeWarningColumn=tru e&metric1.summaryType=NONE&includeMobile=1&latestDayToExecute=0&
                metric1.operator1=/&breakdownOption=1&reportTitleTextKey=REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE&setColumns=true&
                metric1.columnName1=orderValue&metric1.columnName2=orderValue&decorator=popupDecorator&metric1.midOperator=/&affiliateId='.$tradedoubler_affiliate_id.'&
                dateSelectionType=1&sortBy=timeOfEvent&filterOnTimeHrsInterval=false&customKeyMetricCount=0&applyNamedDecorator=true&key='.$tradedoubler_key.'&format=XML';

                //new api
                $tradeURL = $tradedoubler_url.'?metric1.summaryType=NONE&metric1.lastOperator=/&metric1.columnName2=orderValue&metric1.operator1=/&metric1.columnName1=orderValue&metric1.midOperator=/&customKeyMetricCount=0&columns=orderValue&columns=pendingReason&columns=orderNR&columns=leadNR&columns=link&columns=affiliateCommission&columns=device&columns=vendor&columns=browser&columns=os&columns=deviceType&columns=voucher_code&columns=open_product_feeds_name&columns=open_product_feeds_id&columns=productValue&columns=productNrOf&columns=productName&columns=graphicalElementName&columns=siteName&columns=pendingStatus&columns=eventName&columns=epi1&columns=lastModified&columns=timeInSession&columns=timeOfEvent&columns=timeOfVisit&columns=programName&includeWarningColumn=true&dateSelectionType=1&filterOnTimeHrsInterval=false&event_id=0&endDate='.$endDate.'&startDate='.$startDate.'&includeMobile=1&breakdownOption=1&sortBy=timeOfEvent&pending_status=1&currencyId=EUR&affiliateId='.$tradedoubler_affiliate_id.'&latestDayToExecute=0&setColumns=true&reportTitleTextKey=REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE&reportName=aAffiliateEventBreakdownReport&organizationId=2052160&key='.$tradedoubler_key.'&format=XML';
                $tradeURL = $tradedoubler_url.'?metric1.summaryType=NONE&metric1.lastOperator=/&metric1.columnName2=orderValue&metric1.operator1=/&metric1.columnName1=orderValue&metric1.midOperator=/&customKeyMetricCount=0&columns=orderValue&columns=pendingReason&columns=orderNR&columns=leadNR&columns=link&columns=affiliateCommission&columns=device&columns=vendor&columns=browser&columns=os&columns=deviceType&columns=voucher_code&columns=open_product_feeds_name&columns=open_product_feeds_id&columns=productValue&columns=productNrOf&columns=productName&columns=graphicalElementName&columns=siteName&columns=pendingStatus&columns=eventName&columns=epi1&columns=lastModified&columns=timeInSession&columns=timeOfEvent&columns=timeOfVisit&columns=programName&includeWarningColumn=true&dateSelectionType=1&filterOnTimeHrsInterval=false&event_id=0&endDate='.$endDate.'&startDate='.$startDate.'&includeMobile=1&breakdownOption=1&sortBy=timeOfEvent&pending_status=1&currencyId=EUR&affiliateId='.$tradedoubler_affiliate_id.'&latestDayToExecute=0&setColumns=true&reportTitleTextKey=REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE&reportName=aAffiliateEventBreakdownReport&organizationId=2052160&key='.$tradedoubler_key.'&format=XML';*/
                if($tradedoubler_affiliate_id==$AFID_LETSBONUS) {
                    $tradeURL = $tradedoubler_url.'?metric1.summaryType=NONE&metric1.lastOperator=/&metric1.columnName2=orderValue&metric1.operator1=/&metric1.columnName1=orderValue&metric1.midOperator=/&customKeyMetricCount=0&columns=orderValue&columns=pendingReason&columns=orderNR&columns=leadNR&columns=link&columns=affiliateCommission&columns=device&columns=vendor&columns=browser&columns=os&columns=deviceType&columns=voucher_code&columns=open_product_feeds_name&columns=open_product_feeds_id&columns=productValue&columns=productNrOf&columns=productName&columns=graphicalElementName&columns=siteName&columns=pendingStatus&columns=eventName&columns=epi1&columns=lastModified&columns=timeInSession&columns=timeOfEvent&columns=timeOfVisit&columns=programName&includeWarningColumn=true&dateSelectionType=1&filterOnTimeHrsInterval=false&event_id=0&endDate='.$endDate.'&startDate='.$startDate.'&includeMobile=1&breakdownOption=1&sortBy=timeOfEvent&pending_status=1&currencyId=EUR&affiliateId=2389266&latestDayToExecute=0&setColumns=true&reportTitleTextKey=REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE&reportName=aAffiliateEventBreakdownReport&organizationId=1905507&key='.$tradedoubler_key.'&format=XML';
                }else if($tradedoubler_affiliate_id==$AFID_SHOPPIDAY) {
                    $tradeURL = $tradedoubler_url.'?metric1.summaryType=NONE&metric1.lastOperator=/&metric1.columnName2=orderValue&metric1.operator1=/&metric1.columnName1=orderValue&metric1.midOperator=/&customKeyMetricCount=0&columns=orderValue&columns=pendingReason&columns=orderNR&columns=leadNR&columns=link&columns=affiliateCommission&columns=device&columns=vendor&columns=browser&columns=os&columns=deviceType&columns=voucher_code&columns=open_product_feeds_name&columns=open_product_feeds_id&columns=productValue&columns=productNrOf&columns=productName&columns=graphicalElementName&columns=siteName&columns=pendingStatus&columns=eventName&columns=epi1&columns=lastModified&columns=timeInSession&columns=timeOfEvent&columns=timeOfVisit&columns=programName&includeWarningColumn=true&dateSelectionType=1&filterOnTimeHrsInterval=false&event_id=0&endDate='.$endDate.'&startDate='.$startDate.'&includeMobile=1&breakdownOption=1&sortBy=timeOfEvent&pending_status=1&currencyId=EUR&affiliateId=2830790&latestDayToExecute=0&setColumns=true&reportTitleTextKey=REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE&reportName=aAffiliateEventBreakdownReport&organizationId=2052160&key='.$tradedoubler_key.'&format=XML';
                }
                //new static
                //$tradeURL = 'https://reports.tradedoubler.com/pan/aReport3Key.action?metric1.summaryType=NONE&metric1.lastOperator=/&metric1.columnName2=orderValue&metric1.operator1=/&metric1.columnName1=orderValue&metric1.midOperator=/&customKeyMetricCount=0&columns=orderValue&columns=pendingReason&columns=orderNR&columns=leadNR&columns=link&columns=affiliateCommission&columns=device&columns=vendor&columns=browser&columns=os&columns=deviceType&columns=voucher_code&columns=open_product_feeds_name&columns=open_product_feeds_id&columns=productValue&columns=productNrOf&columns=productName&columns=graphicalElementName&columns=siteName&columns=pendingStatus&columns=eventName&columns=epi1&columns=lastModified&columns=timeInSession&columns=timeOfEvent&columns=timeOfVisit&columns=programName&includeWarningColumn=true&dateSelectionType=1&filterOnTimeHrsInterval=false&event_id=0&endDate=4/11/16&startDate=31/07/16&includeMobile=1&breakdownOption=1&sortBy=timeOfEvent&pending_status=1&currencyId=EUR&affiliateId=2830790&latestDayToExecute=0&setColumns=true&reportTitleTextKey=REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE&reportName=aAffiliateEventBreakdownReport&organizationId=2052160&key=e4c54d8f3b5c3fa35ca23b946ddcdfbf&format=XML';
                //old static
                //$tradeURL = 'https://reports.tradedoubler.com/pan/aReport3Key.action?metric1.summaryType=NONE&metric1.lastOperator=/&metric1.columnName2=orderValue&metric1.operator1=/&metric1.columnName1=orderValue&metric1.midOperator=/&customKeyMetricCount=0&columns=orderValue&columns=pendingReason&columns=orderNR&columns=leadNR&columns=link&columns=affiliateCommission&columns=device&columns=vendor&columns=browser&columns=os&columns=deviceType&columns=voucher_code&columns=open_product_feeds_name&columns=open_product_feeds_id&columns=productValue&columns=productNrOf&columns=productName&columns=graphicalElementName&columns=siteName&columns=pendingStatus&columns=eventName&columns=epi1&columns=lastModified&columns=timeInSession&columns=timeOfEvent&columns=timeOfVisit&columns=programName&includeWarningColumn=true&dateSelectionType=1&filterOnTimeHrsInterval=false&event_id=0&endDate=4/11/16&startDate=31/07/16&includeMobile=1&breakdownOption=1&sortBy=timeOfEvent&pending_status=1&currencyId=EUR&affiliateId=2389266&latestDayToExecute=0&setColumns=true&reportTitleTextKey=REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE&reportName=aAffiliateEventBreakdownReport&organizationId=1905507&key=2035e52b7f0f05415202d5ec4e21f9d8&format=XML';
                $tradeDatas = simplexml_load_file($tradeURL);

                if ($tradeDatas !==  false) {
                    $reportTitle = $tradeDatas['title'];
                    $reportTime = $tradeDatas['time'];
                    $count = 0;
                    $recordsCount =  count($tradeDatas->matrix->rows->row);
                    foreach ($tradeDatas->matrix->rows->row as $row):
                       if (!$this->alreadySaved($row, $em)):
                            $tradeEntity = new LetsBonusTransactions();
                            $this->saveTDtransaction($row, $tradeEntity, $em);
                        else:
                            echo "Already saved\n";
                        endif;
                    endforeach;
                } else {
                    $this->sendAdminAlert('TD transactions ', "Report XML empty from $startDate to $endDate",$containerEmailObject, $admin_email_id);
                }
            }
        else:
            die('Network Entry Not Inserted');
        endif;
    }

    private function alreadySaved($saleItem, $em)
    {
        /*print_r($saleItem);*/

        $transactionId = $saleItem->eventId.'-'.$saleItem->orderNR.'-'.$saleItem->leadNR.'-'.$saleItem->epi1;

        $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findOneBy(
          array(
              'transactionId' => $transactionId,
              'referenceId' => $saleItem->eventId,
              'status' => (string) $saleItem->pendingStatus,
              'trackingDate' => (string) $saleItem->timeOfEvent,
              'modifiedDate' => (string) $saleItem->lastModified,
              'programId' => (string) $saleItem->programId,
              'programName' => (string) $saleItem->programName,
              'leadNumber' => (string) $saleItem->leadNR,
              'orderNumber' => (string) $saleItem->orderNR,
        ));

        if (!empty($alreadyAvailable)):
            return true;
        endif;

        return false;
    }
    private function saveTDtransaction($tradedublerData, $tradeEntity, $em)
    {
        $transactionId = $tradedublerData->eventId.'-'.$tradedublerData->orderNR.'-'.$tradedublerData->leadNR.'-'.$tradedublerData->epi1;

        if ($transactionId) {
            $tradeEntity->setTransactionId($transactionId);
        } else {
            $tradeEntity->setTransactionId(null);
        }

        if ($tradedublerData->eventId) {
            $tradeEntity->setReferenceId($tradedublerData->eventId);
        } else {
            $tradeEntity->setReferenceId(null);
        }

        $tradeEntity->setNetwork($this->getTradedoublerNetwork());
        $tradeEntity->setCurrency($this->getTradedoublerCurrency('EUR'));

        if ($tradedublerData->pendingStatus) {
            $tradeEntity->setStatus((string) $tradedublerData->pendingStatus);
        } else {
            $tradeEntity->setStatus(null);
        }

        if ($tradedublerData->pendingReason) {
            $tradeEntity->setStatusMessage($tradedublerData->pendingReason);
        } else {
            $tradeEntity->setStatusMessage(null);
        }

        if ($tradedublerData->affiliateCommission) {
            $tradeEntity->setCommission((float) $tradedublerData->affiliateCommission);
        } else {
            $tradeEntity->setCommission(0);
        }

        if ($tradedublerData->orderValue) {
            $tradeEntity->setAmount((float) $tradedublerData->orderValue);
        } else {
            $tradeEntity->setAmount(0);
        }

        if ($tradedublerData->timeOfEvent) {
            $tradeEntity->setTrackingDate($tradedublerData->timeOfEvent);
        } // tracking date
        else {
            $tradeEntity->setTrackingDate(null);
        }

        if ($tradedublerData->lastModified) {
            $tradeEntity->setModifiedDate($tradedublerData->lastModified);
        } // modified date
        else {
            $tradeEntity->setModifiedDate(null);
        }

        if ($tradedublerData->timeOfVisit) {
            $tradeEntity->setClickDate($tradedublerData->timeOfVisit);
        } // click date
        else {
            $tradeEntity->setClickDate(null);
        }

        if ($tradedublerData->programId) {
            $tradeEntity->setProgramId($tradedublerData->programId);
        } // program_id
        else {
            $tradeEntity->setProgramId(0);
        }

        if ($tradedublerData->programName) {
            $tradeEntity->setProgramName($tradedublerData->programName);
        } // program_name
        else {
            $tradeEntity->setProgramName('');
        }

        if ($tradedublerData->productName) {
            $tradeEntity->setProductName($tradedublerData->productName);
        } // productName
        else {
            $tradeEntity->setProductName('');
        }

        if ($tradedublerData->leadNR) {
            $tradeEntity->setLeadNumber($tradedublerData->leadNR);
        } // leadnumber                
        else {
            $tradeEntity->setLeadNumber(0);
        }

        if ($tradedublerData->orderNR) {
            $tradeEntity->setOrderNumber($tradedublerData->orderNR);
        } // orderNumber                
        else {
            $tradeEntity->setOrderNumber(0);
        }

        if(isset($tradedublerData->epi1) && !empty($tradedublerData->epi1) && !$tradedublerData->epi1==null && !$tradedublerData->epi1=='') {
            $tradeEntity->setParam0($tradedublerData->epi1);
        } // param0
        else {
            $tradeEntity->setParam0(0);
        }

        if(isset($tradedublerData->epi2) && !empty($tradedublerData->epi2) && !$tradedublerData->epi2==null && !$tradedublerData->epi2=='') {
            $tradeEntity->setParam1($tradedublerData->epi2);
        } // param1
        else {
            $tradeEntity->setParam1(0);
        }

        $tradeEntity->setParam2(0);

        $tradeEntity->setProcessed('PENDING');
        $tradeEntity->setProcessedDate(new \DateTime());
        /*$tradeEntity->setShop(NULL);*/
        $em->persist($tradeEntity);
        $em->flush();
    }
    protected function getTradedoublerNetwork()
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName(Network::TRADEDOUBLER);
    }
    protected function getTradedoublerCurrency($CurrencyCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Currency')
            ->findOneByCode($CurrencyCode);
    }
}

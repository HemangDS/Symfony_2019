<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Network;

require_once 'AppShell.php';

class TDITransactionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:tditransaction')->setDescription('Use to Store Sales Data to Database Of TradeDoubler');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $tdi_obj = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findByName(Network::TDI);
        $tdi_id = '';
        foreach ($tdi_obj as $tdi) {
            $tdi_id = $tdi->getId();
        }

        if (!empty($tdi_id)):
            $networkAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:networkCredentials')->findByNetwork(trim($tdi_id));
        foreach ($networkAvailable as $credentials) {
            $tdi_affiliate_id = $credentials->getTdtiAffiliateId();
            $tdi_key = $credentials->getTdtiKey();
            $tdi_url = $credentials->getTdtiUrl();

            $containerEmailObject = $this->getContainer()->get('mailer');
            $sendEmail = new \AppShell();
            $EndDate = date('d/m/y');
            $StartDate = Date('d/m/y', strtotime('-30 days'));
            $reportURLXML = $tdi_url.'?reportName=aAffiliateEventBreakdownReport&columns=order_number&columns=slccId&columns=product_name&columns=product_number&columns=productNrOf&colums=leadNR&columns=product_value&columns=timeOfVisit&columns=timeOfEvent&columns=timeInSession&columns=lastModified&columns=epi1&columns=epi2&columns=programName&columns=programId&columns=eventName&columns=eventId&columns=pendingStatus&columns=siteName&columns=graphicalElementName&columns=productName&columns=productNrOf&columns=productValue&columns=open_product_feeds_id&columns=open_product_feeds_name&columns=voucher_code&columns=deviceType&columns=os&columns=browser&columns=vendor&columns=device&columns=affiliateCommission&columns=link&columns=leadNR&columns=orderNR&columns=pendingReason&columns=orderValue&startDate='.$StartDate.'&endDate='.$EndDate.'&metric1.lastOperator=/&currencyId=EUR&event_id=0&pending_status=1&organizationId=1956008&includeWarningColumn=true&metric1.summaryType=NONE&includeMobile=1&latestDayToExecute=0&metric1.operator1=/&breakdownOption=1&reportTitleTextKey=REPORT3_SERVICE_REPORTS_AAFFILIATEEVENTBREAKDOWNREPORT_TITLE&setColumns=true&metric1.columnName1=orderValue&metric1.columnName2=orderValue&decorator=popupDecorator&metric1.midOperator=/&affiliateId='.$tdi_affiliate_id.'&dateSelectionType=1&sortBy=timeOfEvent&filterOnTimeHrsInterval=false&customKeyMetricCount=0&applyNamedDecorator=true&key='.$tdi_key.'&format=XML';
            $reportXML = simplexml_load_file($reportURLXML);

            if ($reportXML !==  false) {
                $reportTitle = $reportXML['title'];
                $reportTime = $reportXML['time'];

                $count = 0;
                foreach ($reportXML->matrix->rows->row as $row):

                        if (!$this->alreadySaved($row, $em)):
                            $tdiEntity = new LetsBonusTransactions();
                $this->saveTDItransaction($row, $tdiEntity, $em); else:
                            echo "Already saved\n";
                endif;

                endforeach;
            } else {
                $sendEmail->sendAdminAlert('TDI transactions ', "Report XML empty from $startDate to $endDate", $containerEmailObject, $admin_email_id);
            }
        } else:
            die('Network Entry Not Inserted');
        endif;
    }

    private function alreadySaved($saleItem, $em)
    {
        $transactionId = $saleItem->eventId.'-'.$saleItem->orderNR.'-'.$saleItem->leadNR.'-'.$saleItem->epi1;

        $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findBy(array(
            'transactionId' => trim($transactionId),
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

    private function saveTDItransaction($tradedublerData, $tdiEntity, $em)
    {
        $transactionId = $tradedublerData->eventId.'-'.$tradedublerData->orderNR.'-'.$tradedublerData->leadNR.'-'.$tradedublerData->epi1;

        $tdiEntity->setTransactionId($transactionId = $tradedublerData->eventId.'-'.$tradedublerData->orderNR.'-'.$tradedublerData->leadNR.'-'.$tradedublerData->epi1, $transactionId = $tradedublerData->eventId.'-'.$tradedublerData->orderNR.'-'.$tradedublerData->leadNR.'-'.$tradedublerData->epi1);

        if ($tradedublerData->eventId) {
            $tdiEntity->setReferenceId($tradedublerData->eventId, $tradedublerData->eventId);
        } else {
            $tdiEntity->setReferenceId(null);
        }

        $tdiEntity->setNetwork($this->getTditransactionNetwork());
        $tdiEntity->setCurrency($this->getTditransactionCurrency('EUR'));

        if ($tradedublerData->pendingStatus) {
            $tdiEntity->setStatus($tradedublerData->pendingStatus);
        } else {
            $tdiEntity->setStatus(null);
        }

        $tdiEntity->setStatusName(null);
        $tdiEntity->setStatusState(null);

        if ($tradedublerData->pendingReason) {
            $tdiEntity->setStatusMessage($tradedublerData->pendingReason);
        } else {
            $tdiEntity->setStatusMessage(null);
        }

        if ($tradedublerData->affiliateCommission) {
            $tdiEntity->setCommission((float) $tradedublerData->affiliateCommission);
        } else {
            $tdiEntity->setCommission(0);
        }

        if ($tradedublerData->orderValue) {
            $tdiEntity->setAmount((float) $tradedublerData->orderValue);
        } else {
            $tdiEntity->setAmount(0);
        }

        if ($tradedublerData->timeOfEvent) {
            $tdiEntity->setTrackingDate($tradedublerData->timeOfEvent);
        } else {
            $tdiEntity->setTrackingDate(null);
        }

        if ($tradedublerData->lastModified) {
            $tdiEntity->setModifiedDate($tradedublerData->lastModified);
        } else {
            $tdiEntity->setModifiedDate(null);
        }

        if ($tradedublerData->time_of_visit) {
            $tdiEntity->setClickDate($tradedublerData->time_of_visit);
        } else {
            $tdiEntity->setClickDate(null);
        }

        if ($tradedublerData->programId) {
            $tdiEntity->setProgramId($tradedublerData->programId);
        } else {
            $tdiEntity->setProgramId(null);
        }

        if ($tradedublerData->programName) {
            $tdiEntity->setProgramName($tradedublerData->programName);
        } else {
            $tdiEntity->setProgramName(null);
        }

        if ($tradedublerData->open_product_feeds_name) {
            $tdiEntity->setProductName($tradedublerData->open_product_feeds_name);
        } else {
            $tdiEntity->setProductName(null);
        }

        if ($tradedublerData->leadNR) {
            $tdiEntity->setLeadNumber($tradedublerData->leadNR);
        } else {
            $tdiEntity->setLeadNumber(null);
        }

        if ($tradedublerData->orderNR) {
            $tdiEntity->setOrderNumber($tradedublerData->orderNR);
        } else {
            $tdiEntity->setOrderNumber(null);
        }

        if ($tradedublerData->epi1) {
            $tdiEntity->setParam0($tradedublerData->epi1, $tradedublerData->epi1);
        } else {
            $tdiEntity->setParam0(null);
        }

        if ($tradedublerData->epi2) {
            $tdiEntity->setParam1($tradedublerData->epi2, $tradedublerData->epi2);
        } else {
            $tdiEntity->setParam1(null);
        }

        $tdiEntity->setParam2('');

        $tdiEntity->setProcessed('PENDING');
        $tdiEntity->setProcessedDate(new \DateTime());
        $tdiEntity->setDaystoautoapprove(null);
          // EXTRA ADDED FIELDS ON 28032016
          // Changes from :: start              
          $em->persist($tdiEntity);
        $em->flush();
    }

    protected function getTditransactionNetwork()
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName(Network::TDI);
    }
    protected function getTditransactionCurrency($CurrencyCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Currency')
            ->findOneByCode($CurrencyCode);
    }
}

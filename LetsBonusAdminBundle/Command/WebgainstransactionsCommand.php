<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
// Entities
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Network;

require_once 'AppShell.php';

class WebgainstransactionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:webgains')->setDescription('Use to Store Sales Data to Database Of Commission Junction');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');
        $containerEmailObject = $this->getContainer()->get('mailer');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $webgains_obj = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findByName(Network::WEBGAINS);
        $webgains_id = '';
        foreach ($webgains_obj as $webgains) {
            $webgains_id = $webgains->getId();
        }
        if (!empty($webgains_id)):
            $networkAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:networkCredentials')->findByNetwork(trim($webgains_id));
        foreach ($networkAvailable as $credentials) {
            $wg_username = $credentials->getWebgainsUsername();
            $wg_password = $credentials->getWebgainsPassword();
            $wg_campaignid = $credentials->getWebgainsCampaignId();

            $containerEmailObject = $this->getContainer()->get('mailer');
            $sendEmail = new \AppShell();
            try {
                $WebgainsClient = new \SoapClient(
                    null,
                    array(
                        'location' => $this->getContainer()->getParameter('webgains_location'),
                        'uri' => $this->getContainer()->getParameter('webgains_uri'),
                        'style' => SOAP_RPC,
                        'use' => SOAP_ENCODED,
                        'exceptions' => 0,
                    )
                );
                $EndDate = date('Y-m-d H:i:s');
                $StartDate = Date('Y-m-d 00:00:00', strtotime('-90 days'));

                $this->getAlltransactions($WebgainsClient, $StartDate, $EndDate, $wg_username, $wg_password, $wg_campaignid);
                $this->updateTransactions($WebgainsClient, $StartDate, $EndDate, $wg_username, $wg_password, $wg_campaignid);
            } catch (Exception $c) {
                echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                $sendEmail->sendAdminAlert('Imposible crear SOAP Client <br>'.$e->getMessage(), 'Alerta transacciones de Webgains: Shell', $containerEmailObject, $admin_email_id);
            }
        } else:
            die('Network Entry Not Inserted');
        endif;
    }
    private function getAlltransactions($WebgainsClient, $StartDate, $EndDate, $wg_username, $wg_password, $wg_campaignid)
    {
        try {
            $earningsTransactions = $WebgainsClient->getFullEarningsWithEventName($StartDate, $EndDate, $wg_campaignid, $wg_username, $wg_password);
        } catch (Exception $e) {
            if (preg_match('/60 requests/', $e->getMessage())) {
                $earningsTransactions = $WebgainsClient->getFullEarningsWithEventName($StartDate, $EndDate, $wg_campaignid, $wg_username, $wg_password);
            }
        }

        $em = $this->getContainer()->get('doctrine')->getManager();

        if (!empty($earningsTransactions)):
            foreach ($earningsTransactions as $wgtransaction):
                if (!$this->alreadySaved($wgtransaction, $em)):
                    $webgainEntity = new LetsBonusTransactions();
                    $this->saveWGtransaction($wgtransaction, $webgainEntity, $em);
                else:
                    echo "Already saved\n";
                endif;
            endforeach;
            die();
        endif;
    }

    private function updateTransactions($WebgainsClient, $StartDate, $EndDate, $wg_username, $wg_password, $wg_campaignid)
    {
        try {
            $earningsUpdated = $WebgainsClient->getFullUpdatedEarnings($StartDate, $EndDate, $wg_campaignid, $wg_username, $wg_password);
        } catch (Exception $e) {
            if (preg_match('/60 requests/', $e->getMessage())) {
                sleep(60);
                $earningsUpdated = $WebgainsClient->getFullUpdatedEarnings($StartDate, $EndDate, $wg_campaignid, $wg_username, $wg_password);
            }
        }

        $em = $this->getContainer()->get('doctrine')->getManager();

        if (!empty($earningsUpdated)):
            foreach ($earningsUpdated as $wgtransaction):
                echo $count++;
                if (!$this->alreadySaved($wgtransaction, $em)):
                    $webgainEntity = new LetsBonusTransactions();
                    $this->saveWGtransaction($wgtransaction, $webgainEntity, $em);
                else:
                    echo "Already saved\n";
                endif;
            endforeach;
        endif;
    }

    private function alreadySaved($saleItem, $em)
    {
        $transactionId = (string) $saleItem->transactionID;
        if (!empty($saleItem->clickRef)):
            $params = explode(':', (string) $saleItem->clickRef);
        else:
            $params[0] = '';
            $params[1] = '';
            $params[2] = '';
        endif;

        $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')
                               ->findOneBy(
                                    array(
                                      'transactionId' => $transactionId,
                                      'status' => (string) $saleItem->status,
                                      'trackingDate' => (string) $saleItem->date,
                                      'programId' => (string) $saleItem->programID,
                                      'programName' => (string) $saleItem->programName,
                                      'clickDate' => (string) $saleItem->clickthroughTime,
                                      'param0' => $params[0],
                                      'param1' => $params[1],
                                      'param2' => $params[2],
                                    )
                               );

        if (!empty($transaction)):
            return true;
        endif;

        return false;
    }
    private function saveWGtransaction($saleItem, $webgainEntity, $em)
    {
        if ($saleItem->transactionID) {
            $webgainEntity->setTransactionId($saleItem->transactionID);
        } else {
            $webgainEntity->setTransactionId(null);
        }

        $referenceId = $saleItem->affiliateID.':'.$saleItem->campaignID;
        if ($referenceId) {
            $webgainEntity->setReferenceId($referenceId);
        } else {
            $webgainEntity->setReferenceId(null);
        }
        $webgainEntity->setNetwork($this->getWebgainsNetwork());
        $webgainEntity->setCurrency($this->getWebgainCurrency('EUR'));

        if ($saleItem->status) {
            $webgainEntity->setStatus((string) $saleItem->status);
        } else {
            $webgainEntity->setStatus(null);
        }

        $commission = round((float) $saleItem->commission, 2);
        if ($commission) {
            $webgainEntity->setCommission($commission);
        } else {
            $webgainEntity->setCommission(0);
        }

        $amount = round((float) $saleItem->saleValue, 2);
        if ($amount) {
            $webgainEntity->setAmount($amount);
        } else {
            $webgainEntity->setAmount(0);
        }

        if ($saleItem->date) {
            $webgainEntity->setTrackingDate($saleItem->date);
        } else {
            $webgainEntity->setTrackingDate(null);
        }

        if ($saleItem->clickthroughTime) {
            $webgainEntity->setClickDate((string) $saleItem->clickthroughTime);
        } else {
            $webgainEntity->setClickDate(null);
        }

        if ($saleItem->programID) {
            $webgainEntity->setProgramId($saleItem->programID);
        } else {
            $webgainEntity->setProgramId(null);
        }

        if ($saleItem->programName) {
            $webgainEntity->setProgramName($saleItem->programName);
        } else {
            $webgainEntity->setProgramName(null);
        }
        if ($saleItem->clickRef) {
            $params = explode(':', (string) $saleItem->clickRef);
            if ($params[0]) {
                $webgainEntity->setParam0($params[0]);
            } else {
                $webgainEntity->setParam0(0);
            }
            if ($params[1]) {
                $webgainEntity->setParam1($params[1]);
            } else {
                $webgainEntity->setParam1(0);
            }
            if ($params[2]) {
                $webgainEntity->setParam2($params[2]);
            } else {
                $webgainEntity->setParam2(0);
            }
        } else {
            $webgainEntity->setParam0(0);
            $webgainEntity->setParam1(0);
            $webgainEntity->setParam2(0);
        }
        if (!empty($params[2])) {
            $shopHistoryId = $this->getWebgainShopHistory($params[2]);
            if ($shopHistoryId) {
                $webgainEntity->setShopHistory($shopHistoryId);
            }
        } else {
            $webgainEntity->setShopHistory(null);
        }

        // Extra Added By Yogesh :: 21042016 
        $webgainEntity->setProductName('');
        $webgainEntity->setProcessed('PENDING');
        $webgainEntity->setProcessedDate(new \DateTime());
        // Extra Added By Yogesh :: 21042016 
        //$webgainEntity->setProcessedDate(new \DateTime());
        $em->persist($webgainEntity);
        $em->flush();
    }

    protected function getWebgainsNetwork()
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName(Network::WEBGAINS);
    }
    protected function getWebgainCurrency($CurrencyCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Currency')
            ->findOneByCode($CurrencyCode);
    }
    protected function getWebgainShopHistory($ShophistoryCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:shopHistory')
            ->findOneById($ShophistoryCode);
    }
}

<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\SimpleXMLElement;
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Network;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;
use Zanox;

require_once 'AppShell.php';

class ZanoxTransactionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:zanoxtransactions')->setDescription('Use to Store Sales Data to Database Of Zanox');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = \Zanox\ApiClient::factory();

        $em = $this->getContainer()->get('doctrine')->getManager();
        $zanox_obj = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findByName(Network::ZANOX);
        $zanox_id = '';
        foreach ($zanox_obj as $zanox) {
            $zanox_id = $zanox->getId();
        }
        if (!empty($zanox_id)):
            $networkAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:networkCredentials')->findByNetwork(trim($zanox_id));
        foreach ($networkAvailable as $credentials) {
            $connectId = $credentials->getZenoxConnectId();
            $secretKey = $credentials->getZenoxSecretKey();

            $client->setConnectId($connectId);
            $client->setSecretKey($secretKey);
            $today = new \DateTime();

            for ($i = 0; $i <= 90; ++$i) {
                if ($i) {
                    $today->modify('-1 days');
                }
                $day = $today->format('Y-m-d');

                $this->getAllSales($client, $day, $output);
                $this->getAllLeads($client, $day, $output);
            }
        } else:
            die('Network Entry Not Inserted');
        endif;
    }

    private function getAllSales($client, $day, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');

        $containerEmailObject = $this->getContainer()->get('mailer');
        $sendEmail = new \AppShell();

        $sales = $client->getSales($day, 'trackingDate', null, null, null, 0, 50);

        if ($sales->page || $sales->total >= 50 || $sales->items >= 50):
            $sendEmail->sendAdminAlert('LetbsBonus Cashback Proyect: Es posible que queden sales por procesar. Actualmente solo se procesa 1página y hay que modificar el script para aceptar paginación', 'Alerta transacciones de Zanox: Shell', $containerEmailObject, $admin_email_id);
        endif;

        if (!empty($sales->saleItems->saleItem)):
            foreach ($sales->saleItems->saleItem as $saleItem):
                if (!$this->alreadySaved($saleItem)):
                    $this->saveZanoxTransaction($saleItem, $output);
        endif;
        endforeach;
        endif;
    }

    private function getAllLeads($client, $day, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');

        $containerEmailObject = $this->getContainer()->get('mailer');
        $sendEmail = new \AppShell();

        $leads = $client->getLeads($day, 'trackingDate', null, null, null, 0, 50);
        /*$leads = new SimpleXMLElement($leads);*/

        if ($leads->page || $leads->total >= 50 || $leads->items >= 50):
            $sendEmail->sendAdminAlert('LetbsBonus Cashback Proyect: Es posible que queden leads por procesar. Actualmente solo se procesa 1página y hay que modificar el script para aceptar paginación', 'Alerta transacciones leads de Zanox: Shell', $containerEmailObject, $admin_email_id);
        endif;
        /* Send Email to Admin :: Remaining */
        if (!empty($leads->leadItems->leadItem)):
            foreach ($leads->leadItems->leadItem as $leadItem):
                if (!$this->alreadySaved($leadItem)):
                    $this->saveZanoxTransaction($leadItem, $output);
        endif;
        endforeach;
        endif;
    }

    private function alreadySaved($saleItem)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $transactionId = hash('md5', $saleItem->clickId.'-'.$saleItem->trackingDate);
        $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findByTransactionId(trim($transactionId));
        if (!empty($transaction)):
            return true;
        endif;

        return false;
    }

    private function saveZanoxTransaction($saleItem, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');
        $containerEmailObject = $this->getContainer()->get('mailer');
        /*$sendEmail = new \AppShell();   */

        $em = $this->getContainer()->get('doctrine')->getManager();
        $zanoxEntity = new LetsBonusTransactions();
        $transactionId = hash('md5', $saleItem->clickId.'-'.$saleItem->trackingDate); //TRANSACTION ID
        $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findByTransactionId(trim($transactionId));
        if (empty($alreadyAvailable)) {
            $param0 = '';
            $param1 = '';
            $param2 = '';
            if (!empty($saleItem->gpps->gpp)):

                foreach ($saleItem->gpps->gpp as $gpp):
                    if ($gpp->_):

                        $keyparam = str_replace('zpar', '', $gpp->id);

            if ($keyparam == 3):
                            $param2 = $gpp->_; // shopshistory_id
                        else:
                            if ($keyparam == 0) {
                                $param0 = $gpp->_;
                            }
            if ($keyparam == 1) {
                $param1 = $gpp->_;
            }
            endif;
            endif;
            endforeach;

            endif;
            $zanoxEntity->setTransactionId($transactionId);
            $referenceId = $saleItem->id;
            $zanoxEntity->setReferenceId($referenceId);
            $networkId = $this->getZanoxNetwork();// NETWORK ID
            $zanoxEntity->setNetwork($networkId);
            if ($saleItem->currency != 'EUR'):
                /*$sendEmail->sendAdminAlert('LetbsBonus Cashback: transacción con currency != EUR, #'.$transactionId,'Alerta saveZanoxTransaction: Shell', $containerEmailObject, $admin_email_id);*/
            endif;
            $currencyId = $this->getZanoxCurrency($saleItem->currency);
            $zanoxEntity->setCurrency($currencyId);

            //TO-DO :: Added to display records with confirmed & approved status
            /*$statusArray = array("confirmed","approved");
            if(in_array($saleItem->reviewState,$statusArray)) {
                echo "<pre>";
                print_r($saleItem);
            }*/

            $status = $saleItem->reviewState;
            $zanoxEntity->setStatus($status);
            $commission = $saleItem->commission;
            $zanoxEntity->setCommission($commission);

            $amount = (!empty($saleItem->amount)) ? $saleItem->amount : null; // AMOUNT
            if (is_null($amount)) {
                $zanoxEntity->setAmount(0);
            } else {
                $zanoxEntity->setAmount($amount);
            }
            $zanoxEntity->setTrackingDate($saleItem->trackingDate);
            $zanoxEntity->setModifiedDate($saleItem->modifiedDate);
            if (!empty($saleItem->clickDate)) {
                $zanoxEntity->setClickDate($saleItem->clickDate);
            } else {
                $zanoxEntity->setClickDate('');
            }
            $clickId = $saleItem->clickId;
            $zanoxEntity->setClickId($clickId);
            $clickInId = $saleItem->clickInId;
            $zanoxEntity->setClickInId($clickInId);
            /* UPDATED AND EXTRA SET DATA */

            $zanoxEntity->setStatusName('');
            $zanoxEntity->setStatusState('');
            $zanoxEntity->setStatusMessage('');
            $zanoxEntity->setLeadNumber('');
            $zanoxEntity->setDaystoautoapprove('');
            $zanoxEntity->setTrackingUrl('');
            $zanoxEntity->setProductName('');
            $zanoxEntity->setOrderNumber('');
            //$zanoxEntity->setOrderValue('');
           /* UPDATED AND EXTRA SET DATA */

            $programId = $saleItem->program->id;
            if ($programId) {
                $zanoxEntity->setProgramId($programId);
            } else {
                $zanoxEntity->setProgramId(null);
            }

            $programName = $saleItem->program->_;
            $zanoxEntity->setProgramName($programName);

            if(isset($param0) && !empty($param0) && $param0!=null && $param0 != ''){
                $zanoxEntity->setParam0($param0);
            }else{
                $zanoxEntity->setParam0(0);
            }
            if(isset($param1) && !empty($param1) && $param1!=null && $param1 != ''){
                $zanoxEntity->setParam1($param1);
            }else{
                $zanoxEntity->setParam1(0);
            }
            if(isset($param2) && !empty($param2) && $param2!=null && $param2 != ''){
                $zanoxEntity->setParam2($param2);
            }else{
                $zanoxEntity->setParam2(0);
            }

            $shopHistoryId = $this->getZenoxShopHistory($param2);
            if ($shopHistoryId) {
                $zanoxEntity->setShopHistory($shopHistoryId);
            } else {
                $zanoxEntity->setShopHistory(null);
            }
            $zanoxEntity->setProcessed('PENDING');
            $zanoxEntity->setProcessedDate(new \DateTime());

            $em->persist($zanoxEntity);
            $em->flush();
        }
    }

    protected function getZanoxNetwork()
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName(Network::ZANOX);
    }

    protected function getZanoxCurrency($CurrencyCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Currency')
            ->findOneByCode($CurrencyCode);
    }

    protected function getZenoxShopHistory($ShophistoryCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:shopHistory')
            ->findOneById($ShophistoryCode);
    }
}

<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Network;

require_once 'AppShell.php';

class CjnetworkCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:cj')->setDescription('Use to Store Sales Data to Database Of Commission Junction');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $cj_obj = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findByName(Network::CJ);
        $cj_id = '';
        foreach ($cj_obj as $cj) {
            $cj_id = $cj->getId();
        }
        if (!empty($cj_id)):

            $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:networkCredentials')->findByNetwork($cj_id);

        foreach ($alreadyAvailable as $credentials) {
            // $cj_key = trim($credentials->getCjkey());
            $cj_key = trim('00a76f7884f07488b595d0948b77f88355871eaaa496eb2b6a65773e2f53bc497afaae95680e9c2b2c240dad28f9104376853452d00ed10121ed7307077a9826d1/008423fb4f9f9db8cee67f88dc5140761627df51f9378a3de76850a5692082b6d1e50a1ce4a5496685719d6a059a2d211a3f939e50de8286de70feba7de1d31e61');
            $cj_url = trim($credentials->getCjurl());

            $containerEmailObject = $this->getContainer()->get('mailer');
            $sendEmail = new \AppShell();
                // define('CJ_KEY', $this->getContainer()->getParameter('cj_key'));

            $EndDate = date('Y-m-d');
            $StartDate = Date('Y-m-d', strtotime('-31 days'));

            $url = $cj_url."?date-type=event&start-date={$StartDate}&end-date={$EndDate}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: '.$cj_key));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'error:'.curl_error($ch);
            }
            $cjDataXML = simplexml_load_string($result);

            if ($cjDataXML !==  false) {
                // print_r($cjDataXML);

                $not_authenticate = (property_exists($cjDataXML, 'error-message')); // boolean false, as expected

                    if ($not_authenticate == 1) {
                        echo $cjDataXML['error-message'];
                        die();
                        // $sendEmail->sendAdminAlert('CJ transactions', "API response empty from $this->dateStart to $this->dateEnd", $containerEmailObject);
                    } else {
                        $count = 0;
                        foreach ($cjDataXML->commissions->commission as $cjtransaction):

                            if (!$this->alreadySaved($cjtransaction, $em)):

                                $cjEntity = new LetsBonusTransactions();
                        $this->saveCJtransaction($cjtransaction, $cjEntity, $em); else:
                                echo "Already saved\n";
                        endif;

                        endforeach;
                    }
            } else {
                $sendEmail->sendAdminAlert('CJ transactions ', "API response empty from $this->dateStart to $this->dateEnd", $containerEmailObject, $admin_email_id);
            }
        } else:
            die('Network Not Available');
        endif;
    }
    private function alreadySaved($saleItem, $em)
    {
        $param0 = $param1 = $param2 = null;
        $shophistoryId = 0;
        $saleItemArray = get_object_vars($saleItem);
        $transactionId = $saleItemArray['commission-id'];

        $SID = $saleItemArray['sid'];
        if (!empty($SID)):
            $params = explode('x', $SID);
        $param0 = $params[0];
        $param1 = $params[1];
        $param2 = $params[2];
        endif;

        $leadNumber = (trim($saleItemArray['action-type']) == 'lead' || trim($saleItemArray['action-type']) == 'advanced lead') ? $saleItemArray['action-tracker-id'] : null;

        $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findOneBy(
            array(
                'transactionId' => trim($transactionId),
                'status' => trim($saleItemArray['action-status']),
                'trackingDate' => trim($saleItemArray['event-date']),
                'orderNumber' => trim($saleItemArray['order-id']),
                'programId' => trim($saleItemArray['cid']),
                'programName' => trim($saleItemArray['advertiser-name']),
                'param0' => trim($param0),
                'param1' => trim($param1),
                'param2' => trim($param2),
            )
        );

        if (!empty($alreadyAvailable)):
            return true;
        endif;

        return false;
    }
    public function saveCJtransaction($saleItem, $cjEntity, $em)
    {
        $commisionData = (array) $saleItem;

        if ($commisionData['commission-id']) {
            $cjEntity->setTransactionId($commisionData['commission-id']);
        } else {
            $cjEntity->setTransactionId(null);
        }

        if ($commisionData['action-tracker-id']) {
            $cjEntity->setReferenceId($commisionData['action-tracker-id']);
        } else {
            $cjEntity->setReferenceId(null);
        }

        $cjEntity->setNetwork($this->getCjNetwork());
        $cjEntity->setCurrency($this->getCjCurrency('EUR'));
        $cjEntity->setStatus($commisionData['action-status']);

        $commission = round($commisionData['commission-amount'], 2);
        if ($commission) {
            $cjEntity->setCommission($commission);
        } else {
            $cjEntity->setCommission(0);
        }

        $amount = round($commisionData['sale-amount'], 2);
        if ($amount) {
            $cjEntity->setAmount($amount);
        } else {
            $cjEntity->setAmount(0);
        }

        $cjEntity->setTrackingDate($commisionData['event-date']);

        $cjEntity->setProgramId($commisionData['cid']);

        $cjEntity->setProgramName($commisionData['advertiser-name']);
        $cjEntity->setOrderNumber($commisionData['order-id']);

        $leadNumber = (trim($commisionData['action-type']) == 'lead' || trim($commisionData['action-type']) == 'advanced lead') ? $commisionData['action-tracker-id'] : null;
        $cjEntity->setLeadNumber($leadNumber);

        $parameters = $commisionData['sid'];
        if (!empty($parameters)):
            $paratersData = explode('x', $parameters); else:
            $paratersData[0] = '';
        $paratersData[1] = '';
        $paratersData[2] = '';
        endif;

        if ($paratersData[0]) {
            $cjEntity->setParam0($paratersData[0]);
        } else {
            $cjEntity->setParam0(0);
        }

        if ($paratersData[1]) {
            $cjEntity->setParam1($paratersData[1]);
        } else {
            $cjEntity->setParam1(0);
        }

        if ($paratersData[2]) {
            $cjEntity->setParam2($paratersData[2]);
            $cjEntity->setShopHistory($this->getCjShopHistory($paratersData[2]));
        } else {
            $cjEntity->setParam2(0);
        }

        $cjEntity->setProductName('');

        $cjEntity->setProcessed('PENDING');
        $cjEntity->setProcessedDate(new \DateTime());
        $em->persist($cjEntity);
        $em->flush();
    }

    protected function getCjNetwork()
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName(Network::CJ);
    }

    protected function getCjCurrency($CurrencyCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Currency')
            ->findOneByCode($CurrencyCode);
    }

    protected function getCjShopHistory($ShophistoryCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:shopHistory')
            ->findOneById($ShophistoryCode);
    }
}

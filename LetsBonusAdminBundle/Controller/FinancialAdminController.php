<?php

namespace iFlair\LetsBonusAdminBundle\Controller;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions;
use iFlair\LetsBonusAdminBundle\Entity\FrontUser;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Settings;
use Digitick\Sepa\GroupHeader;
use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\DomBuilder\DomBuilderFactory;
use Digitick\Sepa\TransferFile\CustomerCreditTransferFile;
use Digitick\Sepa\TransferInformation\CustomerCreditTransferInformation;

class FinancialAdminController extends CRUDController
{
    public function ListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions');
        $cashbackTransaction = $entities->findBy(
          array('type' => array(cashbackTransactions::TRANSACTION_TYPE_WITHDRAWAL, cashbackTransactions::TRANSACTION_TYPE_DRAFT), 'status' => cashbackTransactions::STATUS_TYPE_PENDING)
        );
        $check_for_same_user_id = array();
        $same_user_transaction_data = array();
        $user_data_key = 0;
        foreach ($cashbackTransaction as $key => $cashtansdata) {
            $user_id = $cashtansdata->getUserId();
            if (!in_array(trim($user_id), $check_for_same_user_id)) {
                $check_for_same_user_id[] = $user_id;
                $Userres = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findBy(array('id' => trim($cashtansdata->getUserId())));
                $Mainusername = '';
                foreach ($Userres as $ures) {
                    $Mainusername = trim($ures->getName());
                }

                if (!empty($Mainusername)) {
                    $cashtansdata->setCashbacktransactionsChilds($Mainusername);
                } else {
                    $cashtansdata->setCashbacktransactionsChilds('--');
                }

                //--------------Collect CURRENCY Data---------------
                $Currencyres = $em->getRepository('iFlairLetsBonusAdminBundle:Currency')->findOneBy(array('code' => trim($cashtansdata->getCurrency())));
                if (!empty($Currencyres)) {
                    $currency_code = $Currencyres->getCode();
                    $cashtansdata->setCurrency($currency_code);
                } else {
                    $cashtansdata->setCurrency('--');
                }

                $same_user_transaction_data[$user_data_key]['userid'] = $cashtansdata->getUserId();
                $same_user_transaction_data[$user_data_key]['id'] = $cashtansdata->getId();
                $same_user_transaction_data[$user_data_key]['shop_id'] = $cashtansdata->getShopId();
                $same_user_transaction_data[$user_data_key]['amount'] = $cashtansdata->getAmount();
                $Currencyres = $em->getRepository('iFlairLetsBonusAdminBundle:Currency')->findOneBy(array('code' => trim($cashtansdata->getCurrency())));
                if (!empty($Currencyres)) {
                    $same_user_transaction_data[$user_data_key]['currency'] = $currency_code = $Currencyres->getCode();
                } else {
                    $same_user_transaction_data[$user_data_key]['currency'] = '--';
                }
                // unset($cashbackTransaction[$key]);
                ++$user_data_key;
            } else {
                $same_user_transaction_data[$user_data_key]['userid'] = $cashtansdata->getUserId();
                $same_user_transaction_data[$user_data_key]['id'] = $cashtansdata->getId();
                $same_user_transaction_data[$user_data_key]['shop_id'] = $cashtansdata->getShopId();
                $same_user_transaction_data[$user_data_key]['amount'] = $cashtansdata->getAmount();
                $Currencyres = $em->getRepository('iFlairLetsBonusAdminBundle:Currency')->findOneBy(array('code' => trim($cashtansdata->getCurrency())));
                if (!empty($Currencyres)) {
                    $same_user_transaction_data[$user_data_key]['currency'] = $currency_code = $Currencyres->getCode();
                } else {
                    $same_user_transaction_data[$user_data_key]['currency'] = '--';
                }
                unset($cashbackTransaction[$key]);
                ++$user_data_key;
            }
        }

        $table_data = array();
        $user_ids = array();
        $amount__test = 0;
        $user_id_amount_total = array();
        foreach ($same_user_transaction_data as $user_same_t_data) {
            if (!in_array($user_same_t_data['userid'], $table_data)) {
                $table_data[] = $user_same_t_data['userid'];
                $user_ids[$user_same_t_data['userid']] = '<tr><td>'.$user_same_t_data['id'].'</td><td>'.$user_same_t_data['shop_id'].'</td><td>'.$user_same_t_data['amount'].'</td><td>'.$user_same_t_data['currency'].'</td></tr>';
                $user_id_amount_total[][$user_same_t_data['userid']] = $user_same_t_data['amount'];
            } else {
                $user_ids[$user_same_t_data['userid']] .= '<tr><td>'.$user_same_t_data['id'].'</td><td>'.$user_same_t_data['shop_id'].'</td><td>'.$user_same_t_data['amount'].'</td><td>'.$user_same_t_data['currency'].'</td></tr>';
                $user_id_amount_total[][$user_same_t_data['userid']] = $user_same_t_data['amount'];
            }
        }

        /* USER TOTAL TRANSACTION MAKE TOTAL PROCESS */
        $final_user_transacation_amount = array();
        array_walk_recursive($user_id_amount_total, function ($item, $key) use (&$final_user_transacation_amount) {
            $final_user_transacation_amount[$key] = isset($final_user_transacation_amount[$key]) ?  $item + $final_user_transacation_amount[$key] : $item;
        });

        $user_table_data = array();
        foreach ($user_ids as $k => $d) {
            $user_table_data[$k] = "<p><b>Transacciones relacionadas</b></p><table class='table'><thead><tr><th>Id</th><th>ShopId</th><th>total</th><th>moneda</th></tr></thead><tbody>".$d.'</tbody></table>';
        }

        foreach ($cashbackTransaction as $cbtrans) {
            foreach ($user_table_data as $ky => $vl) {
                if ($cbtrans->getUserId() == $ky) {
                    $cbtrans->setComments($vl);
                }
            }
        }

        // $final_user_transacation_amount

        foreach ($cashbackTransaction as $cashbackTransactionData) {
            $user_set_amount = 0;
            $user_set_amount = $final_user_transacation_amount[$cashbackTransactionData->getUserId()];
            $comments = $cashbackTransactionData->setAmount($user_set_amount);
            $user_set_amount = 0;
        }

        return $this->render('iFlairLetsBonusAdminBundle:Financial:financialListing.html.twig', array(
            'cashback' => $cashbackTransaction,
        ));
    }
    /**
     * @Route("/admin/financial/newlist/", name="newlist")
     */
    public function NewlistAction()
    {
        $request = $this->get('request');
        $content = $request->getContent();
        $res_data = explode('%5B', $content);
        $requestParams = $request->request;
        $formParams = $requestParams->get($res_data[0]);
        if (count($formParams) > 0) {
            try {
                $em = $this->getDoctrine()->getManager();
                $entities = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions');

                //Retrieved list of users
                $listOfUsers = $em->createQueryBuilder()
                ->select('DISTINCT ctu.userId')
                ->from('iFlairLetsBonusAdminBundle:cashbackTransactions',  'ctu')
                ->andWhere('ctu.id IN (:idlist)')
                ->setParameter('idlist', $formParams)
                ->groupBy('ctu.userId')
                ->getQuery()
                ->getResult();
                //$listOfUsers = implode(',', array_map(function($el){ return $el['userId']; }, $listOfUsers));
                $listOfUsers = array_map(function ($el) { return $el['userId']; }, $listOfUsers);

                //get Child transactions
                $childCashbackTransactions = $em->createQueryBuilder()
                ->select('ct.id')
                ->from('iFlairLetsBonusAdminBundle:cashbackTransactions',  'ct')
                ->where('ct.status = :status')
                ->setParameter('status', cashbackTransactions::STATUS_TYPE_PENDING)
                ->andWhere('ct.type IN (:type)')
                ->setParameter('type', array(cashbackTransactions::TRANSACTION_TYPE_WITHDRAWAL, cashbackTransactions::TRANSACTION_TYPE_DRAFT))
                ->andWhere('ct.id NOT IN (:idlist)')
                ->setParameter('idlist', $formParams)
                ->andWhere('ct.userId IN (:userlist)')
                ->setParameter('userlist', $listOfUsers)
                ->getQuery()
                ->getResult();
                $childCashbackTransactions = array_map(function ($el) { return $el['id']; }, $childCashbackTransactions);
                $formParams = array_merge($formParams, $childCashbackTransactions);

                $xml_filename = 'cashback_payment_'.date('Y-m-d').'.xml';
                header('Content-type: text/xml');
                header('Content-Disposition: attachment; filename="'.$xml_filename.'"');

                $sepaInitiatingPartyId = $this->container->getParameter('sepa_initiating_party_id');
                $sepaInitiatingPartyName = $this->container->getParameter('sepa_initiating_party_name');
                $sepaPaymentInformationId = $this->container->getParameter('sepa_payment_information_id');
                $sepaPaymentInformationIBAN = $this->container->getParameter('sepa_payment_information_iban');
                $sepaPaymentInformationBIC = $this->container->getParameter('sepa_payment_information_bic');
                $sepaInitiatingPartyStreetAddress = $this->container->getParameter('sepa_initiating_party_street_address');
                $sepaInitiatingPartyStreetAddressLine1 = $this->container->getParameter('sepa_initiating_party_street_address_line1');
                $sepaInitiatingPartyStreetAddressLine2 = $this->container->getParameter('sepa_initiating_party_street_address_line2');
                $sepaInitiatingPartyPostCode = $this->container->getParameter('sepa_initiating_party_post_code');
                $sepaInitiatingPartyCityName = $this->container->getParameter('sepa_initiating_party_city_name');
                $sepaInitiatingPartyCountryCode = $this->container->getParameter('sepa_initiating_party_country_code');
                $groupHeader = new GroupHeader($sepaInitiatingPartyId, 'Lets Bonus S.L Spain', array('AdrTp' => 'ADDR', 'PstCd' => '08006', 'TwnNm' => 'Barcelona', 'Ctry' => 'ES'));
                $groupHeader->setInitiatingPartyId($sepaInitiatingPartyId);
                $sepaFile = new CustomerCreditTransferFile($groupHeader);

                // Create a PaymentInformation the Transfer belongs to
                $payment = new PaymentInformation(
                    $sepaPaymentInformationId, //ID
                    $sepaPaymentInformationIBAN, // IBAN the money is transferred from
                    $sepaPaymentInformationBIC,  // BIC
                    $sepaInitiatingPartyName // Debitor Name
                );
                $payment->setCreditorId($sepaInitiatingPartyId);
                //TO-DO :: Confirm address record in finance file as no such method found
                /*$payment->setAddress(array(
                    'AdrTp' => $sepaInitiatingPartyStreetAddress,
                    'PstCd' => $sepaInitiatingPartyPostCode,
                    'TwnNm' => $sepaInitiatingPartyCityName,
                    'Ctry' => $sepaInitiatingPartyCountryCode,
                    'AdrLine' => $sepaInitiatingPartyStreetAddressLine1,
                    'AdrLine2' => $sepaInitiatingPartyStreetAddressLine2
                ));*/

                $cashbackTransactions = $entities->findBy(array(
                    'id' => $formParams,
                    'status' => cashbackTransactions::STATUS_TYPE_PENDING,
                ));

                if ($cashbackTransactions) {
                    foreach ($cashbackTransactions as $cashbackInfo) {
                        $userId = $cashbackInfo->getUserId();
                        $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findBy(array('id' => $userId));
                        if ($userRecord) {
                            $amount = $cashbackInfo->getAmount() * -1;
                            $iban = trim($cashbackInfo->getUserBankAccountNumber());
                            $bic = $cashbackInfo->getBic();
                            $userName = $cashbackInfo->getUserName();
                            $Ctry = 'ES'; //TO-DO :: Update country dynamically
                            $remittanceInfo = $userId.' '.$Ctry.' '.date('Y-m-d', strtotime($cashbackInfo->getDate()));
                            if ($iban && $bic && $userName) {
                                $transfer = new CustomerCreditTransferInformation(
                                    $amount,    // Amount
                                    $iban,      //IBAN of creditor
                                    $userName,  //ID/Name of Creditor						
                                    array(), //Address information
                                    $Ctry
                                );
                                $transfer->setBic($bic); // Set the BIC explicitly
                                //TO-DO :: Confirm country record in finance file as no such method found
                                //$transfer->setCountry($Ctry);
                                $transfer->setRemittanceInformation($remittanceInfo);
                                $payment->addTransfer($transfer);

                                $cashbackInfo->setStatus(cashbackTransactions::STATUS_TYPE_PAYED);
                                $cashbackInfo->setSepageneratedDate(new \DateTime(date('Y-m-d H:i:s')));
                                $cashbackInfo->setSepageneratedbyUserId($this->get('security.context')->getToken()->getUser()->getId());
                                $em->persist($cashbackInfo);
                            }
                        }
                    }
                    $settings = $this->checkIFSettingsExists(Settings::PREV_DOWNLOADED_SEPA_RECORDS);
                    if (!$settings) {
                        $settings = new Settings();
                    }
                    $settings->setCode(Settings::PREV_DOWNLOADED_SEPA_RECORDS);
                    $settings->setValue(implode(',', $formParams));
                    $settings->setStatus(Settings::YES);
                    $em->persist($settings);
                    $em->flush();
                }

                $sepaFile->addPaymentInformation($payment);
                // Or if you want to use the format 'pain.001.001.03' instead
                $domBuilder = DomBuilderFactory::createDomBuilder($sepaFile, 'pain.001.001.03');

                return new Response($domBuilder->asXml());
            } catch (\Exception $e) {
                $request->getSession()
                ->getFlashBag()
                ->add('error', $e->getMessage());

                return $this->redirect($this->generateUrl('i_flair_lets_bonus_financial_list'));
            }
        } else {
            $request->getSession()
                ->getFlashBag()
                ->add('error', 'There are no transactions available.');

            return $this->redirect($this->generateUrl('i_flair_lets_bonus_financial_list'));
        }
    }

    public function revertAction()
    {
        $request = $this->get('request');
        $settings = $this->checkIFSettingsExists(Settings::PREV_DOWNLOADED_SEPA_RECORDS);
        if ($settings) {
            $cashbackTransactions = $settings->getValue();
            $cashbackTransactions = explode(',', $cashbackTransactions);
            $em = $this->getDoctrine()->getManager();
            $revertCashbackTransactions = $em->createQueryBuilder()
                ->select('ct')
                ->from('iFlairLetsBonusAdminBundle:cashbackTransactions',  'ct')
                ->where('ct.status = :status')
                ->setParameter('status', cashbackTransactions::STATUS_TYPE_PAYED)
                ->andWhere('ct.type = :type')
                ->setParameter('type', cashbackTransactions::TRANSACTION_TYPE_WITHDRAWAL)
                ->andWhere('ct.id IN (:idlist)')
                ->setParameter('idlist', $cashbackTransactions)
                ->getQuery()
                ->getResult();
            if ($revertCashbackTransactions) {
                foreach ($revertCashbackTransactions as $cashbackInfo) {
                    $cashbackInfo->setStatus(cashbackTransactions::STATUS_TYPE_PENDING);
                    $cashbackInfo->setType(cashbackTransactions::TRANSACTION_TYPE_DRAFT);
                    $em->persist($cashbackInfo);
                }
                $em->flush();
            }
            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Previously downloaded transactions reverted back.');
        } else {
            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'There are no transactions found in the system.');
        }
        $resp = array();
        $resp['redirectUrl'] = $this->generateUrl('i_flair_lets_bonus_financial_list');

        return new Response(json_encode($resp));
    }

    protected function checkIFSettingsExists($code)
    {
        return $this->getDoctrine()
            ->getManager()
            ->getRepository('iFlairLetsBonusAdminBundle:Settings')
            ->findOneByCode(Settings::PREV_DOWNLOADED_SEPA_RECORDS);
    }
}

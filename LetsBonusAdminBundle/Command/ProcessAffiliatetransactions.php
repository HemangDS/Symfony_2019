<?php

/* INCLUDE COMMAND FOR USE SENDADMINALERT FUNCTION */
/* ENTITY */
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions;
use iFlair\LetsBonusAdminBundle\Entity\Network;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\TransactionalQueueMail;

require_once 'AppShell.php';

class ProcessAffiliatetransaction
{
    protected $ExceptionLeadShopId = array(384);

    protected $statusMap = array(
        'approved' => LetsBonusTransactions::STATUS_TYPE_APPROVED,
        'A' => LetsBonusTransactions::STATUS_TYPE_APPROVED,
        'confirmed' => LetsBonusTransactions::STATUS_TYPE_APPROVED,
        'closed' => LetsBonusTransactions::STATUS_TYPE_APPROVED,
        'Closed' => LetsBonusTransactions::STATUS_TYPE_APPROVED,
        'pending' => LetsBonusTransactions::STATUS_TYPE_PENDING,
        'P' => LetsBonusTransactions::STATUS_TYPE_PENDING,
        'open' => LetsBonusTransactions::STATUS_TYPE_PENDING,
        'new' => LetsBonusTransactions::STATUS_TYPE_PENDING,
        'New' => LetsBonusTransactions::STATUS_TYPE_PENDING,
        'locked' => LetsBonusTransactions::STATUS_TYPE_PENDING,
        'Locked' => LetsBonusTransactions::STATUS_TYPE_PENDING,
        'delayed' => LetsBonusTransactions::STATUS_TYPE_PENDING,
        'extended' => LetsBonusTransactions::STATUS_TYPE_PENDING,
        'rejected' => LetsBonusTransactions::STATUS_TYPE_CANCELLED,
        'cancelled' => LetsBonusTransactions::STATUS_TYPE_CANCELLED,
        'C' => LetsBonusTransactions::STATUS_TYPE_CANCELLED,
        'denied' => LetsBonusTransactions::STATUS_TYPE_CANCELLED,
        'D' => LetsBonusTransactions::STATUS_TYPE_CANCELLED,
        'payed' => LetsBonusTransactions::STATUS_TYPE_PAID,
    );

    /* FUNCTION FOR CHECKIN LEAD NUMBER IS AVAILABLE ON TRANASCTION */
    public function isLead($pendingTransaction)
    {
        if (!empty($pendingTransaction->getLeadNumber()) && in_array($pendingTransaction->getParam1(), $this->ExceptionLeadShopId)) {
            /* Affiliation is Containing Lead */
            return true;
        }

        return false;
    }

    /* TRANSACTION WILL BE PROCESSED FROM PENDING */
    public function changeAffiliateStatus($pendingTransaction, $em)
    {
        $pendingTransaction->setProcessed(LetsBonusTransactions::PROCESSED_TYPE_PROCESSED);
        $pendingTransaction->setProcessedDate(new \DateTime());
        $em->persist($pendingTransaction);
        //$em->flush();
    }

    public function checkifClienttransactionalreadyPayed($pendingTransaction, &$em)
    {
        $transaction = $pendingTransaction->getTransactionId();
        /*INFO :: Check if client transaction exists and was payed */
        $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('transactionId' => $transaction));

        if (!empty($clientTransaction)):
            if (trim(strtolower($clientTransaction->getStatus())) == cashbackTransactions::STATUS_TYPE_PAYED):
                if (trim(strtolower($pendingTransaction->getStatus())) == LetsBonusTransactions::STATUS_TYPE_APPROVED):
                    return false;
                endif;
            endif;
        endif;

        if (!empty($clientTransaction)):
            /*Client transaction exists and was payed: Send Error by email */
            return true;
        endif;

        return false;
    }

    public function getAffiliateStatus($affiliatetransaction)
    {
        if (!in_array(trim($affiliatetransaction->getStatus()), array_keys($this->statusMap))):
            /* "Error status Affiliate status unknown ".$affiliatetransaction->getStatus() */
            return -1;
        endif;
        $orderStatus = $this->statusMap[trim($affiliatetransaction->getStatus())];
        /* Get affiliatetransaction status  $data = $this->statusMap[trim($affiliatetransaction->getStatus())] */
        $orderStatus = strtolower($orderStatus);

        return $orderStatus;
    }

    public function checkifClienttransactionExist($affiliatetransaction, $em)
    {
        $transactionId = $affiliatetransaction->getTransactionId();

        $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('transactionId' => $transactionId));
        /*$clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('transactionId' => trim('1963834619')));*/
        if (!empty($clientTransaction)):
            return $clientTransaction->getId();
        endif;

        return false;
    }
    public function updateClienttransaction($affiliatetransaction, $em)
    {
        $networkslist = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findAll();
        /* Updating Client transaction */

        $transactionId = $affiliatetransaction->getTransactionId(); // 1964385540

        $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('transactionId' => trim($transactionId)));
        /*$clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')
                                ->findOneBy(array('transactionId' => trim('1964127723')));*/

        /* update Clienttransaction order_reference */
        if (!empty($clientTransaction)):
            $clientTransaction->setOrderReference($affiliatetransaction->getOrderNumber()); // lb_transaction order number
            $em->persist($clientTransaction);
            //$em->flush();

            $networks = array();
            $count = 0;
            foreach ($networkslist as $network) {
                $networks[$count]['id'] = $network->getId();
                $networks[$count]['name'] = $network->getName();
                ++$count;
            }

            $networkName = $networks[trim($affiliatetransaction->getNetwork())]['name'];

            /****** Special Case Ebay: Pueden llegar ajustes de una transacción (cantidad positiva o negativa)  *******/
            if (strtolower($networkName) == strtolower(Network::EBAY)):
                $this->UpdateClienttransactionEbay($affiliatetransaction, $em);
            else:
                $this->UpdateClienttransactionSub($affiliatetransaction, $em);
            endif;
        endif;
    }

    public function UpdateClienttransactionEbay($affiliatetransaction, $em)
    {
        /* DUMMY DEFINE */
        $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('transactionId' => $affiliatetransaction->getTransactionId()));
        /*$clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('transactionId' => '1964127723'));*/

        $networkAmount = $this->getAmountbyNetwork($affiliatetransaction, $em);
        $totalAffiliateAmount = $clientTransaction->getTotalAffiliateAmount() + $clientTransaction->getAmount();
        $amount = $clientTransaction->getAmount() + $networkAmount;
        $commission = $clientTransaction->getAffiliateAmount() + $affiliatetransaction->getCommission();

        $clientTransaction->setTotalAffiliateAmount($totalAffiliateAmount);
        $clientTransaction->setAmount($amount);
        $clientTransaction->setAffiliateAmount($commission);

        $em->persist($clientTransaction);
        //$em->flush();
    }

    public function UpdateClienttransactionSub($affiliatetransaction, $em)
    {
        $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('transactionId' => $affiliatetransaction->getTransactionId()));
        /*$clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('transactionId' => '1964127723'));*/

        if ($clientTransaction->getTotalAffiliateAmount() != $affiliatetransaction->getAmount()):

            $amount = $this->getAmountbyNetwork($affiliatetransaction, $em);

            $clientTransaction->setTotalAffiliateAmount($affiliatetransaction->getAmount());
            $clientTransaction->setAmount($amount);
            $clientTransaction->setAffiliateAmount($affiliatetransaction->getCommission());

            $em->persist($clientTransaction);
            //$em->flush();
        endif;
    }

    public function getAmountbyNetwork($affiliatetransaction, $em)
    {
        $amount = null;
        $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('transactionId' => trim($affiliatetransaction->getTransactionId())));
        /*$clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('transactionId' => trim('1964127723')));*/

        $networkslist = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findAll();
        $networks = array();
        foreach ($networkslist as $network):
            $networks[$network->getId()] = $network->getName();
        endforeach;

        if (strtolower($networks[$clientTransaction->getNetworkId()]) == strtolower(Network::ZANOX) ||
            strtolower($networks[$clientTransaction->getNetworkId()]) == strtolower(Network::WEBGAINS) ||
            strtolower($networks[$clientTransaction->getNetworkId()]) == strtolower(Network::CJ) ||
            strtolower($networks[$clientTransaction->getNetworkId()]) == strtolower(Network::EBAY)
            ):

            $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $affiliatetransaction->getShopHistory()));
            $commission = $affiliatetransaction->getCommission();
            if (!empty($shopHistory->getLetsBonusPercentage())):
                    $amount = round($commission - ($commission * ($shopHistory->getLetsBonusPercentage() / 100)), 2);
            endif;
        elseif (strtolower($networks[$clientTransaction->getNetworkId()]) == strtolower(Network::TRADEDOUBLER) ||strtolower($networks[$clientTransaction->getNetworkId()]) == strtolower(Network::TDI)):
            $trackingDate = new DateTime($affiliatetransaction->getTrackingDate());
            $connection = $em->getConnection();
           /* $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh LEFT JOIN lb_shop s ON s.id = sh.shop WHERE sh.startDate <= :startDate AND (sh.endDate IS NULL OR sh.endDate >= :endDate)');*/
            $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh LEFT JOIN lb_shop s ON s.id = sh.shop WHERE sh.startDate <= :startDate');
            $statement->bindValue('startDate', $trackingDate->format('Y-m-d H:i:s'));
            //$statement->bindValue('endDate', $trackingDate->format('Y-m-d H:i:s'));
            $statement->execute();
            $shopData = $statement->fetchAll();
            $shopHistoryfRecord = array();
            foreach ($shopData as $sd) {
                $shopHistoryfRecord = $sd;
            }
            $commission = $affiliatetransaction->getCommission();
            if (!empty($shopHistoryfRecord['letsBonusPercentage'])) {
                $amount = round($commission - ($commission * ($shopHistoryfRecord['letsBonusPercentage'] / 100)), 2);
            }
        endif;

        return $amount;
    }

    public function createClienttransaction($affiliatetransaction, $em, $containerEmailObject, $admin_email_id)
    {
        $networkslist = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findAll();

        $networks = array();
        foreach ($networkslist as $network):
            $networks[$network->getId()] = $network->getName();
        endforeach;
        if (strtolower($networks[trim($affiliatetransaction->getNetwork())]) == strtolower(Network::ZANOX)):
            $this->createClienttransactionZanox($affiliatetransaction, $em, $containerEmailObject, $admin_email_id);
        elseif (strtolower($networks[trim($affiliatetransaction->getNetwork())]) == strtolower(Network::TRADEDOUBLER)):
            $this->createClienttransactionTD($affiliatetransaction, $em, $containerEmailObject, $admin_email_id);
        elseif (strtolower($networks[trim($affiliatetransaction->getNetwork())]) == strtolower(Network::WEBGAINS)):
            $this->createClienttransactionWebgains($affiliatetransaction, $em, $containerEmailObject, $admin_email_id);
        elseif (strtolower($networks[trim($affiliatetransaction->getNetwork())]) == strtolower(Network::CJ)):
            $this->createClienttransactionCJ($affiliatetransaction, $em, $containerEmailObject, $admin_email_id);
        elseif (strtolower($networks[trim($affiliatetransaction->getNetwork())]) == strtolower(Network::TDI)):
            $this->createClienttransactionTDI($affiliatetransaction, $em, $containerEmailObject, $admin_email_id);
        elseif (strtolower($networks[trim($affiliatetransaction->getNetwork())]) == strtolower(Network::EBAY)):
            $this->createClienttransactionEbay($affiliatetransaction, $em, $containerEmailObject, $admin_email_id);
        elseif (strtolower($networks[trim($affiliatetransaction->getNetwork())]) == strtolower(Network::AMAZON)):
            $this->createClienttransactionAmazon($affiliatetransaction, $em, $containerEmailObject, $admin_email_id);
        elseif (strtolower($networks[trim($affiliatetransaction->getNetwork())]) == strtolower(Network::LINKSHARE)):
            $this->createClienttransactionLinkShare($affiliatetransaction, $em, $containerEmailObject, $admin_email_id);
        endif;
    }

    public function createClienttransactionZanox($affiliatetransaction, $em, $containerEmailObject, $admin_email_id)
    {
        if ($affiliatetransaction->getShopHistory() || $affiliatetransaction->getParam1()) {
            $sendEmail = new \AppShell();
            /* Creating Client transaction ZANOX */

            if($affiliatetransaction->getShopHistory()){
                $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => trim($affiliatetransaction->getShopHistory()->getId())));
            }else{
                $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $affiliatetransaction->getParam2()));
                //$trackingDate = new DateTime($affiliatetransaction->getTrackingDate());
                /*$trackingDate->format('Y-m-d H:i:s');*/
                /*$shShopId = $affiliatetransaction->getParam1();
                $connection = $em->getConnection();*/
                /*GET DATA FROM SHOPHISTORY AND SHOP WHERE SHOPHISTORY DATE IS IN BETWEEEN AND SHOP ID AND SHOPHISTORY SHOP ID RELATION IS SAME*/
               /* $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate AND (sh.endDate IS NULL OR sh.endDate >= :endDate) LIMIT 0,1');*/
                /*$statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate LIMIT 0,1');
                $statement->bindValue('shShopId', $shShopId);
                $statement->bindValue('startDate', $trackingDate->format('Y-m-d H:i:s'));*/
                //$statement->bindValue('endDate', $trackingDate->format('Y-m-d H:i:s'));
                /*$statement->execute();
                $shopHistory = $statement->fetch();*/
            }

            if (empty($shopHistory)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción Zanox Shophistory empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionZanox ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction ZANOX, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }
                return false;
            endif;

            $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => trim($affiliatetransaction->getParam0())));

            if (empty($userRecord)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción Zanox User record empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionZanox ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction ZANOX, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $shopRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($affiliatetransaction->getParam1())));
            if ($shopRecord && $userRecord) {
                $userId = $userRecord->getId();
                $shopId = $shopRecord->getId();
                $shopHistoryId = 'NULL';
                if (is_object($shopHistory)):
                    $shopHistoryId = $shopHistory->getId();
                elseif (!empty($shopHistory['id'])):
                    $shopHistoryId = $shopHistory['id'];
                endif;

                $affiliateStatus = $this->getAffiliateStatus($affiliatetransaction);
                $networkStatus = $affiliatetransaction->getStatus();
                $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_APPROVED;
                $affiliateCancelDate = date('Y-m-d H:i:s');
                $transactionalemailType = TransactionalQueueMail::PURCHASE_DONE;

                if ($affiliateStatus == LetsBonusTransactions::STATUS_TYPE_CANCELLED):
                    /* Creating Client transaction ZANOX cancelled */
                    $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_DENIED;
                    $affiliateCancelDate = date('Y-m-d H:i:s');
                    $transactionalemailType = TransactionalQueueMail::PURCHASE_DENIED;
                endif;

                $commission = $affiliatetransaction->getCommission();

                $amount = 0;
                if (is_object($shopHistory)) {
                    if ($letsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                        $amount = round($commission - ($commission * ($letsBonusPercentage / 100)), 2);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $amount = round($commission - ($commission * ($shopHistory['letsBonusPercentage'] / 100)), 2);
                }

                $zenoxEntity = new cashbackTransactions();
                $currency = new Currency();
                /*$currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')
                                        ->findById(trim($affiliatetransaction->getCurrency()));*/
                $currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findOneBy(array('id' => trim($affiliatetransaction->getCurrency()->getId())));

                $curCode = '';
                $curCode = $currencyCode->getCode();
                /*foreach ($currencyCode as $c) {
                    $curCode = $c->getCode();
                }*/

                $shopIdRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($shopId)));
                $zenoxEntity->setShopId($shopIdRecord);
                $zenoxEntity->setShopHistory($shopHistory);
                $zenoxEntity->setUserId($userId);
                $zenoxEntity->setTransactionId($affiliatetransaction->getTransactionId());

                $NetworkRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('id' => trim($affiliatetransaction->getNetwork()->getId())));
                $zenoxEntity->setNetworkId($NetworkRecord);

                $zenoxEntity->setAmount($amount);
                $zenoxEntity->setAffiliateAmount($affiliatetransaction->getCommission());
                $zenoxEntity->setTotalAffiliateAmount($affiliatetransaction->getAmount());

                $zenoxEntity->setLetsbonusPct(0);

                if (is_object($shopHistory)) {
                    if ($letsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                        $zenoxEntity->setLetsbonusPct($letsBonusPercentage);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $zenoxEntity->setLetsbonusPct((float) $shopHistory['letsBonusPercentage']);
                } else {
                    $zenoxEntity->setLetsbonusPct(0);
                }

                $zenoxEntity->setCurrency($currencyCode);
                $zenoxEntity->setStatus($clienttransactionStatus);
                $zenoxEntity->setType(cashbackTransactions::TRANSACTION_TYPE_ADDED);
                $zenoxEntity->setNetworkStatus($networkStatus);
                $zenoxEntity->setAffiliateCanceldate(new \DateTime($affiliateCancelDate));
                $trackingDate = new DateTime($affiliatetransaction->getTrackingDate());
                $trackingDate = $trackingDate->format('Y-m-d H:i:s');
                $zenoxEntity->setDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                $zenoxEntity->setExtraAmount(0);
                $zenoxEntity->setExtraPct(0);
                $zenoxEntity->setOrderReference(0);
                $zenoxEntity->setAffiliateAproveddate(new \DateTime(date('Y-m-d H:i:s')));
                $zenoxEntity->setAprovalDate(new \DateTime(date('Y-m-d H:i:s')));
                $zenoxEntity->setUserName('NULL');
                $zenoxEntity->setUserAddress('NULL');
                $zenoxEntity->setUserdni('NULL');
                $zenoxEntity->setUserPhone('NULL');
                $zenoxEntity->setUserBankAccountNumber('NULL');
                $zenoxEntity->setBic('NULL');
                $zenoxEntity->setCompanyId(null);
                $zenoxEntity->setCashbacktransactionsChilds('NULL');
                $zenoxEntity->setAdminuserId(1);
                $zenoxEntity->setManualNumdaystoapprove(0);
                $zenoxEntity->setComments('NULL');
                $zenoxEntity->setParentTransactionId(0);
                $zenoxEntity->setCashbacksettingId(null);
                $zenoxEntity->setSepageneratedbyUserId(0);
                $zenoxEntity->setSepageneratedDate(new \DateTime(date('Y-m-d H:i:s')));
                $zenoxEntity->setDeviceType('NULL');
                $zenoxEntity->setCreated($affiliatetransaction->getCreated());
                $zenoxEntity->setModified($affiliatetransaction->getModified());
                try {
                    $em->persist($zenoxEntity);
                    $em->flush();
                    $LastInsertedId = $zenoxEntity->getId();
                } catch (Exception $e) {
                    echo $e->getMessage();
                    // reset entity manager
                    if (!$em->isOpen()) {
                        $em = $em->create(
                            $em->getConnection(),
                            $em->getConfiguration()
                        );
                    }
                }
                $clientcashbacktransactionId = $LastInsertedId;

                // echo 'Last Created Tranasction Id : '.$clientcashbacktransactionId;
                if ($clientcashbacktransactionId) {
                    $this->createDobleTripleCashback($clientcashbacktransactionId, $em);
                    $TQueueMail = new TransactionalQueueMail();
                    //TO-DO :: Confirm shop record
                    $TQueueMail->setShop($this->getProcessAffiliateShop($shopId, $em));
                    $TQueueMail->setShopHistory($this->getProcessAffiliateShopHistory($shopHistoryId, $em));
                    $TQueueMail->setCashbacktransactionId($clientcashbacktransactionId);
                    $TQueueMail->setIdClient($userId);
                    $TQueueMail->setIsoCode('ES');
                    $TQueueMail->setMailType($transactionalemailType);
                    //$TQueueMail->setShopName($shopRecord->getBrand());
                    $TQueueMail->setShopName(' ');
                    $TQueueMail->setAmount($amount);
                    $TQueueMail->setTotal(0);
                    $TQueueMail->setCurrency($curCode); //Removed fixed EUR as ideally email should contain the transactional currency
                    $TQueueMail->setPurchaseDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                    $TQueueMail->setStatus(TransactionalQueueMail::STATUS_TYPE_CONFIRMED);
                    $TQueueMail->setSendedDate(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setModified(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($TQueueMail);
                    //$em->flush();				
                }
            }
        }

        return true;
    }

    public function createClienttransactionTD($affiliatetransaction, $em, $containerEmailObject, $admin_email_id)
    {
        if ($affiliatetransaction->getShopHistory() || $affiliatetransaction->getParam1()) {
            $sendEmail = new \AppShell();
            /*  Creating Client transaction TD  */
            $affiliateStatus = $this->getAffiliateStatus($affiliatetransaction);
            $networkStatus = $this->statusMap[$affiliatetransaction->getStatus()];

            $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_APPROVED;
            $affiliateCancelDate = null;
            $transactionalemailType = TransactionalQueueMail::PURCHASE_DONE;
            if ($affiliateStatus == LetsBonusTransactions::STATUS_TYPE_CANCELLED):
                /*  Creating Client transaction ZANOX cancelled  */
                $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_DENIED;
                $affiliateCancelDate = date('Y-m-d H:i:s');
                $transactionalemailType = TransactionalQueueMail::PURCHASE_DENIED;
            endif;
            $trackingDate = new DateTime($affiliatetransaction->getTrackingDate());
            $shShopId = $affiliatetransaction->getParam1();
            $connection = $em->getConnection();
            /*$statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate AND (sh.endDate IS NULL OR sh.endDate >= :endDate) LIMIT 0,1');*/
            $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate LIMIT 0,1');
            $statement->bindValue('shShopId', $shShopId);
            $statement->bindValue('startDate', $trackingDate->format('Y-m-d H:i:s'));
            //$statement->bindValue('endDate', $trackingDate->format('Y-m-d H:i:s'));
            $statement->execute();
            $shopHistory = $statement->fetch();
            /*echo "<pre>";
            print_r($shopHistory);
            die();*/

            if (empty($shopHistory)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción TD Shophistory empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionTD ', $containerEmailObject, $admin_email_id);
                    /* ERROR creating Client transaction TD, shophistory EMPTY */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => trim($affiliatetransaction->getParam0())));

            if (empty($userRecord)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción TD User record empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionTD ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction ZANOX, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $shopRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($affiliatetransaction->getParam1())));
            if ($shopRecord && $userRecord) {
                $userId = $userRecord->getId();
                $shopId = $shopRecord->getId();
                $shopHistoryId = 'NULL';
                if (is_object($shopHistory)):
                    $shopHistoryId = $shopHistory->getId(); elseif (!empty($shopHistory['id'])):
                    $shopHistoryId = $shopHistory['id'];
                endif;

                $commission = $affiliatetransaction->getCommission();

                $amount = 0;
                if (is_object($shopHistory)) {
                    if (!empty($shopHistory->getLetsBonusPercentage())) {
                        $amount = round($commission - ($commission * ($shopHistory->getLetsBonusPercentage() / 100)), 2);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $amount = round($commission - ($commission * ($shopHistory['letsBonusPercentage'] / 100)), 2);
                } else {
                    $amount = 0;
                }

                $tdEntity = new cashbackTransactions();
                $currency = new Currency();
                /*$currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findById(trim($affiliatetransaction->getCurrency()));*/

                $currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findOneBy(array('id' => trim($affiliatetransaction->getCurrency())));
                $curCode = '';
                $curCode = $currencyCode->getCode();
                /*foreach ($currencyCode as $c) {
                    $curCode = $c->getCode();
                }*/

                $shopIdRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($shopId)));
                $tdEntity->setShopId($shopIdRecord);

                if (is_object($shopHistory)):
                    $tdEntity->setShopHistory($shopHistory);
                else:
                    $shopHistory_obj = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => trim($shopHistory['id'])));
                    $tdEntity->setShopHistory($shopHistory_obj);
                endif;

                $tdEntity->setUserId($userId);
                $tdEntity->setTransactionId($affiliatetransaction->getTransactionId());

                $NetworkRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('id' => trim($affiliatetransaction->getNetwork()->getId())));
                $tdEntity->setNetworkId($NetworkRecord);
                $tdEntity->setAmount($amount);
                $tdEntity->setAffiliateAmount($affiliatetransaction->getCommission());
                $tdEntity->setTotalAffiliateAmount($affiliatetransaction->getAmount());

                if (is_object($shopHistory)) {
                    if ($letsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                        $tdEntity->setLetsbonusPct($letsBonusPercentage);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $tdEntity->setLetsbonusPct((float) $shopHistory['letsBonusPercentage']);
                } else {
                    $tdEntity->setLetsbonusPct(0);
                }

                $tdEntity->setCurrency($currencyCode);
                $tdEntity->setStatus($clienttransactionStatus);
                $tdEntity->setType(cashbackTransactions::TRANSACTION_TYPE_ADDED);
                $tdEntity->setNetworkStatus($networkStatus);
                $tdEntity->setAffiliateCanceldate(new \DateTime($affiliateCancelDate));
                $tdEntity->setDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                $tdEntity->setExtraAmount(0);
                $tdEntity->setExtraPct(0);
                $tdEntity->setOrderReference(0);
                $tdEntity->setAffiliateAproveddate(new \DateTime('0000-00-00 00:00:00'));
                $tdEntity->setAprovalDate(new \DateTime('0000-00-00 00:00:00'));
                $tdEntity->setUserName('NULL');
                $tdEntity->setUserAddress('NULL');
                $tdEntity->setUserdni('NULL');
                $tdEntity->setUserPhone('NULL');
                $tdEntity->setUserBankAccountNumber('NULL');
                $tdEntity->setBic('NULL');
                $tdEntity->setCompanyId(null);
                $tdEntity->setCashbacktransactionsChilds('NULL');
                $tdEntity->setAdminuserId(1);
                $tdEntity->setManualNumdaystoapprove(0);
                $tdEntity->setComments('NULL');
                $tdEntity->setParentTransactionId(0);
                $tdEntity->setCashbacksettingId(null);
                $tdEntity->setSepageneratedbyUserId(0);
                $tdEntity->setSepageneratedDate(new \DateTime('0000-00-00 00:00:00'));
                $tdEntity->setDeviceType('NULL');
                $tdEntity->setCreated($affiliatetransaction->getCreated());
                $tdEntity->setModified($affiliatetransaction->getModified());
                /* if(!$this->debugmode): */
                try {
                    $em->persist($tdEntity);
                    $em->flush();
                    $LastInsertedId = $tdEntity->getId();
                } catch (Exception $e) {
                    return false;
                }
                $clientcashbacktransactionId = $LastInsertedId;
                // echo 'td '.$LastInsertedId;
                if ($clientcashbacktransactionId) {

                    //----------------- Create double or triple Cashback based on Cashbacktransaction -----------------/
                    $this->createDobleTripleCashback($clientcashbacktransactionId, $em);
                    $TQueueMail = new TransactionalQueueMail();
                    //TO-DO :: Confirm shop record
                    $TQueueMail->setShop($this->getProcessAffiliateShop($shopId, $em));
                    $TQueueMail->setShopHistory($this->getProcessAffiliateShopHistory($shopHistoryId, $em));
                    $TQueueMail->setCashbacktransactionId($clientcashbacktransactionId);
                    $TQueueMail->setIdClient($userId);
                    $TQueueMail->setIsoCode('ES');
                    $TQueueMail->setMailType($transactionalemailType);
                    //$TQueueMail->setShopName($shopRecord->getBrand());
                    $TQueueMail->setShopName(' ');
                    $TQueueMail->setAmount($amount);
                    $TQueueMail->setTotal(0);
                    $TQueueMail->setCurrency($curCode); //Removed fixed EUR as ideally email should contain the transactional currency
                    $TQueueMail->setPurchaseDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                    $TQueueMail->setStatus(TransactionalQueueMail::STATUS_TYPE_CONFIRMED);
                    $TQueueMail->setSendedDate(new \DateTime('0000-00-00 00:00:00'));
                    $TQueueMail->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setModified(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($TQueueMail);
                    //$em->flush();					
                }
            }
        }

        return true;
    }

    public function createClienttransactionWebgains($affiliatetransaction, $em, $containerEmailObject, $admin_email_id)
    {
        if ($affiliatetransaction->getShopHistory() || $affiliatetransaction->getParam1()) {
            $sendEmail = new \AppShell();
            $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $affiliatetransaction->getShopHistory()));
            if (empty($shopHistory)):
                $trackingDate = new DateTime($affiliatetransaction->getTrackingDate());
                $shShopId = $affiliatetransaction->getParam1();
                $connection = $em->getConnection();
                /*$statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate AND (sh.endDate IS NULL OR sh.endDate >= :endDate) LIMIT 0,1');*/
                $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate LIMIT 0,1');
                $statement->bindValue('shShopId', $shShopId);
                $statement->bindValue('startDate', $trackingDate->format('Y-m-d H:i:s'));
                //$statement->bindValue('endDate', $trackingDate->format('Y-m-d H:i:s'));
                $statement->execute();
                $shopHistory = $statement->fetch();
            endif;

            if (empty($shopHistory)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción Webgains Shophistory Empty: '.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionWebgains', $containerEmailObject, $admin_email_id);
                    /* ERROR creating Client transaction Webgains, shophistory EMPTY */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => trim($affiliatetransaction->getParam0())));

            if (empty($userRecord)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción Webgains User record empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionWebgains ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction ZANOX, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $shopRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($affiliatetransaction->getParam1())));
            if ($shopRecord && $userRecord) {
                $userId = $userRecord->getId();
                $shopId = $shopRecord->getId();
                // $shopId = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findById($shop_id);
                // echo $shopId->getId();
                // die();

                $shopHistoryId = 'NULL';
                if (is_object($shopHistory)):
                    $shopHistoryId = $shopHistory->getId();
                elseif (!empty($shopHistory['id'])):
                    $shopHistoryId = $shopHistory['id'];
                endif;

                $affiliateStatus = $this->getAffiliateStatus($affiliatetransaction); //pending
                $networkStatus = $this->statusMap[$affiliatetransaction->getStatus()];
                $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_APPROVED;
                $affiliateCancelDate = '0000-00-00 00:00:00';
                $transactionalemailType = TransactionalQueueMail::PURCHASE_DONE;

                if ($affiliateStatus == LetsBonusTransactions::STATUS_TYPE_CANCELLED):
                    /*  Creating Client transaction WEBGAINS cancelled  */
                    $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_DENIED;
                    $affiliateCancelDate = date('Y-m-d H:i:s');
                    $transactionalemailType = TransactionalQueueMail::PURCHASE_DENIED;
                endif;

                $commission = $affiliatetransaction->getCommission();
                $amount = 0;
                if (is_object($shopHistory)) {
                    if ($letsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                        $amount = round($commission - ($commission * ($letsBonusPercentage / 100)), 2);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $amount = round($commission - ($commission * ($shopHistory['letsBonusPercentage'] / 100)), 2);
                } else {
                    $amount = 0;
                }

                $webgainsEntity = new cashbackTransactions();
                $currency = new Currency();

                $currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findOneBy(array('id' => trim($affiliatetransaction->getCurrency())));
                $curCode = '';
                $curCode = $currencyCode->getCode();

                $shopIdRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($shopId)));
                $webgainsEntity->setShopId($shopIdRecord);

                $webgainsEntity->setShopHistory($shopHistory);

                $webgainsEntity->setUserId($userId);
                $webgainsEntity->setTransactionId($affiliatetransaction->getTransactionId());

                $NetworkRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('id' => trim($affiliatetransaction->getNetwork()->getId())));
                $webgainsEntity->setNetworkId($NetworkRecord);
                $webgainsEntity->setAmount($amount);
                $webgainsEntity->setAffiliateAmount($affiliatetransaction->getCommission());
                $webgainsEntity->setTotalAffiliateAmount($affiliatetransaction->getAmount());

                $webgainsEntity->setLetsbonusPct(0);

                if (is_object($shopHistory)) {
                    if ($letsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                        $webgainsEntity->setLetsbonusPct($letsBonusPercentage);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $webgainsEntity->setLetsbonusPct((float) $shopHistory['letsBonusPercentage']);
                } else {
                    $webgainsEntity->setLetsbonusPct(0);
                }

                $webgainsEntity->setCurrency($currencyCode);
                $webgainsEntity->setStatus($clienttransactionStatus);
                $webgainsEntity->setType(cashbackTransactions::TRANSACTION_TYPE_ADDED);
                $webgainsEntity->setNetworkStatus($networkStatus);
                //$webgainsEntity->setAffiliateCanceldate(new \DateTime($affiliateCancelDate));
                $webgainsEntity->setAffiliateCanceldate(new \DateTime(date('Y-m-d H:i:s')));
                $webgainsEntity->setDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                $webgainsEntity->setExtraAmount(0);
                $webgainsEntity->setExtraPct(0);
                $webgainsEntity->setOrderReference(0);
                $webgainsEntity->setAffiliateAproveddate(new \DateTime(date('Y-m-d H:i:s')));
                $webgainsEntity->setAprovalDate(new \DateTime(date('Y-m-d H:i:s')));
                $webgainsEntity->setUserName('NULL');
                $webgainsEntity->setUserAddress('NULL');
                $webgainsEntity->setUserdni('NULL');
                $webgainsEntity->setUserPhone('NULL');
                $webgainsEntity->setUserBankAccountNumber('NULL');
                $webgainsEntity->setBic('NULL');
                $webgainsEntity->setCompanyId(null);
                $webgainsEntity->setCashbacktransactionsChilds('NULL');
                $webgainsEntity->setAdminuserId(1);
                $webgainsEntity->setManualNumdaystoapprove(0);
                $webgainsEntity->setComments('NULL');
                $webgainsEntity->setParentTransactionId(0);
                $webgainsEntity->setCashbacksettingId(null);
                $webgainsEntity->setSepageneratedbyUserId(0);
                $webgainsEntity->setSepageneratedDate(new \DateTime(date('Y-m-d H:i:s')));
                $webgainsEntity->setDeviceType('NULL');
                $webgainsEntity->setCreated($affiliatetransaction->getCreated());
                $webgainsEntity->setModified($affiliatetransaction->getModified());
                /*if(!$this->debugmode):*/
                // echo $webgainsEntity->getShopId(); die();

                try {
                    $em->persist($webgainsEntity);
                    $em->flush();
                    $LastInsertedId = $webgainsEntity->getId();
                    // die();
                } catch (Exception $e) {
                    echo $e->getMessage();
                    // reset entity manager
                    if (!$em->isOpen()) {
                        $em = $em->create(
                            $em->getConnection(),
                            $em->getConfiguration()
                        );
                    }
                }

                $clientcashbacktransactionId = $LastInsertedId;
                // echo 'webgain '.$LastInsertedId;
                if ($clientcashbacktransactionId) {
                    $this->createDobleTripleCashback($clientcashbacktransactionId, $em);
                    $TQueueMail = new TransactionalQueueMail();
                    //TO-DO :: Confirm shop record
                    $TQueueMail->setShop($this->getProcessAffiliateShop($shopId, $em));
                    $TQueueMail->setShopHistory($this->getProcessAffiliateShopHistory($shopHistoryId, $em));
                    $TQueueMail->setCashbacktransactionId($clientcashbacktransactionId);
                    $TQueueMail->setIdClient($userId);
                    $TQueueMail->setIsoCode('ES');
                    $TQueueMail->setMailType($transactionalemailType);
                    //$TQueueMail->setShopName($shopRecord->getBrand()); //TO-DO :: Check shop name
                    $TQueueMail->setShopName('');
                    $TQueueMail->setAmount($amount);
                    $TQueueMail->setTotal(0);
                    $TQueueMail->setCurrency($curCode); //Removed fixed EUR as ideally email should contain the transactional currency
                    $TQueueMail->setPurchaseDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                    $TQueueMail->setStatus(TransactionalQueueMail::STATUS_TYPE_CONFIRMED);
                    $TQueueMail->setSendedDate(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setModified(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($TQueueMail);
                    //$em->flush();					
                }
            }
        }

        return true;
    }

    public function createClienttransactionCJ($affiliatetransaction, $em, $containerEmailObject, $admin_email_id)
    {
        if ($affiliatetransaction->getShopHistory() || $affiliatetransaction->getParam1()) {
            $sendEmail = new \AppShell();
            $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $affiliatetransaction->getShopHistory()));
            if (empty($shopHistory)):
                $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $affiliatetransaction->getParam2()));
                /*$trackingDate = new DateTime($affiliatetransaction->getTrackingDate());
                $shShopId = $affiliatetransaction->getParam1();
                $connection = $em->getConnection();*/
                /* $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate AND (sh.endDate IS NULL OR sh.endDate >= :endDate) LIMIT 0,1');*/
                /*$statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate LIMIT 0,1');
                $statement->bindValue('shShopId', $shShopId);
                $statement->bindValue('startDate', $trackingDate->format('Y-m-d H:i:s'));*/
                //$statement->bindValue('endDate', $trackingDate->format('Y-m-d H:i:s'));
                /*$statement->execute();
                $shopHistory = $statement->fetch();*/
            endif;

            if (empty($shopHistory)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción CJ Shophistory empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionCJ ', $containerEmailObject, $admin_email_id);
                    /* ERROR creating Client transaction CJ, shophistory EMPTY */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => trim($affiliatetransaction->getParam0())));

            if (empty($userRecord)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción CJ User record empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionCJ ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction ZANOX, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $shopRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($affiliatetransaction->getParam1())));
            if ($shopRecord && $userRecord) {
                $userId = $userRecord->getId();
                $shopId = $shopRecord->getId();
                $shopHistoryId = 'NULL';
                if (is_object($shopHistory)):
                    $shopHistoryId = $shopHistory->getId(); 
                elseif (!empty($shopHistory['id'])):
                    $shopHistoryId = $shopHistory['id'];
                endif;

                $affiliateStatus = $this->getAffiliateStatus($affiliatetransaction); //pending
                $networkStatus = $this->statusMap[$affiliatetransaction->getStatus()];

                $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_APPROVED;
                $affiliateCancelDate = date('Y-m-d H:i:s');
                $transactionalemailType = TransactionalQueueMail::PURCHASE_DONE;
                if ($affiliateStatus == LetsBonusTransactions::STATUS_TYPE_CANCELLED):
                    /*  Creating Client transaction CJ cancelled  */
                    $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_DENIED;
                    $affiliateCancelDate = date('Y-m-d H:i:s');
                    $transactionalemailType = TransactionalQueueMail::PURCHASE_DENIED;
                endif;

                $commission = $affiliatetransaction->getCommission();

                $amount = 0;
                if (is_object($shopHistory)) {
                    if (!empty($shopHistory->getLetsBonusPercentage())) {
                        $amount = round($commission - ($commission * ($shopHistory->getLetsBonusPercentage() / 100)), 2);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $amount = round($commission - ($commission * ($shopHistory['letsBonusPercentage'] / 100)), 2);
                } else {
                    $amount = 0;
                }

                $cjEntity = new cashbackTransactions();
                $currency = new Currency();
                /*$currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findById(trim($affiliatetransaction->getCurrency()));*/
                $currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findOneBy(array('id' => trim($affiliatetransaction->getCurrency())));
                $curCode = '';
                $curCode = $currencyCode->getCode();
                /*foreach ($currencyCode as $c) {
                    $curCode = $c->getCode();
                }*/

                $shopIdRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($shopId)));
                $cjEntity->setShopId($shopIdRecord);
                $cjEntity->setShopHistory($shopHistory);
                $cjEntity->setUserId($userId);
                $cjEntity->setTransactionId($affiliatetransaction->getTransactionId());

                $NetworkRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('id' => trim($affiliatetransaction->getNetwork()->getId())));
                $cjEntity->setNetworkId($NetworkRecord);

                $cjEntity->setAmount($amount);
                $cjEntity->setAffiliateAmount($affiliatetransaction->getCommission());
                $cjEntity->setTotalAffiliateAmount($affiliatetransaction->getAmount());

                if (is_object($shopHistory)) {
                    if ($letsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                        $cjEntity->setLetsbonusPct($letsBonusPercentage);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $cjEntity->setLetsbonusPct((float) $shopHistory['letsBonusPercentage']);
                } else {
                    $cjEntity->setLetsbonusPct(0);
                }

                $cjEntity->setCurrency($currencyCode);
                $cjEntity->setStatus($clienttransactionStatus);
                $cjEntity->setType(cashbackTransactions::TRANSACTION_TYPE_ADDED);
                $cjEntity->setNetworkStatus($networkStatus);
                $cjEntity->setAffiliateCanceldate(new \DateTime($affiliateCancelDate));
                $cjEntity->setDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                $cjEntity->setExtraAmount(0);
                $cjEntity->setExtraPct(0);
                $cjEntity->setOrderReference(0);
                $cjEntity->setAffiliateAproveddate(new \DateTime(date('Y-m-d H:i:s')));
                $cjEntity->setAprovalDate(new \DateTime(date('Y-m-d H:i:s')));
                $cjEntity->setUserName('NULL');
                $cjEntity->setUserAddress('NULL');
                $cjEntity->setUserdni('NULL');
                $cjEntity->setUserPhone('NULL');
                $cjEntity->setUserBankAccountNumber('NULL');
                $cjEntity->setBic('NULL');
                $cjEntity->setCompanyId(null);
                $cjEntity->setCashbacktransactionsChilds('NULL');
                $cjEntity->setAdminuserId(1);
                $cjEntity->setManualNumdaystoapprove(0);
                $cjEntity->setComments('NULL');
                $cjEntity->setParentTransactionId(0);
                $cjEntity->setCashbacksettingId(null);
                $cjEntity->setSepageneratedbyUserId(0);
                $cjEntity->setSepageneratedDate(new \DateTime(date('Y-m-d H:i:s')));
                $cjEntity->setDeviceType('NULL');
                $cjEntity->setCreated($affiliatetransaction->getCreated());
                $cjEntity->setModified($affiliatetransaction->getModified());
                try {
                    $em->persist($cjEntity);
                    $em->flush();
                    $LastInsertedId = $cjEntity->getId();
                } catch (Exception $e) {
                    echo $e->getMessage();
                    // reset entity manager
                    if (!$em->isOpen()) {
                        $em = $em->create(
                            $em->getConnection(),
                            $em->getConfiguration()
                        );
                    }
                }
                $clientcashbacktransactionId = $LastInsertedId;
                // echo 'cj '.$LastInsertedId;
                if ($clientcashbacktransactionId) {
                    $this->createDobleTripleCashback($clientcashbacktransactionId, $em);
                    $TQueueMail = new TransactionalQueueMail();
                    //TO-DO :: Confirm shop record
                    $TQueueMail->setShop($this->getProcessAffiliateShop($shopId, $em));
                    $TQueueMail->setShopHistory($this->getProcessAffiliateShopHistory($shopHistoryId, $em));
                    $TQueueMail->setCashbacktransactionId($clientcashbacktransactionId);
                    $TQueueMail->setIdClient($userId);
                    $TQueueMail->setIsoCode('ES');
                    $TQueueMail->setMailType($transactionalemailType);
                    //$TQueueMail->setShopName($shopRecord->getBrand()); //TO-DO :: Check shop name
                    $TQueueMail->setShopName('');
                    $TQueueMail->setAmount($amount);
                    $TQueueMail->setTotal(0);
                    $TQueueMail->setCurrency($curCode); //Removed fixed EUR as ideally email should contain the transactional currency
                    $TQueueMail->setPurchaseDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                    $TQueueMail->setStatus(TransactionalQueueMail::STATUS_TYPE_CONFIRMED);
                    $TQueueMail->setSendedDate(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setModified(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($TQueueMail);
                    //$em->flush();				
                }
            }
        }

        return true;
    }

    public function createClienttransactionTDI($affiliatetransaction, $em, $containerEmailObject, $admin_email_id)
    {
        if ($affiliatetransaction->getShopHistory() || $affiliatetransaction->getParam1()) {
            $sendEmail = new \AppShell();
            $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $affiliatetransaction->getShopHistory()));
            if (empty($shopHistory)):
                $trackingDate = new DateTime($affiliatetransaction->getTrackingDate());
                $shShopId = $affiliatetransaction->getParam1();
                $connection = $em->getConnection();
               /* $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate AND (sh.endDate IS NULL OR sh.endDate >= :endDate) LIMIT 0,1');*/
                $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate LIMIT 0,1');
                $statement->bindValue('shShopId', $shShopId);
                $statement->bindValue('startDate', $trackingDate->format('Y-m-d H:i:s'));
                //$statement->bindValue('endDate', $trackingDate->format('Y-m-d H:i:s'));
                $statement->execute();
                $shopHistory = $statement->fetch();
            endif;

            if (empty($shopHistory)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción TDI Shophistory empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionTDI ', $containerEmailObject, $admin_email_id);
                    /* ERROR creating Client transaction TD, shophistory EMPTY */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => trim($affiliatetransaction->getParam0())));

            if (empty($userRecord)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción TDI User record empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionTDI ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction ZANOX, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $shopRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($affiliatetransaction->getParam1())));
            if ($shopRecord && $userRecord) {
                $userId = $userRecord->getId();
                $shopId = $shopRecord->getId();
                $shopHistoryId = 'NULL';
                if (is_object($shopHistory)):
                    $shopHistoryId = $shopHistory->getId();
                elseif (!empty($shopHistory['id'])):
                    $shopHistoryId = $shopHistory['id'];
                endif;

                /*  Creating Client transaction TDI  */
                $affiliateStatus = $this->getAffiliateStatus($affiliatetransaction);
                $networkStatus = $this->statusMap[$affiliatetransaction->getStatus()];

                $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_APPROVED;
                $affiliateCancelDate = null;
                $transactionalemailType = TransactionalQueueMail::PURCHASE_DONE;
                if ($affiliateStatus == LetsBonusTransactions::STATUS_TYPE_CANCELLED):
                    /* Creating Client transaction TDI cancelled */
                    $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_DENIED;
                    $affiliateCancelDate = date('Y-m-d H:i:s');
                    $transactionalemailType = TransactionalQueueMail::PURCHASE_DENIED;
                endif;

                $commission = $affiliatetransaction->getCommission();

                $amount = 0;
                if (is_object($shopHistory)) {
                    if (!empty($shopHistory->getLetsBonusPercentage())) {
                        $amount = round($commission - ($commission * ($shopHistory->getLetsBonusPercentage() / 100)), 2);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $amount = round($commission - ($commission * ($shopHistory['letsBonusPercentage'] / 100)), 2);
                } else {
                    $amount = 0;
                }

                $tdiEntity = new cashbackTransactions();
                $currency = new Currency();
                /*$currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findById(trim($affiliatetransaction->getCurrency()));
                
                foreach ($currencyCode as $c) {
                    $curCode = $c->getCode();
                }*/
                $currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findOneBy(array('id' => trim($affiliatetransaction->getCurrency())));
                $curCode = '';
                $curCode = $currencyCode->getCode();

                $shopIdRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($shopId)));
                $tdiEntity->setShopId($shopIdRecord);
                $tdiEntity->setShopHistory($shopHistory);
                $tdiEntity->setUserId($userId);
                $tdiEntity->setTransactionId($affiliatetransaction->getTransactionId());

                $NetworkRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('id' => trim($affiliatetransaction->getNetwork()->getId())));
                $tdiEntity->setNetworkId($NetworkRecord);

                $tdiEntity->setAmount($amount);
                $tdiEntity->setAffiliateAmount($affiliatetransaction->getCommission());
                $tdiEntity->setTotalAffiliateAmount($affiliatetransaction->getAmount());

                if (is_object($shopHistory)) {
                    if ($letsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                        $tdiEntity->setLetsbonusPct($letsBonusPercentage);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $tdiEntity->setLetsbonusPct((float) $shopHistory['letsBonusPercentage']);
                } else {
                    $tdiEntity->setLetsbonusPct(0);
                }

                $tdiEntity->setCurrency($currencyCode);
                $tdiEntity->setStatus($clienttransactionStatus);
                $tdiEntity->setType(cashbackTransactions::TRANSACTION_TYPE_ADDED);
                $tdiEntity->setNetworkStatus($networkStatus);
                $tdiEntity->setAffiliateCanceldate(new \DateTime($affiliateCancelDate));
                $tdiEntity->setDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                $tdiEntity->setExtraAmount(0);
                $tdiEntity->setExtraPct(0);
                $tdiEntity->setOrderReference(0);
                $tdiEntity->setAffiliateAproveddate(new \DateTime('0000-00-00 00:00:00'));
                $tdiEntity->setAprovalDate(new \DateTime('0000-00-00 00:00:00'));
                $tdiEntity->setUserName('NULL');
                $tdiEntity->setUserAddress('NULL');
                $tdiEntity->setUserdni('NULL');
                $tdiEntity->setUserPhone('NULL');
                $tdiEntity->setUserBankAccountNumber('NULL');
                $tdiEntity->setBic('NULL');
                $tdiEntity->setCompanyId(null);
                $tdiEntity->setCashbacktransactionsChilds('NULL');
                $tdiEntity->setAdminuserId(1);
                $tdiEntity->setManualNumdaystoapprove(0);
                $tdiEntity->setComments('NULL');
                $tdiEntity->setParentTransactionId(0);
                $tdiEntity->setCashbacksettingId(null);
                $tdiEntity->setSepageneratedbyUserId(0);
                $tdiEntity->setSepageneratedDate(new \DateTime('0000-00-00 00:00:00'));
                $tdiEntity->setDeviceType('NULL');
                $tdiEntity->setCreated($affiliatetransaction->getCreated());
                $tdiEntity->setModified($affiliatetransaction->getModified());
                /*if(!$this->debugmode):*/
                try {
                    $em->persist($tdiEntity);
                    $em->flush();
                    $LastInsertedId = $tdiEntity->getId();
                } catch (Exception $e) {
                    return false;
                }
                $clientcashbacktransactionId = $LastInsertedId;
                // echo 'tdi '.$LastInsertedId;
                if ($clientcashbacktransactionId) {
                    /***************** Create double or triple Cashback based on Cashbacktransaction **********************/
                    $this->createDobleTripleCashback($clientcashbacktransactionId, $em);
                    $TQueueMail = new TransactionalQueueMail();
                    //TO-DO :: Confirm shop record
                    $TQueueMail->setShop($this->getProcessAffiliateShop($shopId, $em));
                    $TQueueMail->setShopHistory($this->getProcessAffiliateShopHistory($shopHistoryId, $em));
                    $TQueueMail->setCashbacktransactionId($clientcashbacktransactionId);
                    $TQueueMail->setIdClient($userId);
                    $TQueueMail->setIsoCode('ES');
                    $TQueueMail->setMailType($transactionalemailType);
                    //$TQueueMail->setShopName($shopRecord->getBrand());
                    $TQueueMail->setShopName(' ');
                    $TQueueMail->setAmount($amount);
                    $TQueueMail->setTotal(0);
                    $TQueueMail->setCurrency($curCode); //Removed fixed EUR as ideally email should contain the transactional currency
                    $TQueueMail->setPurchaseDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                    $TQueueMail->setStatus(TransactionalQueueMail::STATUS_TYPE_CONFIRMED);
                    $TQueueMail->setSendedDate(new \DateTime('0000-00-00 00:00:00'));
                    $TQueueMail->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setModified(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($TQueueMail);
                    //$em->flush();					
                }
            }
        }

        return true;
    }

    public function createClienttransactionEbay($affiliatetransaction, $em, $containerEmailObject, $admin_email_id)
    {
        if ($affiliatetransaction->getShopHistory() || $affiliatetransaction->getParam1()) {
            $sendEmail = new \AppShell();
            $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $affiliatetransaction->getShopHistory()));
            if (empty($shopHistory)):
                $trackingDate = new DateTime($affiliatetransaction->getTrackingDate());
                $shShopId = $affiliatetransaction->getParam1();
                $connection = $em->getConnection();
                /*$statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate AND (sh.endDate IS NULL OR sh.endDate >= :endDate) LIMIT 0,1');*/
                $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate LIMIT 0,1');
                $statement->bindValue('shShopId', $shShopId);
                $statement->bindValue('startDate', $trackingDate->format('Y-m-d H:i:s'));
               // $statement->bindValue('endDate', $trackingDate->format('Y-m-d H:i:s'));
                $statement->execute();
                $shopHistory = $statement->fetch();
            endif;

            if (empty($shopHistory)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción eBay Shophistory empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createEbayClienttransactionEbay ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction Ebay, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => trim($affiliatetransaction->getParam0())));

            if (empty($userRecord)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción eBay User record empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createEbayClienttransactionEbay ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction ZANOX, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $shopRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($affiliatetransaction->getParam1())));
            if ($shopRecord && $userRecord) {
                $userId = $userRecord->getId();
                $shopId = $shopRecord->getId();
                $shopHistoryId = 'NULL';
                if (is_object($shopHistory)):
                    $shopHistoryId = $shopHistory->getId(); elseif (!empty($shopHistory['id'])):
                    $shopHistoryId = $shopHistory['id'];
                endif;

                /*  Creating Client transaction eBay  */
                $affiliateStatus = $this->getAffiliateStatus($affiliatetransaction);
                #$networkStatus = $this->statusMap[$affiliatetransaction->getStatus()]; //Do not need network status as it is approved in eBay

                $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_APPROVED;
                $affiliateCancelDate = null;
                $transactionalemailType = TransactionalQueueMail::PURCHASE_DONE;
                if ($affiliateStatus == LetsBonusTransactions::STATUS_TYPE_CANCELLED):
                    /* Creating Client transaction eBay cancelled */
                    $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_DENIED;
                    $affiliateCancelDate = date('Y-m-d H:i:s');
                    $transactionalemailType = TransactionalQueueMail::PURCHASE_DENIED;
                endif;

                if ($affiliateCancelDate == null) {
                    $affiliateCancelDate = '0000-00-00 00:00:00';
                }

                $commission = $affiliatetransaction->getCommission();
                $amount = 0;
                if (is_object($shopHistory)) {
                    if (!empty($shopHistory->getLetsBonusPercentage())) {
                        $amount = round($commission - ($commission * ($shopHistory->getLetsBonusPercentage() / 100)), 2);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $amount = round($commission - ($commission * ($shopHistory['letsBonusPercentage'] / 100)), 2);
                } else {
                    $amount = 0;
                }

                $ebayEntity = new cashbackTransactions();
                $currency = new Currency();
                $currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findOneBy(array('id' => trim($affiliatetransaction->getCurrency())));
                /*$currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findById(trim($affiliatetransaction->getCurrency()));*/
                $curCode = '';
                $curCode = $currencyCode->getCode();

                $shopIdRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($shopId)));
                $ebayEntity->setShopId($shopIdRecord);
                $ebayEntity->setShopHistory($shopHistory);
                $ebayEntity->setUserId($userId);
                $ebayEntity->setTransactionId($affiliatetransaction->getTransactionId());

                $NetworkRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('id' => trim($affiliatetransaction->getNetwork()->getId())));
                $ebayEntity->setNetworkId($NetworkRecord);
                $ebayEntity->setAmount($amount);
                $ebayEntity->setAffiliateAmount($affiliatetransaction->getCommission());
                $ebayEntity->setTotalAffiliateAmount($affiliatetransaction->getAmount());

                if (is_object($shopHistory)) {
                    if ($letsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                        $ebayEntity->setLetsbonusPct($letsBonusPercentage);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $ebayEntity->setLetsbonusPct((float) $shopHistory['letsBonusPercentage']);
                } else {
                    $ebayEntity->setLetsbonusPct(0);
                }

                $ebayEntity->setCurrency($currencyCode);
                $ebayEntity->setStatus($clienttransactionStatus);
                $ebayEntity->setType(cashbackTransactions::TRANSACTION_TYPE_ADDED);
                $ebayEntity->setNetworkStatus(cashbackTransactions::NETWORK_STATUS_APPROVED);
                $ebayEntity->setAffiliateCanceldate(new \DateTime($affiliateCancelDate));
                $ebayEntity->setDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                $ebayEntity->setExtraAmount(0);
                $ebayEntity->setExtraPct(0);
                $ebayEntity->setOrderReference(0);
                $ebayEntity->setAffiliateAproveddate(new \DateTime('0000-00-00 00:00:00'));
                $ebayEntity->setAprovalDate(new \DateTime('0000-00-00 00:00:00'));
                $ebayEntity->setUserName('NULL');
                $ebayEntity->setUserAddress('NULL');
                $ebayEntity->setUserdni('NULL');
                $ebayEntity->setUserPhone('NULL');
                $ebayEntity->setUserBankAccountNumber('NULL');
                $ebayEntity->setBic('NULL');
                $ebayEntity->setCompanyId(null);
                $ebayEntity->setCashbacktransactionsChilds('NULL');
                $ebayEntity->setAdminuserId(1);
                $ebayEntity->setManualNumdaystoapprove(0);
                $ebayEntity->setComments('NULL');
                $ebayEntity->setParentTransactionId(0);
                $ebayEntity->setCashbacksettingId(null);
                $ebayEntity->setSepageneratedbyUserId(0);
                $ebayEntity->setSepageneratedDate(new \DateTime('0000-00-00 00:00:00'));
                $ebayEntity->setDeviceType('NULL');
                $ebayEntity->setCreated($affiliatetransaction->getCreated());
                $ebayEntity->setModified($affiliatetransaction->getModified());
                /*if(!$this->debugmode):*/
                try {
                    $em->persist($ebayEntity);
                    $em->flush();
                    $LastInsertedId = $ebayEntity->getId();
                } catch (Exception $e) {
                    return false;
                }
                /***************** PENDIG is also Approved Transaction on trackingDate + period  **********/
                $clientcashbacktransactionId = $LastInsertedId;
                // echo 'ebay '.$LastInsertedId;
                if ($clientcashbacktransactionId) {
                    $ebayEntity->setAffiliateAproveddate(new \DateTime($affiliatetransaction->getTrackingDate()));
                    $em->persist($ebayEntity);
                    //$em->flush();
                    /***************** Create double or triple Cashback based on Cashbacktransaction **********************/
                    $this->createDobleTripleCashback($clientcashbacktransactionId, $em);
                    $TQueueMail = new TransactionalQueueMail();
                    //TO-DO :: Confirm shop record
                    $TQueueMail->setShop($this->getProcessAffiliateShop($shopId, $em));
                    $TQueueMail->setShopHistory($this->getProcessAffiliateShopHistory($shopHistoryId, $em));
                    $TQueueMail->setCashbacktransactionId($clientcashbacktransactionId);
                    $TQueueMail->setIdClient($userId);
                    $TQueueMail->setIsoCode('ES');
                    $TQueueMail->setMailType($transactionalemailType);
                    //$TQueueMail->setShopName($shopRecord->getBrand()); // TO-DO :: Check shop name
                    $TQueueMail->setShopName('');
                    $TQueueMail->setAmount($amount);
                    $TQueueMail->setTotal(0);
                    $TQueueMail->setCurrency($curCode); //Removed fixed EUR as ideally email should contain the transactional currency
                    $TQueueMail->setPurchaseDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                    $TQueueMail->setStatus(TransactionalQueueMail::STATUS_TYPE_CONFIRMED);
                    $TQueueMail->setSendedDate(new \DateTime('0000-00-00 00:00:00'));
                    $TQueueMail->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setModified(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($TQueueMail);
                    //$em->flush();				
                }
            }
        }

        return true;
    }

    public function createClienttransactionAmazon($affiliatetransaction, $em, $containerEmailObject, $admin_email_id)
    {
        if ($affiliatetransaction->getShopHistory() || $affiliatetransaction->getParam1()) {
            $sendEmail = new \AppShell();
            $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $affiliatetransaction->getShopHistory()));
            if (empty($shopHistory)):
                $trackingDate = new DateTime($affiliatetransaction->getTrackingDate());
                $shShopId = $affiliatetransaction->getParam1();
                $connection = $em->getConnection();
               /* $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate AND (sh.endDate IS NULL OR sh.endDate >= :endDate) LIMIT 0,1');*/
                $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate LIMIT 0,1');
                $statement->bindValue('shShopId', $shShopId);
                $statement->bindValue('startDate', $trackingDate->format('Y-m-d H:i:s'));
               // $statement->bindValue('endDate', $trackingDate->format('Y-m-d H:i:s'));
                $statement->execute();
                $shopHistory = $statement->fetch();
            endif;

            if (empty($shopHistory)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción Amazon Shophistory empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createEbayClienttransactionAmazon ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction Ebay, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => trim($affiliatetransaction->getParam0())));

            if (empty($userRecord)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción Amazon User record empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createEbayClienttransactionAmazon ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction ZANOX, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $shopRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($affiliatetransaction->getParam1())));
            if ($shopRecord && $userRecord) {
                $userId = $userRecord->getId();
                $shopId = $shopRecord->getId();
                $shopHistoryId = 'NULL';
                if (is_object($shopHistory)):
                    $shopHistoryId = $shopHistory->getId();
                elseif (!empty($shopHistory['id'])):
                    $shopHistoryId = $shopHistory['id'];
                endif;

                /*  Creating Client transaction Amazon  */
                $affiliateStatus = $this->getAffiliateStatus($affiliatetransaction);
                #$networkStatus = $this->statusMap[$affiliatetransaction->getStatus()]; //Do not need network status as it is approved in Amazon

                $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_APPROVED;
                $affiliateCancelDate = null;
                $transactionalemailType = TransactionalQueueMail::PURCHASE_DONE;
                if ($affiliateStatus == LetsBonusTransactions::STATUS_TYPE_CANCELLED):
                    /* Creating Client transaction Amazon cancelled */
                    $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_DENIED;
                    $affiliateCancelDate = date('Y-m-d H:i:s');
                    $transactionalemailType = TransactionalQueueMail::PURCHASE_DENIED;
                endif;

                if ($affiliateCancelDate == null) {
                    $affiliateCancelDate = '0000-00-00 00:00:00';
                }

                $commission = $affiliatetransaction->getCommission();
                $amount = 0;
                if (is_object($shopHistory)) {
                    if (!empty($shopHistory->getLetsBonusPercentage())) {
                        $amount = round($commission - ($commission * ($shopHistory->getLetsBonusPercentage() / 100)), 2);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $amount = round($commission - ($commission * ($shopHistory['letsBonusPercentage'] / 100)), 2);
                } else {
                    $amount = 0;
                }

                $amazonEntity = new cashbackTransactions();
                $currency = new Currency();
                /*$currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findById(trim($affiliatetransaction->getCurrency()));
                */
                $currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findOneBy(array('id' => trim($affiliatetransaction->getCurrency())));
                $curCode = '';
                $curCode = $currencyCode->getCode();

                $shopIdRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($shopId)));
                $amazonEntity->setShopId($shopIdRecord);
                $amazonEntity->setShopHistory($shopHistory);
                $amazonEntity->setUserId($userId);
                $amazonEntity->setTransactionId($affiliatetransaction->getTransactionId());

                $NetworkRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('id' => trim($affiliatetransaction->getNetwork()->getId())));
                $amazonEntity->setNetworkId($NetworkRecord);
                $amazonEntity->setAmount($amount);
                $amazonEntity->setAffiliateAmount($affiliatetransaction->getCommission());
                $amazonEntity->setTotalAffiliateAmount($affiliatetransaction->getAmount());

                if (is_object($shopHistory)) {
                    if ($letsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                        $amazonEntity->setLetsbonusPct($letsBonusPercentage);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $amazonEntity->setLetsbonusPct((float) $shopHistory['letsBonusPercentage']);
                } else {
                    $amazonEntity->setLetsbonusPct(0);
                }

                $amazonEntity->setCurrency($currencyCode);
                $amazonEntity->setStatus($clienttransactionStatus);
                $amazonEntity->setType(cashbackTransactions::TRANSACTION_TYPE_ADDED);
                $amazonEntity->setNetworkStatus(cashbackTransactions::NETWORK_STATUS_APPROVED);
                $amazonEntity->setAffiliateCanceldate(new \DateTime($affiliateCancelDate));
                $amazonEntity->setDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                $amazonEntity->setExtraAmount(0);
                $amazonEntity->setExtraPct(0);
                $amazonEntity->setOrderReference(0);
                $amazonEntity->setAffiliateAproveddate(new \DateTime('0000-00-00 00:00:00'));
                $amazonEntity->setAprovalDate(new \DateTime('0000-00-00 00:00:00'));
                $amazonEntity->setUserName('NULL');
                $amazonEntity->setUserAddress('NULL');
                $amazonEntity->setUserdni('NULL');
                $amazonEntity->setUserPhone('NULL');
                $amazonEntity->setUserBankAccountNumber('NULL');
                $amazonEntity->setBic('NULL');
                $amazonEntity->setCompanyId(null);
                $amazonEntity->setCashbacktransactionsChilds('NULL');
                $amazonEntity->setAdminuserId(1);
                $amazonEntity->setManualNumdaystoapprove(0);
                $amazonEntity->setComments('NULL');
                $amazonEntity->setParentTransactionId(0);
                $amazonEntity->setCashbacksettingId(null);
                $amazonEntity->setSepageneratedbyUserId(0);
                $amazonEntity->setSepageneratedDate(new \DateTime('0000-00-00 00:00:00'));
                $amazonEntity->setDeviceType('NULL');
                $amazonEntity->setCreated($affiliatetransaction->getCreated());
                $amazonEntity->setModified($affiliatetransaction->getModified());
                /*if(!$this->debugmode):*/
                try {
                    $em->persist($amazonEntity);
                    $em->flush();
                    $LastInsertedId = $amazonEntity->getId();
                } catch (Exception $e) {
                    return false;
                }
                /***************** PENDIG is also Approved Transaction on trackingDate + period  **********/
                $clientcashbacktransactionId = $LastInsertedId;
                // echo 'amazon '.$LastInsertedId;
                if ($clientcashbacktransactionId) {
                    $amazonEntity->setAffiliateAproveddate(new \DateTime($affiliatetransaction->getTrackingDate()));
                    $em->persist($amazonEntity);
                    //$em->flush();
                    /***************** Create double or triple Cashback based on Cashbacktransaction **********************/
                    $this->createDobleTripleCashback($clientcashbacktransactionId, $em);
                    $TQueueMail = new TransactionalQueueMail();
                    //TO-DO :: Confirm shop record
                    $TQueueMail->setShop($this->getProcessAffiliateShop($shopId, $em));
                    $TQueueMail->setShopHistory($this->getProcessAffiliateShopHistory($shopHistoryId, $em));
                    $TQueueMail->setCashbacktransactionId($clientcashbacktransactionId);
                    $TQueueMail->setIdClient($userId);
                    $TQueueMail->setIsoCode('ES');
                    $TQueueMail->setMailType($transactionalemailType);
                    //$TQueueMail->setShopName($shopRecord->getBrand()); // TO-DO :: Check shop name
                    $TQueueMail->setShopName('');
                    $TQueueMail->setAmount($amount);
                    $TQueueMail->setTotal(0);
                    $TQueueMail->setCurrency($curCode); //Removed fixed EUR as ideally email should contain the transactional currency
                    $TQueueMail->setPurchaseDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                    $TQueueMail->setStatus(TransactionalQueueMail::STATUS_TYPE_CONFIRMED);
                    $TQueueMail->setSendedDate(new \DateTime('0000-00-00 00:00:00'));
                    $TQueueMail->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setModified(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($TQueueMail);
                    //$em->flush();				
                }
            }
        }

        return true;
    }

    public function createClienttransactionLinkShare($affiliatetransaction, $em, $containerEmailObject, $admin_email_id)
    {
        if ($affiliatetransaction->getShopHistory() || $affiliatetransaction->getParam1()) {
            $sendEmail = new \AppShell();
            $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $affiliatetransaction->getShopHistory()));
            if (empty($shopHistory)):
                $trackingDate = new DateTime($affiliatetransaction->getTrackingDate());
                $shShopId = $affiliatetransaction->getParam1();
                $connection = $em->getConnection();
               /* $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate AND (sh.endDate IS NULL OR sh.endDate >= :endDate) LIMIT 0,1');*/
                $statement = $connection->prepare('SELECT * FROM `lb_shop_history` sh WHERE sh.shop = :shShopId AND sh.startDate <= :startDate LIMIT 0,1');
                $statement->bindValue('shShopId', $shShopId);
                $statement->bindValue('startDate', $trackingDate->format('Y-m-d H:i:s'));
                //$statement->bindValue('endDate', $trackingDate->format('Y-m-d H:i:s'));
                $statement->execute();
                $shopHistory = $statement->fetch();
            endif;

            if (empty($shopHistory)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción LinkShare Shophistory empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionLinkShare ', $containerEmailObject, $admin_email_id);
                    /* ERROR creating Client transaction TD, shophistory EMPTY */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id' => trim($affiliatetransaction->getParam0())));

            if (empty($userRecord)):
                try {
                    $sendEmail->sendAdminAlert('Error: Transacción LinkShare User record empty'.$affiliatetransaction->getTransactionId(), 'AffiliatetransactionsShell createClienttransactionLinkShare ', $containerEmailObject, $admin_email_id);
                    /*  ERROR creating Client transaction ZANOX, shophistory EMPTY  */
                } catch (Exception $e) {
                    //Flushed when got any exception on swiftmailer
                    $em->flush();
                }

                return false;
            endif;

            $shopRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($affiliatetransaction->getParam1())));
            if ($shopRecord && $userRecord) {
                $userId = $userRecord->getId();
                $shopId = $shopRecord->getId();
                $shopHistoryId = 'NULL';
                if (is_object($shopHistory)):
                    $shopHistoryId = $shopHistory->getId();
                elseif (!empty($shopHistory['id'])):
                    $shopHistoryId = $shopHistory['id'];
                endif;

                /*  Creating Client transaction LinkShare  */
                $affiliateStatus = $this->getAffiliateStatus($affiliatetransaction);
                $networkStatus = $this->statusMap[$affiliatetransaction->getStatus()];

                $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_APPROVED;
                $affiliateCancelDate = null;
                $transactionalemailType = TransactionalQueueMail::PURCHASE_DONE;
                if ($affiliateStatus == LetsBonusTransactions::STATUS_TYPE_CANCELLED):
                    /* Creating Client transaction LinkShare cancelled */
                    $clienttransactionStatus = cashbackTransactions::STATUS_TYPE_DENIED;
                    $affiliateCancelDate = date('Y-m-d H:i:s');
                    $transactionalemailType = TransactionalQueueMail::PURCHASE_DENIED;
                endif;

                $commission = $affiliatetransaction->getCommission();

                $amount = 0;
                if (is_object($shopHistory)) {
                    if (!empty($shopHistory->getLetsBonusPercentage())) {
                        $amount = round($commission - ($commission * ($shopHistory->getLetsBonusPercentage() / 100)), 2);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $amount = round($commission - ($commission * ($shopHistory['letsBonusPercentage'] / 100)), 2);
                } else {
                    $amount = 0;
                }

                $linkShareEntity = new cashbackTransactions();
                $currency = new Currency();

                $currencyCode = $em->getRepository('iFlairLetsBonusAdminBundle:currency')->findOneBy(array('id' => trim($affiliatetransaction->getCurrency())));

                $curCode = '';
                $curCode = $currencyCode->getCode();
                /*foreach ($currencyCode as $c) {
                    $curCode = $c->getCode();
                }*/

                $shopIdRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => trim($shopId)));
                $linkShareEntity->setShopId($shopIdRecord);
                $linkShareEntity->setShopHistory($shopHistory);
                $linkShareEntity->setUserId($userId);
                $linkShareEntity->setTransactionId($affiliatetransaction->getTransactionId());

                $NetworkRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('id' => trim($affiliatetransaction->getNetwork()->getId())));
                $linkShareEntity->setNetworkId($NetworkRecord);
                $linkShareEntity->setAmount($amount);
                $linkShareEntity->setAffiliateAmount($affiliatetransaction->getCommission());
                $linkShareEntity->setTotalAffiliateAmount($affiliatetransaction->getAmount());

                if (is_object($shopHistory)) {
                    if ($letsBonusPercentage = $shopHistory->getLetsBonusPercentage()) {
                        $linkShareEntity->setLetsbonusPct($letsBonusPercentage);
                    }
                } elseif (!empty($shopHistory['letsBonusPercentage'])) {
                    $linkShareEntity->setLetsbonusPct((float) $shopHistory['letsBonusPercentage']);
                } else {
                    $linkShareEntity->setLetsbonusPct(0);
                }

                $linkShareEntity->setCurrency($currencyCode);
                $linkShareEntity->setStatus($clienttransactionStatus);
                $linkShareEntity->setType(cashbackTransactions::TRANSACTION_TYPE_ADDED);
                $linkShareEntity->setNetworkStatus($networkStatus);
                $linkShareEntity->setAffiliateCanceldate(new \DateTime($affiliateCancelDate));
                $linkShareEntity->setDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                $linkShareEntity->setExtraAmount(0);
                $linkShareEntity->setExtraPct(0);
                $linkShareEntity->setOrderReference(0);
                $linkShareEntity->setAffiliateAproveddate(new \DateTime('0000-00-00 00:00:00'));
                $linkShareEntity->setAprovalDate(new \DateTime('0000-00-00 00:00:00'));
                $linkShareEntity->setUserName('NULL');
                $linkShareEntity->setUserAddress('NULL');
                $linkShareEntity->setUserdni('NULL');
                $linkShareEntity->setUserPhone('NULL');
                $linkShareEntity->setUserBankAccountNumber('NULL');
                $linkShareEntity->setBic('NULL');
                $linkShareEntity->setCompanyId(null);
                $linkShareEntity->setCashbacktransactionsChilds('NULL');
                $linkShareEntity->setAdminuserId(1);
                $linkShareEntity->setManualNumdaystoapprove(0);
                $linkShareEntity->setComments('NULL');
                $linkShareEntity->setParentTransactionId(0);
                $linkShareEntity->setCashbacksettingId(null);
                $linkShareEntity->setSepageneratedbyUserId(0);
                $linkShareEntity->setSepageneratedDate(new \DateTime('0000-00-00 00:00:00'));
                $linkShareEntity->setDeviceType('NULL');
                $linkShareEntity->setCreated($affiliatetransaction->getCreated());
                $linkShareEntity->setModified($affiliatetransaction->getModified());
                /*if(!$this->debugmode):*/
                try {
                    $em->persist($linkShareEntity);
                    // $em->flush();
                    $LastInsertedId = $linkShareEntity->getId();
                } catch (Exception $e) {
                    return false;
                }
                $clientcashbacktransactionId = $LastInsertedId;
                if ($clientcashbacktransactionId) {
                    /***************** Create double or triple Cashback based on Cashbacktransaction **********************/
                    $this->createDobleTripleCashback($clientcashbacktransactionId, $em);
                    $TQueueMail = new TransactionalQueueMail();
                    //TO-DO :: Confirm shop record
                    $TQueueMail->setShop($this->getProcessAffiliateShop($shopId, $em));
                    $TQueueMail->setShopHistory($this->getProcessAffiliateShopHistory($shopHistoryId, $em));
                    $TQueueMail->setCashbacktransactionId($clientcashbacktransactionId);
                    $TQueueMail->setIdClient($userId);
                    $TQueueMail->setIsoCode('ES');
                    $TQueueMail->setMailType($transactionalemailType);
                    //$TQueueMail->setShopName($shopRecord->getBrand());
                    $TQueueMail->setShopName(' ');
                    $TQueueMail->setAmount($amount);
                    $TQueueMail->setTotal(0);
                    $TQueueMail->setCurrency($curCode); //Removed fixed EUR as ideally email should contain the transactional currency
                    $TQueueMail->setPurchaseDate(new \DateTime($affiliatetransaction->getTrackingDate()));
                    $TQueueMail->setStatus(TransactionalQueueMail::STATUS_TYPE_CONFIRMED);
                    $TQueueMail->setSendedDate(new \DateTime('0000-00-00 00:00:00'));
                    $TQueueMail->setCreated(new \DateTime(date('Y-m-d H:i:s')));
                    $TQueueMail->setModified(new \DateTime(date('Y-m-d H:i:s')));
                    $em->persist($TQueueMail);
                    //$em->flush();					
                }
            }
        }

        return true;
    }

    public function createDobleTripleCashback($cashbacktransactionId, $em)
    {
        /*
            We have Skipped Below Contain Function
        */
        /* $this->Cashbacktransaction->contain(array('Shop')); */
        /*$parentCashbacktransaction = $this->Cashbacktransaction->read(null,$cashbacktransactionId);*/
        //------------------------- CHECK IF EXIST Cashback config on evenDate -------------------------/
        $parentCashbacktransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => $cashbacktransactionId));
        if ($parentCashbacktransaction) {
            $eventdate = $parentCashbacktransaction->getDate();
            $eventdate->format('Y-m-d H:i:s');  //2016-03-01 15:33:45
            $parentCashbacktransaction->getShopId(); // 188       
            $connection = $em->getConnection();
            $statement = $connection->prepare('SELECT * FROM lb_cashbackSettings AS c, lb_cachback_settings_shop AS cs
							 WHERE c.startDate <= :startDate 
							 AND c.endDate >= :endDate
							 AND c.status = 1
							 AND cs.shop_id = :shopId
							 AND c.id = cs.cashback_settings_id
							 GROUP BY c.id LIMIT 0,1');
            $statement->bindValue('startDate', $eventdate->format('Y-m-d H:i:s'));
            $statement->bindValue('endDate', $eventdate->format('Y-m-d H:i:s'));
            $statement->bindValue('shopId', $parentCashbacktransaction->getShopId());
            $statement->execute();
            $cashbacksettings = $statement->fetch();
            //-------------------- UPDATE parent transaction: cashbacksetting ID --------------------------
            if (!empty($cashbacksettings)):
                $cashbacktransn = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => $parentCashbacktransaction->getId()));

                $cashbacksettingObj = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackSettings')->findOneBy(array('id' => $cashbacksettings['id']));
                $cashbacktransn->setCashbacksettingId($cashbacksettingObj);
                $em->persist($cashbacktransn);
                $em->flush();
                    /*$parentCashbacktransaction->setId(null);*/
                $parentCashbacktransaction->setLetsbonusPct(0);
                $parentCashbacktransaction->setType(cashbackTransactions::TRANSACTION_TYPE_MANUAL);
                $parentCashbacktransaction->setComments($cashbacksettings['type'].' Cashback');
                $parentCashbacktransaction->setParentTransactionId($cashbacktransactionId);
                $parentCashbacktransaction->setTransactionId('manual-'.$cashbacksettings['type'].':'.$cashbacksettings['id'].'-'.$parentCashbacktransaction->getTransactionId());
                $parentCashbacktransaction->setCashbacksettingId($cashbacksettingObj);
                $repeat = 0;
                if ($cashbacksettings['type'] == 'double'):
                    $repeat = 1;
                elseif ($cashbacksettings['type'] == 'triple'):
                    $repeat = 2;
                endif;
                    /* Clone For Save Duplicate Data */
                $cloneParentCashbackTransaction = clone $parentCashbacktransaction;

                for ($i = 0;$i < $repeat;++$i):
                    if ($i == 0):
                        $em->persist($parentCashbacktransaction);
                        //$em->flush();
                    endif;
                    if ($i == 1):
                        $em->persist($cloneParentCashbackTransaction);
                        //$em->flush();
                    endif;
                endfor;
                // die('Double Tripple Saved..!!');
            endif;
        }
    }

    public function getClienttransactiondetail($id, $em)
    {
        return $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => trim($id)));
    }

    public function getClientTransactionStatus($transaction, $em)
    {
        if (!in_array(trim($transaction->getStatus()), array_keys($this->statusMap))):
            /* Error status Affiliate status unknown ".$transaction->getStatus() */
            return -1;
        endif;
        /* Get client transaction status:".$this->statusMap[trim($transaction['Cashbacktransaction']['status'])]);*/
        return $this->statusMap[trim($transaction->getStatus())];
    }

    public function updateClientAffiliateApprovaldate($id, $em)
    {
        /* Updating client transaction ClientAffiliateAprovaldate to: ".date('Y-m-d H:i:s') */
        $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => trim($id)));

        if (!empty($clientTransaction)) {
            $clientTransaction->setAffiliateAproveddate(new \DateTime());
            $em->persist($clientTransaction);
            //$em->flush();
        }
    }

    public function updateClientNetworkStatus($id, $status, $em)
    {
        /*die('update client network status');*/
        /*  Updating client transaction updateClientNetworkStatus to Approved  */
        $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => trim($id)));
        $clientTransaction->setNetworkStatus(trim($status));
        $em->persist($clientTransaction);
        //$em->flush();
    }

    public function updateClientStatus($id, $status, $em)
    {
        /* Updating client transaction updateClientStatus to: ".$status */
        $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => trim($id)));
        $clientTransaction->setStatus(trim($status));
        $em->persist($clientTransaction);

        //$em->flush();
    }

    public function updateClientAffiliateCanceldate($id, $em)
    {
        /* Updating client transaction updateClientAffiliateCanceldate to: ".date('Y-m-d H:i:s') */
        $clientTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')->findOneBy(array('id' => trim($id)));
        $clientTransaction->setAffiliateCanceldate(new \DateTime());
        $em->persist($clientTransaction);
        //$em->flush();
    }

    protected function getProcessAffiliateShop($shopId, $em)
    {
        return $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneById(trim($shopId));
    }

    protected function getProcessAffiliateShopHistory($shopHistoryId, $em)
    {
        return $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneById(trim($shopHistoryId));
    }
}

<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/*use Symfony\Bundle\SwiftmailerBundle\Command\SendEmailCommand;*/
/* ENTITY */
use iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions;
use iFlair\LetsBonusAdminBundle\Entity\TransactionalQueueMail;

require_once 'AppShell.php';

class ApproveClientTransactionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:approveclienttransaction')->setDescription("Approve client's transactions");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        /********************** Client�s transaccions created by SAC && !Doblecashback *****************/
        $this->processTransactionsCreatedBySAC($em);

        /*********************** Client�s transaccions type PROMO ************************************/
        $this->processPromoTransactions($em);

        $clientTransactions = $em->createQueryBuilder()
            ->select('ct')
            ->from('iFlairLetsBonusAdminBundle:cashbackTransactions',  'ct')
            ->where('ct.status = :status')
            ->setParameter('status', cashbackTransactions::STATUS_TYPE_PENDING)
            ->andWhere('ct.affiliateAproveddate IS NOT NULL AND ct.affiliateAproveddate != :blankDate')
            ->setParameter('blankDate', '0000-00-00 00:00:00')
            ->andWhere('ct.type IN (:typelist)')
            ->setParameter('typelist', array(cashbackTransactions::TRANSACTION_TYPE_ADDED, cashbackTransactions::TRANSACTION_TYPE_WITHDRAWAL, cashbackTransactions::TRANSACTION_TYPE_VOUCHER))
            ->getQuery()
            ->getResult();

        echo "\n\n   Start ApproveClienttransactionsShell   \n\n";
        if (empty($clientTransactions)) {
            echo 'No client transaction on pending status';
            $this->processDeniedTransactions($em);
            echo 'client transaction on denied status';
        } else {
            foreach ($clientTransactions as $element):
                $clientTransactionId = $element->getId();
            echo "============================ Client transactions ID: $clientTransactionId===========================\n";

            $shop = $this->getMatchedShop($element->getShopId(), $em);
            if (!$shop) {
                echo "\t ERROR: SHOP NOT EXIST ID# ".$element->getShopId();
                break;
            }

            $daysToConfirm = ($shop->getDaysValidateConfirmation()) ? $shop->getDaysValidateConfirmation() : 15;
            $dateToApprove = new \DateTime($element->getAffiliateAproveddate()->format('Y-m-d H:i:s'));
            $dateToApprove = $dateToApprove->modify('+'.$daysToConfirm.' days');
            $today = new \DateTime();

            echo "\tCheck to approve transaction dateToapprove: ".$dateToApprove->format('Y-m-d H:i:s').' today: '.$today->format('Y-m-d H:i:s')."\n";
            if ($dateToApprove->format('U') < $today->format('U')):

                    echo "\tApprove client transaction\n";
            $element->setAprovalDate(new \DateTime(date('Y-m-d H:i:s')));
            $element->setStatus(cashbackTransactions::STATUS_TYPE_CONFIRMED);
            $em->persist($element);
            /* Process Confirm Transaction :: Send Email */
            $this->processConfiredTransactions($element, $em);

                    /**** Save  Childs transactions (DobleCashback) ***/
                    $this->saveChildsTransactions($clientTransactionId, $em);

            $userId = $element->getUserId();
            $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')
                                    ->findOneBy(array('id' => $element->getShopHistory()));
            $totalAmount = $em->createQueryBuilder()
                        ->select('SUM(ct.amount) as Saldo')
                        ->from('iFlairLetsBonusAdminBundle:cashbackTransactions',  'ct')
                        ->where('ct.userId = :userId')
                        ->setParameter('userId', $userId)
                        ->andWhere('ct.status = :status')
                        ->setParameter('status', cashbackTransactions::STATUS_TYPE_CONFIRMED)
                        ->getQuery()
                        ->getResult();
            $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')
                                    ->findOneBy(array('id' => $userId));

            if ($shopHistory && $userRecord) {
                $transactionalEmailType = TransactionalQueueMail::PURCHASE_CONFIRMED;
                $TQueueMail = new TransactionalQueueMail();
                        //TO-DO :: Confirm shop record
                        $TQueueMail->setShop($shop);
                $TQueueMail->setShopHistory($shopHistory);
                $TQueueMail->setCashbacktransactionId($clientTransactionId);
                $TQueueMail->setIdClient($userId);
                $TQueueMail->setIsoCode('ES');
                $TQueueMail->setMailType($transactionalEmailType);
                        //$TQueueMail->setShopName($shop->getBrand());
                        $TQueueMail->setShopName(' ');
                $TQueueMail->setAmount($element->getAmount());
                $TQueueMail->setTotal(($totalAmount[0]['Saldo']) ? $totalAmount[0]['Saldo'] : 0);
                $TQueueMail->setCurrency('EUR');
                $TQueueMail->setStatus(TransactionalQueueMail::STATUS_TYPE_CONFIRMED);
                $TQueueMail->setSendedDate(new \DateTime());
                $TQueueMail->setPurchaseDate(new \DateTime(date('Y-m-d H:i:s', strtotime($element->getDate()->format('Y-m-d H:i:s')))));
                $em->persist($TQueueMail);
            }
            endif;
            echo "\n\n";
            endforeach;
            $this->processDeniedTransactions($em);
        }

        $em->flush();
    }

    public function processConfiredTransactions($cashbackTransaction, $em)
    {
        $provider = $this->getContainer()->get('sonata.media.provider.image');
        $cashback_Record = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')
                                ->findOneBy(array('id' => trim($cashbackTransaction->getId())));

        $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')
                                    ->findOneBy(array('id' => trim($cashback_Record->getUserId())));
        $name = $userRecord->getName();
        $email = $userRecord->getEmail();
        $shopRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')
                            ->findOneBy(array('id' => trim($cashback_Record->getShopId())));
        $shopname = $shopRecord->getTitle();
        $shop_image = '';
        if (!empty($shopRecord->getImage())):
            $media = $shopRecord->getImage();
        $format = $provider->getFormatName($media, 'hoy_te_recomendamos');
        $shop_image = $provider->generatePublicUrl($media, $format); // Shop Image
        else:
            $shop_image = ''; // Shop Image
        endif;

        $total_amount = $cashback_Record->getAmount(); // Total Amount

        $CurrencyRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Currency')
                            ->findOneBy(array('id' => trim($cashback_Record->getCurrency())));
        $currency_symbol = '';
        /* TO-DO :: Update Dynamic Currency Symbols */
        if (trim($CurrencyRecord->getCode()) == 'EUR') {
            $currency_symbol = '�';
        }
        $message = \Swift_Message::newInstance()
            ->setSubject('�Tu compra ha sido confirmada!')
            ->setFrom($this->getContainer()->getParameter('from_send_email_id'))
            ->setTo(trim($email))
            ->setBody($this->getContainer()->get('templating')->render(
                'iFlairLetsBonusFrontBundle:Email:Transactional_Email_Compra_Confirmada.html.twig',
                array(
                        'name' => $name,
                        'email' => $email,
                        'shopname' => $shopname,
                        'amount' => $total_amount,
                        'shopimage' => $shop_image,
                        'currency' => $currency_symbol,
                    )
                ), 'text/html');
        $containerEmailObject = $this->getContainer()->get('mailer');
        $res = $containerEmailObject->send($message);
        if ($res == 1):
            echo "Confirmation Email Sent!  \n";
        endif;
    }

    public function processTransactionsCreatedBySAC($em)
    {
        /*************************** Client�s transaccions created by SAC && !Doblecashback ********************************/
        $clientTransactionsManual = $em->createQueryBuilder()
            ->select('ct')
            ->from('iFlairLetsBonusAdminBundle:cashbackTransactions',  'ct')
            ->where('ct.status = :status')
            ->setParameter('status', cashbackTransactions::STATUS_TYPE_PENDING)
            ->andWhere('ct.parentTransactionId IS NULL')
            ->andWhere('ct.type = :type')
            ->setParameter('type', cashbackTransactions::TRANSACTION_TYPE_MANUAL)
            ->getQuery()
            ->getResult();

        echo "\n\n   Start ApproveClienttransactionsShell: MANUAL transactions   \n\n";
        if (empty($clientTransactionsManual)) {
            echo 'No Manual - client transaction on pending status';
        } else {
            foreach ($clientTransactionsManual as $element):
                $clientTransactionId = $element->getId();
            echo "============================ Client transactions ID: $clientTransactionId===========================\n";
            $shop = $this->getMatchedShop($element->getShopId(), $em);
            if (!$shop) {
                echo "\t ERROR: SHOP NOT EXIST ID# ".$element->getShopId();
                break;
            }
            $daysToConfirm = $element->getManualNumdaystoapprove();
            $dateToApprove = new \DateTime($element->getDate());
            $dateToApprove = $dateToApprove->modify('+'.$daysToConfirm.' days');
            $today = new \DateTime();

            echo "\tCheck to approve transaction dateToapprove: ".$dateToApprove->format('Y-m-d H:i:s').' today: '.$today->format('Y-m-d H:i:s')."\n";
            if ($dateToApprove->format('U') < $today->format('U')):

                    echo "\tApprove client transaction\n";
            $element->setAprovalDate(new \DateTime(date('Y-m-d H:i:s')));
            $element->setStatus(cashbackTransactions::STATUS_TYPE_CONFIRMED);
            $em->persist($element);

            endif;
            endforeach;
        }
    }

    public function processDeniedTransactions($em)
    {
        $clientDeniedTransactions = $em->createQueryBuilder()
            ->select('ct')
            ->from('iFlairLetsBonusAdminBundle:cashbackTransactions',  'ct')
            ->where('ct.status = :status')
            ->setParameter('status', cashbackTransactions::STATUS_TYPE_DENIED)
            ->andWhere('ct.deniedMailStatus = :deniedMailStatus')
            ->setParameter('deniedMailStatus', 0)
            ->getQuery()
            ->getResult();

        if (empty($clientDeniedTransactions)) {
            echo "No client transaction on Denied status \n ";
        } else {
            foreach ($clientDeniedTransactions as $transaction) {
                $userRecord = $em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')
                                    ->findOneBy(array('id' => trim($transaction->getUserId())));
                $name = $userRecord->getName();
                $email = $userRecord->getEmail();
                $shopRecord = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')
                                    ->findOneBy(array('id' => trim($transaction->getShopId())));
                $shopname = $shopRecord->getTitle();
                $message = \Swift_Message::newInstance()
                    ->setSubject('Tu Cashback no ha sido aprobado')
                    ->setFrom($this->getContainer()->getParameter('from_send_email_id'))
                    ->setTo(trim($email))
                    ->setBody($this->getContainer()->get('templating')->render(
                        'iFlairLetsBonusFrontBundle:Email:Transactional_Email_Cashback_Denegado.html.twig',
                        array(
                                'name' => $name,
                                'email' => $email,
                                'shopname' => $shopname,
                            )
                        ), 'text/html');
                $containerEmailObject = $this->getContainer()->get('mailer');
                $res = $containerEmailObject->send($message);
                if ($res) {
                    echo "Transaction Denied Email Alert Sent..!! \n";
                }
                $transaction->setDeniedMailStatus(1);
                $em->persist($transaction);
                $em->flush();
            }
        }
    }

    public function processPromoTransactions($em)
    {
        //----- Una transaccion PROMO no deberia estar asociada a ninguna venta ----
        $clientTransactionsPromo = $em->createQueryBuilder()
            ->select('ct')
            ->from('iFlairLetsBonusAdminBundle:cashbackTransactions',  'ct')
            ->where('ct.status = :status')
            ->setParameter('status', cashbackTransactions::STATUS_TYPE_PENDING)
            ->andWhere('ct.type = :type')
            ->setParameter('type', cashbackTransactions::TRANSACTION_TYPE_PROMO)
            ->getQuery()
            ->getResult();

        echo "\n\n   Start ApproveClienttransactionsShell: PROMO transactions   \n\n";
        if (empty($clientTransactionsPromo)) {
            echo 'No PROMO - client transaction on pending status';
        } else {
            foreach ($clientTransactionsPromo as $element):
                $clientTransactionId = $element->getId();
            echo "============================ Client transactions ID: $clientTransactionId===========================\n";

            $daysToConfirm = $element->getManualNumdaystoapprove();
            $dateToApprove = new \DateTime($element->getDate());
            $dateToApprove = $dateToApprove->modify('+'.$daysToConfirm.' days');
            $today = new \DateTime();

            echo "\tCheck to approve transaction dateToapprove: ".$dateToApprove->format('Y-m-d H:i:s').' today: '.$today->format('Y-m-d H:i:s')."\n";
            if ($dateToApprove->format('U') < $today->format('U')):

                    echo "\tApprove client transaction\n";
            $element->setAprovalDate(new \DateTime(date('Y-m-d H:i:s')));
            $element->setStatus(cashbackTransactions::STATUS_TYPE_CONFIRMED);
            $em->persist($element);
            endif;
            endforeach;
        }
    }

    protected function getMatchedShop($shopId, $em)
    {
        return $em
            ->getRepository('iFlairLetsBonusAdminBundle:Shop')
            ->findOneById(trim($shopId));
    }

    public function saveChildsTransactions($clientTransactionId, $em)
    {
        //----- type manual: �nico m�tod de crear transacciones relacionadas
        $clientTransactionsChildsManual = $em->createQueryBuilder()
            ->select('ct')
            ->from('iFlairLetsBonusAdminBundle:cashbackTransactions',  'ct')
            ->where('ct.parentTransactionId = :parentTransactionId')
            ->setParameter('parentTransactionId', $clientTransactionId)
            ->andWhere('ct.status = :status')
            ->setParameter('status', cashbackTransactions::STATUS_TYPE_PENDING)
            ->andWhere('ct.type = :type')
            ->setParameter('type', cashbackTransactions::TRANSACTION_TYPE_MANUAL)
            ->getQuery()
            ->getResult();

        $parentTransaction = $em->getRepository('iFlairLetsBonusAdminBundle:cashbackTransactions')
                                    ->findOneBy(array('id' => $clientTransactionId));

        if (!empty($clientTransactionsChildsManual)):
            foreach ($clientTransactionsChildsManual as $element):
                if ($parentTransaction->getStatus() == cashbackTransactions::STATUS_TYPE_CONFIRMED):
                    echo "\tApprove Child transaction\n";
        $element->setAprovalDate(new \DateTime(date('Y-m-d H:i:s', strtotime($parentTransaction->getAprovalDate()))));
        $element->setStatus(cashbackTransactions::STATUS_TYPE_CONFIRMED);
        $em->persist($element);
        endif;
        endforeach;
        endif;
    }
}

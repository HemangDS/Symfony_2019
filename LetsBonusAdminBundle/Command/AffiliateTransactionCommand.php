<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/*use Symfony\Bundle\SwiftmailerBundle\Command\SendEmailCommand;*/
/* ENTITY */
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions;
use iFlair\LetsBonusAdminBundle\Entity\Network;

require_once 'ProcessAffiliatetransactions.php';
require_once 'AppShell.php';

class AffiliateTransactionCommand extends ContainerAwareCommand
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
    protected function configure()
    {
        $this->setName('network:affiliatetransaction')->setDescription('Change Status from Transaction and Store into Cashbacktransaction');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        // GET PENDING STATUS ALL TRANSACTION FROM LB_TRANSACTION
        $pendingTransactions = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findByProcessed(LetsBonusTransactions::PROCESSED_TYPE_PENDING);

        if (empty($pendingTransactions)):
            /* No transanctions with PENDING status */
        endif;
        foreach ($pendingTransactions as $pendingTransaction) {
            /* Process Affiliate Transaction Object */
            $processaffiliatetransaction = new \ProcessAffiliatetransaction();
            if ($processaffiliatetransaction->isLead($pendingTransaction)):
                $processaffiliatetransaction->changeAffiliateStatus($pendingTransaction, $em);
            else:
                if ($processaffiliatetransaction->checkifClienttransactionalreadyPayed($pendingTransaction, $em)):
                    $processaffiliatetransaction->changeAffiliateStatus($pendingTransaction, $em);
                    continue;
                endif;
                $this->processAffiliateTransaction($pendingTransaction, $em);
            endif;
        }
        $em->flush();
    }

    public function processAffiliateTransaction($pendingTransaction, $em)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');

        $containerEmailObject = $this->getContainer()->get('mailer');
        $sendEmail = new \AppShell();

        $processaffiliatetransaction = new \ProcessAffiliatetransaction();
        switch ($processaffiliatetransaction->getAffiliateStatus($pendingTransaction)) {

            case LetsBonusTransactions::STATUS_TYPE_PENDING:
                $this->processAffiliateTransactionPending($pendingTransaction, $em);
            break;

            case LetsBonusTransactions::STATUS_TYPE_APPROVED:
                $this->processAffiliateTransactionApproved($pendingTransaction, $em);
            break;

            case LetsBonusTransactions::STATUS_TYPE_CANCELLED:
                $this->processAffiliateTransactionCancelled($pendingTransaction, $em);
            break;

            case -1:
                $sendEmail->sendAdminAlert('Error: Affiliate transaction with status unknow: '.$pendingTransaction->getTransactionId(), 'ProcessAffiliatetransactionsShell by status ', $containerEmailObject, $admin_email_id);
                $processaffiliatetransaction->changeAffiliateStatus($pendingTransaction, $em);
            break;
        }
    }

    public function processAffiliateTransactionPending($pendingTransaction, $em)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');

        $containerEmailObject = $this->getContainer()->get('mailer');
        /*  processAffiliateTransaction status PENDING  */
        $processaffiliatetransaction = new \ProcessAffiliatetransaction();

        if ($processaffiliatetransaction->checkifClienttransactionExist($pendingTransaction, $em)):
            $processaffiliatetransaction->updateClienttransaction($pendingTransaction, $em);
            $processaffiliatetransaction->changeAffiliateStatus($pendingTransaction, $em);
        else:
            $processaffiliatetransaction->createClienttransaction($pendingTransaction, $em, $containerEmailObject, $admin_email_id);
            $processaffiliatetransaction->changeAffiliateStatus($pendingTransaction, $em);
        endif;
    }

    public function processAffiliateTransactionApproved($approvedTransaction, $em)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');

        /*  processAffiliateTransaction status Approved  */
        $sendEmail = new \AppShell();
        $containerEmailObject = $this->getContainer()->get('mailer');

        $approvedTransaction->getTransactionId();
        $processaffiliatetransaction = new \ProcessAffiliatetransaction();
        /* processAffiliateTransaction status Approved */
        if ($clientTransactionId = $processaffiliatetransaction->checkifClienttransactionExist($approvedTransaction, $em)):
            $clientTransaction = $processaffiliatetransaction->getClienttransactiondetail($clientTransactionId, $em);
            switch ($processaffiliatetransaction->getClientTransactionStatus($clientTransaction, $em)) {

                case cashbackTransactions::STATUS_TYPE_PENDING:

                    $affiliateApproveDate = $clientTransaction->getAffiliateAproveddate();
                    if (substr($affiliateApproveDate->format('Y'), 0, 1) == '-'):
                        $processaffiliatetransaction->updateClientAffiliateApprovaldate($clientTransactionId, $em);
                    endif;
                    $processaffiliatetransaction->updateClienttransaction($approvedTransaction, $em);
                    $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
                    $processaffiliatetransaction->updateClientNetworkStatus($clientTransactionId, cashbackTransactions::NETWORK_STATUS_APPROVED, $em);
                break;

                case cashbackTransactions::STATUS_TYPE_APPROVED:

                    if (empty($clientTransaction->getAffiliateAproveddate())):
                        $processaffiliatetransaction->updateClientAffiliateApprovaldate($clientTransactionId, $em);
                    endif;
                    $processaffiliatetransaction->updateClienttransaction($approvedTransaction, $em);
                    $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
                    $processaffiliatetransaction->updateClientNetworkStatus($clientTransactionId, cashbackTransactions::NETWORK_STATUS_APPROVED, $em);
                break;

                case cashbackTransactions::STATUS_TYPE_CANCELLED:

                    $sendEmail->sendAdminAlert('Error: Transacción con status approved ', 'ProcessAffiliatetransactionsShell processAffiliateTransactionApproved ', $containerEmailObject, $admin_email_id);
                    $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
                break;

                case cashbackTransactions::STATUS_TYPE_PAYED:

                    //Special case: Cashbacktransaction already payed and getting 'confirmed' transaccion after 'approved'
                    //if($element['Transaction']['status']=='confirmed'):
                    if ($this->statusMap[trim(strtolower($approvedTransaction->getStatus()))] == LetsBonusTransactions::STATUS_TYPE_APPROVED):
                        $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
                    endif;
                break;

                case -1:
                    $sendEmail->sendAdminAlert('Error: Client transaction with status unknow', 'ProcessAffiliatetransactionsShell bprocessAffiliateTransactionApproved ', $containerEmailObject, $admin_email_id);
                    $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
                break;
            }
        else:
            $processaffiliatetransaction->createClienttransaction($approvedTransaction, $em, $containerEmailObject, $admin_email_id);
            $clientTransactionId = $processaffiliatetransaction->checkifClienttransactionExist($approvedTransaction, $em);
            $processaffiliatetransaction->updateClientAffiliateApprovaldate($clientTransactionId, $em);
            $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
        endif;
    }

    public function processAffiliateTransactionCancelled($approvedTransaction, $em)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');

        /*  processAffiliateTransaction status Cancelled  */
        $sendEmail = new \AppShell();
        $containerEmailObject = $this->getContainer()->get('mailer');
        $processaffiliatetransaction = new \ProcessAffiliatetransaction();
        if ($clientTransactionId = $processaffiliatetransaction->checkifClienttransactionExist($approvedTransaction, $em)):
            $clientTransaction = $processaffiliatetransaction->getClienttransactiondetail($clientTransactionId, $em);
            switch ($processaffiliatetransaction->getClientTransactionStatus($clientTransaction, $em)) {
                case cashbackTransactions::STATUS_TYPE_PENDING:
                    $processaffiliatetransaction->updateClientStatus($clientTransactionId, cashbackTransactions::STATUS_TYPE_DENIED, $em);
                    $processaffiliatetransaction->updateClientAffiliateCanceldate($clientTransactionId, $em);
                    $processaffiliatetransaction->updateClientNetworkStatus($clientTransactionId, cashbackTransactions::NETWORK_STATUS_CANCELLED, $em);
                    $processaffiliatetransaction->updateClienttransaction($approvedTransaction, $em);
                    $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
                    // TODO MAIL AL USUARIO :: MAIL EVERYTING TO YOU
                    break;

                case cashbackTransactions::STATUS_TYPE_APPROVED:
                    $transactionEmailData = 'Error: No deberia llegar un cancel sobre una transaccion ya aprovada con el margen cumplido! <br/><br/>';
                    $transactionEmailData .= 'CashbacktransactionId: '.$clientTransaction->getTransactionId().'; amount: '.$clientTransaction->getAmount().' '.$clientTransaction->getCurrency();
                    $sendEmail->sendAdminAlert($transactionEmailData, 'Cashback Monitor: Alerta transacción denegada', $containerEmailObject, $admin_email_id);
                    $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
                    break;

                case cashbackTransactions::STATUS_TYPE_CANCELLED:
                    $processaffiliatetransaction->updateClienttransaction($approvedTransaction, $em);
                    $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
                    break;

                case -1:
                    $sendEmail->sendAdminAlert('Error: Client transaction with status unknow', 'ProcessAffiliatetransactionsShell processAffiliateTransactionCancelled ', $containerEmailObject, $admin_email_id);
                    $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
                    break;
            }
        else:
            $processaffiliatetransaction->createClienttransaction($approvedTransaction, $em, $containerEmailObject, $admin_email_id);
            $processaffiliatetransaction->changeAffiliateStatus($approvedTransaction, $em);
        endif;
    }

    /*public function sendAdminAlert($alert, $subject , $email = 'edelaespada@omatech.com')*/
    /*public function sendAdminAlert($alert, $subject, $containerEmailObject)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('testing.testuser34@gmail.com')
            ->setTo('yogesh.makwana@iflair.com')
            ->setCharset('UTF-8')
            ->setContentType('text/html')
            ->setBody($alert);
        $containerEmailObject->send($message);
    }*/
}
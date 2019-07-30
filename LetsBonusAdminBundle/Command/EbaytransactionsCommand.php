<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;
use iFlair\LetsBonusAdminBundle\Entity\Network;
use PHPExcel_IOFactory;

require_once 'AppShell.php';

class EbaytransactionsCommand extends ContainerAwareCommand
{
    protected $statusMap = array('Winning Bid (Revenue)' => 'P', 'Winning Bid (Count)' => 'P', 'Bid / BIN' => 'P', 'CRU' => 'P', 'ACRU' => 'P');

    protected function configure()
    {
        $this->setName('network:ebay')->setDescription('Use to Store Sales Data to Database Of Commission Junction');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*define('EBAY_USR', $this->getContainer()->getParameter('ebay_user'));
        define('EBAY_PWD', $this->getContainer()->getParameter('ebay_password'));*/
        $em = $this->getContainer()->get('doctrine')->getManager();
        $ebay_obj = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findByName(Network::EBAY);
        $ebay_id = '';
        foreach ($ebay_obj as $ebay) {
            $ebay_id = $ebay->getId();
        }
        if (!empty($ebay_id)):
            $networkAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:networkCredentials')->findByNetwork(trim($ebay_id));
        foreach ($networkAvailable as $credentials) {
            $ebay_username = $credentials->getEbayUsername();
            $ebay_password = $credentials->getEbayPassword();

            $endDate = date('m/d/y');
            $startDate = Date('m/d/y', strtotime('-30 days'));

            $fileName = $this->getEbayTransactionsReports($startDate, $endDate, $ebay_username, $ebay_password);
            $this->processEbayTransactionsReports($fileName);
        } else:
            die('Network Entry Not Inserted');
        endif;
        /*$this->deleteEbayTransactionsReports($fileName);*/
    }
    private function getEbayTransactionsReports($startDate, $endDate, $ebay_username, $ebay_password)
    {
        $reportURL = 'https://publisher.ebaypartnernetwork.com/PublisherReportsTx?pt=2&user_name='.$ebay_username.'&user_password='.$ebay_password."&start_date=$startDate&end_date=$endDate&submit_excel=1";
        $fileName = 'ebayreport_'.date('Y-m-d').'.xlsx';
        $url = $reportURL;

        $dir = $this->getContainer()->getParameter('kernel.cache_dir').'/EbayData';
        //$dir = __DIR__ . "/EbayData";

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $name = trim($fileName);

        is_dir($dir) || @mkdir($dir) || die("Can't Create folder");

        $source = $reportURL;
        $target = $dir.DIRECTORY_SEPARATOR.$name;

        $fileSystem = new FileSystem();
        $fileDownloaded = $fileSystem->copy($source, $target);

        $fileSystem->chmod($target, 0777);

        return $target;
    }
    private function processEbayTransactionsReports($fileName)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $admin_email_id = $container->getParameter('from_send_email_id');

        $containerEmailObject = $this->getContainer()->get('mailer');
        $sendEmail = new \AppShell();

        if (!file_exists($fileName)):
            return false;
        endif;

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($fileName);
        $objWorksheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        unset($objWorksheet[1]);
        if (!empty($objWorksheet)):
            $count = 0;

        $em = $this->getContainer()->get('doctrine')->getManager();

        foreach ($objWorksheet as $index => $saleItem):
                if (trim(strtolower($saleItem['C'])) == trim(strtolower('Winning Bid (Revenue)'))):

                    if (!$this->alreadySaved($saleItem, $em)):
                            $ebayEntity = new LetsBonusTransactions();
        $this->saveEbayTransaction($saleItem, $ebayEntity, $em); else:
                        echo "Already saved\n";
        endif; elseif (!empty($saleItem['U'])):
                    $sendEmail->sendAdminAlert('Error: Ebay transactions '.$saleItem['S'].'<br/> type != Winning Bid (Revenue) && Ganancia > 0', 'EbaytransactionsShell processEbayTransactionsReports ', $containerEmailObject, $admin_email_id);
        endif;

        endforeach;
        endif;
    }
    private function alreadySaved($saleItem, $em)
    {
        $transactionId = $saleItem['S'].'-'.$saleItem['M'];
        $status = $this->getFinalStatus($saleItem);

        $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findBy(array(
            'transactionId' => trim($transactionId),
            'programId' => (string) $saleItem['E'],
        ));

        if (!empty($alreadyAvailable)):
            return true;
        endif;

        return false;
    }

    public function saveEbayTransaction($saleItem, $ebayEntity, $em)
    {
        $transactionId = $saleItem['S'].'-'.$saleItem['M'];

        $ebayEntity->setTransactionId($transactionId);
        $ebayEntity->setCurrency($this->getEbayCurrency('EUR'));
        $ebayEntity->setNetwork($this->getEbayNetwork());
        $status = $this->getFinalStatus($saleItem);
        if ($status) {
            $ebayEntity->setStatus((string) $status);
        } else {
            $ebayEntity->setStatus(null);
        }
        $ebayEntity->setStatusName(null);
        if ($saleItem['C']) {
            $ebayEntity->setStatusState($saleItem['C']);
        } else {
            $ebayEntity->setStatusState(null);
        }
        $ebayEntity->setStatusMessage(null);
        if ($saleItem['P']) {
            $ebayEntity->setAmount(round((string) $saleItem['P'], 2));
        } else {
            $ebayEntity->setAmount(0);
        }
        if ($saleItem['U']) {
            $ebayEntity->setCommission(round((string) $saleItem['U'], 2));
        } else {
            $ebayEntity->setCommission(0);
        }
        if ($saleItem['B']) {
            $ebayEntity->setTrackingDate((string) $saleItem['B']);
        } else {
            $ebayEntity->setTrackingDate(null);
        }
        if ($saleItem['L']) {
            $ebayEntity->setClickDate($saleItem['L']);
        } else {
            $ebayEntity->setClickDate(null);
        }
        if ($saleItem['M']) {
            $ebayEntity->setClickInId($saleItem['M']);
        } else {
            $ebayEntity->setClickInId(null);
        }
        if ($saleItem['E']) {
            $ebayEntity->setProgramId($saleItem['E']);
        } else {
            $ebayEntity->setProgramId(null);
        }
        if ($saleItem['F']) {
            $ebayEntity->setProgramName($saleItem['F']);
        } else {
            $ebayEntity->setProgramName(null);
        }
        if ($saleItem['W']) {
            $ebayEntity->setProductName($saleItem['W']);
        } else {
            $ebayEntity->setProductName(null);
        }
        if ($saleItem['P']) {
            $ebayEntity->setorderValue($saleItem['P']);
        } else {
            $ebayEntity->setorderValue(null);
        }

        $customId = $saleItem['K'];
        if (!empty($customId)):
            $params = explode('-', $customId);
        $ebayEntity->setParam0($params[0], $params[0]);
        $ebayEntity->setParam1($params[1], $params[1]);
        $ebayEntity->setParam2($params[2], $params[2]);
        if ($this->getEbayShopHistory($params[2])) {
            $ebayEntity->setShopHistory($this->getEbayShopHistory($params[2]));
        } else {
            $ebayEntity->setShopHistory(null);
        }
        endif;

        $ebayEntity->setProcessed('PENDING');
        $ebayEntity->setProcessedDate(new \DateTime());
        $em->persist($ebayEntity);
        $em->flush();
    }

    protected function getEbayNetwork()
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName(Network::EBAY);
    }

    protected function getEbayCurrency($CurrencyCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Currency')
            ->findOneByCode($CurrencyCode);
    }

    private function getFinalStatus($saleItem)
    {
        $status = $this->statusMap[$saleItem['C']];

        return $status;
    }

    protected function getEbayShopHistory($ShophistoryCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:shopHistory')
            ->findOneById($ShophistoryCode);
    }
}

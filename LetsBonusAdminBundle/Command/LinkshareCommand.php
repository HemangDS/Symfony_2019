<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Network;
use iFlair\LetsBonusAdminBundle\Entity\FrontUser;
use iFlair\LetsBonusTransactions\Entity\shopHistory;

class LinkshareCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:linkshare')->setDescription('Use to Store Sales Data to Database Of LinkShare');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $endDate = date('Ymd');
        $startDate = Date('Ymd', strtotime('-90 days'));
        $reportPath = $this->getLinkShareTransactionsReports($startDate, $endDate); // Download file on auto created linkshare folder
        $this->processLinkShareTransactionsReports($reportPath); // Save Data into database
    }
    private function getLinkShareTransactionsReports($startDate, $endDate)
    {
        $reportURL = "https://reportws.linksynergy.com/downloadreport.php?bdate=$startDate&edate=$endDate&token=ca9dd1fb40e6b0f402fae20002d5ad2b9e11830361526de7d3d104d8832212f6&reportid=12&lang=es&locale=es";

        $fileName = 'linksharereport_'.date('Y-m-d').'.csv';

        $lsdir = $this->getContainer()->getParameter('kernel.cache_dir').'/LinkShareData';
        //$lsdir = __DIR__ . "/LinkShareData";
        if (!file_exists($lsdir)) {
            mkdir($lsdir, 0777, true);
        }

        is_dir($lsdir) || @mkdir($lsdir) || die("Can't Create folder");

        $source = $reportURL;
        $target = $lsdir.DIRECTORY_SEPARATOR.$fileName;

        $fileSystem = new FileSystem();
        $fileDownloaded = $fileSystem->copy($source, $target);

        $fileSystem->chmod($target, 0777);

        if (file_exists($target)) {
            return $target;
        } else {
            die('File Not Exists..!!!');
        }
    }
    private function processLinkShareTransactionsReports($fileName)
    {
        if (!file_exists($fileName)) {
            die('File Not Exists..!!!');
        }

        $file = fopen($fileName, 'r');
        $header = fgetcsv($file);

        $row = array();
        while ($line = fgetcsv($file)) {
            $row = array_combine($header, $line);
            if (!$this->alreadySaved($row['Order ID'])) {
                $this->saveLinkShareTransaction($row);
            } else {
                echo "Already saved\n";
            }
        }
        fclose($file);
        unlink($fileName);
    }
    private function alreadySaved($transactionId)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findByTransactionId(trim($transactionId));
        if (empty($alreadyAvailable)) {
            return false;
        } else {
            return true;
        }
    }
    private function saveLinkShareTransaction($row)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $linkshareEntity = new LetsBonusTransactions();

        //------------------------------------------
        if (!empty($row['Transaction Date']) && !empty($row['Transaction Time'])) {
            $transDateTime = \DateTime::createFromFormat('d/m/Y H:i', $row['Transaction Date'].' '.$row['Transaction Time']);
            $transDateTime = (array) $transDateTime;
            $transctionData = explode('.', $transDateTime['date']);
            $transactionDate = $transctionData[0]; // Click Date :: Transaction Date
        } else {
            $transactionDate = '';
        }
        //------------------------------------------
        if (!empty($row['Process Date']) && !empty($row['Process Time'])) {
            $processDate = \DateTime::createFromFormat('d/m/Y H:i', $row['Process Date'].' '.$row['Process Time']);
            $processDateTime = (array) $processDate;
            $processData = explode('.', $processDateTime['date']);
            $processDate = $processData[0];
        } else {
            $processDate = '';
        }

        /*$processDate = date_create_from_format("d/m/Y H:i", $row['Process Date'].' '.$row['Process Time']);*/
        /*$processDate = date("d/m/Y H:i", $row['Process Date'].' '.$row['Process Time']);*/

        $date = $row['Process Date'];
        $time = $row['Process Time'];

        $processDate = date('d/m/Y H:i', strtotime("$date $time"));

        /*print_r($processDate);
        die();*/
        if (!empty($processDate)):
            $linkshareEntity->setTrackingDate($processDate); else:
            $linkshareEntity->setTrackingDate('');
        endif;

        if (!empty($row['Member ID'])) {
            $paramValues = $row['Member ID'];
            $paramValues = explode('-', $paramValues);
            $param0 = $paramValues[0];
            $param1 = $paramValues[1];
            $param2 = $shophistoryId = $paramValues[2];
        } else {
            $param0 = 0;
            $param1 = 0;
            $param2 = 0;
        }

        if ($row['Order ID']) {
            $linkshareEntity->setTransactionId($row['Order ID']);
        } else {
            $linkshareEntity->setTransactionId(0);
        }

        $linkshareEntity->setReferenceId(0, 0); // Not Available

        if ($row['Sales']) {
            $linkshareEntity->setAmount(round((string) $row['Sales'], 2));
        } else {
            $linkshareEntity->setAmount(0);
        }

        // $linkshareEntity->setTotalPurchase(0, 0); // Not Available

        if ($row['Commissions']) {
            $linkshareEntity->setCommission(round((string) $row['Commissions'], 2));
        } else {
            $linkshareEntity->setCommission(0);
        }

        $linkshareEntity->setParam0($param0, $param0);
        $linkshareEntity->setParam1($param1, $param1);
        $linkshareEntity->setParam2($param2, $param2);
        $linkshareEntity->setShopHistory($this->getLinkshareShopHistory($param2, $param2));

        $linkshareEntity->setCurrency($this->getLinkshareCurrency('EUR'));

        if (!empty($row['SKU Number'])):
            $linkshareEntity->setProductName($row['SKU Number']); else:
            $linkshareEntity->setProductName('');
        endif;

        /*program_name*/
        if (!empty($row['Merchant Name'])):
            $linkshareEntity->setProgramName($row['Merchant Name']); else:
            $linkshareEntity->setProgramName('');
        endif;

        $linkshareEntity->setStatus('confirmed');
        $linkshareEntity->setProcessed('PENDING');
        $linkshareEntity->setProcessedDate(new \DateTime());
        $linkshareEntity->setNetwork($this->getLinkshareNetwork());
        $em->persist($linkshareEntity);
        $em->flush();
    }
    protected function getLinkshareCurrency($CurrencyCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Currency')
            ->findOneByCode($CurrencyCode);
    }
    protected function getLinkshareShopHistory($ShophistoryCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:shopHistory')
            ->findOneById($ShophistoryCode);
    }
    protected function getLinkshareUser($UserCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:FrontUser')
            ->findOneById($UserCode);
    }
    protected function getLinkshareNetwork()
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName(Network::LINKSHARE);
    }
}

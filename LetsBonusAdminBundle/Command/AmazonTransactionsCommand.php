<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Network;

class AmazonTransactionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:amazon')->setDescription('Use to Store Sales Data to Database Of Commission Junction');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        define('AMAZON_USR', $this->getContainer()->getParameter('amazon_user'));
        define('AMAZON_PWD', $this->getContainer()->getParameter('amazon_password'));
        $em = $this->getContainer()->get('doctrine')->getManager();
        $amazon_obj = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findByName(Network::AMAZON);
        $amazon_id = '';
        foreach ($amazon_obj as $amazon) {
            echo $amazon_id = $amazon->getId();
        }
        if (!empty($amazon_id)):
            $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:networkCredentials')->findByNetwork($amazon_id);
        foreach ($alreadyAvailable as $credentials) {
            $amazon_user = $credentials->getAmazonUsername();
            $amazon_password = $credentials->getAmazonPassword();

            $endDate = date('m/d/y');
            $startDate = Date('m/d/y', strtotime('-30 days'));

            $amazonDir = $this->getContainer()->getParameter('kernel.cache_dir').'/amazon-files';
                //$amazonDir = __DIR__ . "/amazon-files"; // Full Path

                if (!file_exists($amazonDir)) {
                    mkdir($amazonDir, 0777, true);
                }

            $totalRevenues = $totalEarnings = $totalItems = 0;
            $numDays = 30;
            $noProcessedDays = 8;
            $numDays = $numDays - $noProcessedDays;

            for ($i = 0; $i <= $numDays; ++$i) {
                $originalDate = $startDate;
                $newStartDate = date('Ymd', strtotime($originalDate));
                $day = $newStartDate;

                if (file_exists("$amazonDir/cashlets-21-earnings-report-$day.xml")) {
                    unlink("$amazonDir/cashlets-21-earnings-report-$day.xml");
                }

                $cmd = "wget --quiet --output-document $amazonDir/cashlets-21-earnings-report-$day.xml.gz --http-user=".trim($amazon_user).' --http-password='.trim($amazon_password)." https://assoc-datafeeds-eu.amazon.com/datafeed/getReport?filename=cashlets-21-earnings-report-$day.xml.gz";

                exec($cmd, $output, $return);

                if (file_exists("$amazonDir/cashlets-21-earnings-report-$day.xml.gz")) {
                    $cmd = "gunzip $amazonDir/cashlets-21-earnings-report-$day.xml.gz";
                    exec($cmd, $output, $return);
                }

                $xmlTransactions = @simplexml_load_file("$amazonDir/cashlets-21-earnings-report-$day.xml");

                if ($xmlTransactions) {
                    $uniqueTransactions = array();
                    $categoriesArray = array();
                    foreach ($xmlTransactions->ProductLine_array->ProductLine as $xmlCategory) {
                        $categoriesArray[(int) $xmlCategory->GLProductGroupID] = (string) $xmlCategory->ProductGroupName;
                    }

                    $em = $this->getContainer()->get('doctrine')->getManager();

                    foreach ($xmlTransactions->Items->Item as $item) {
                        $dia = date('Y-m-d', strtotime($item['Date']));
                        $diacode = date('Ymd', strtotime($item['Date']));
                        $category = (int) $item['Category'];
                        $categoryName = $categoriesArray[$category];
                        $price = str_replace(',', '.', $item['Price']);
                        $quantity = $item['Qty'];
                        $earnings = str_replace(',', '.', $item['Earnings']);
                        $rate = str_replace(',', '.', $item['Rate']);
                        $revenue = str_replace(',', '.', $item['Revenue']);
                        $seller = $item['Seller'];
                        $subtag = $item['SubTag'];
                        $totalRevenues += $revenue;
                        $totalEarnings += $earnings;
                        ++$totalItems;
                        $dupeTransaction = 0;
                        $transactionId = "$diacode|$category|$revenue|$earnings|$dupeTransaction|$subtag";
                        while (in_array($transactionId, $uniqueTransactions)) {
                            ++$dupeTransaction;
                            $transactionId = "$diacode|$category|$revenue|$earnings|$dupeTransaction|$subtag";
                        }
                        $uniqueTransactions[] = $transactionId;
                              // echo "$transactionId: $dia $subtag $category $categoryName $seller $price $quantity  $revenue $rate *$earnings*\n";
                              $saleItem = array();
                        $saleItem['transactionID'] = $transactionId;
                        $saleItem['dia'] = $dia;
                        $saleItem['diacode'] = $diacode;
                        $saleItem['category'] = $category;
                        $saleItem['categoryName'] = $categoryName;
                        $saleItem['price'] = $price;
                        $saleItem['quantity'] = $quantity;
                        $saleItem['earnings'] = $earnings;
                        $saleItem['rate'] = $rate;
                        $saleItem['revenue'] = $revenue;
                        $saleItem['seller'] = $seller;
                        $saleItem['subtag'] = $subtag;

                        $amazonEntity = new LetsBonusTransactions();

                        $alreadyAvailable = $em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findByTransactionId(trim($saleItem['transactionID']));

                        if (empty($alreadyAvailable)) {
                            if ($saleItem['transactionID']) {
                                $amazonEntity->setTransactionId($saleItem['transactionID'], $saleItem['transactionID']);
                            } else {
                                $amazonEntity->setTransactionId(0);
                            }
                            $amazonEntity->setReferenceId('');
                            $amazonEntity->setCurrency($this->getAmazonCurrency('EUR'));
                            if ($saleItem['revenue']) {
                                $amazonEntity->setAmount(round((string) $saleItem['revenue'], 2));
                            } else {
                                $amazonEntity->setAmount(0);
                            }
                            if ($saleItem['earnings']) {
                                $amazonEntity->setCommission(round((string) $saleItem['earnings'], 2));
                            } else {
                                $amazonEntity->setCommission(0);
                            }
                            if ($saleItem['dia']) {
                                $amazonEntity->setTrackingDate($saleItem['dia']);
                            } else {
                                $amazonEntity->setTrackingDate(null);
                            }
                            if ($saleItem['category']) {
                                $amazonEntity->setProgramId($saleItem['category']);
                            } else {
                                $amazonEntity->setProgramId(null);
                            }
                            if ($saleItem['categoryName']) {
                                $amazonEntity->setProgramName($saleItem['categoryName']);
                            } else {
                                $amazonEntity->setProgramName(null);
                            }
                            if ($saleItem['categoryName']) {
                                $amazonEntity->setProductName($saleItem['categoryName']);
                            } else {
                                $amazonEntity->setProductName(null);
                            }
                            $amazonEntity->setStatus('confirmed');
                            $customId = $saleItem['subtag'];
                            if (empty($customId) || $customId == '') {
                                $param0 = null;
                                $param1 = null;
                                $param2 = null;
                                $shophistoryId = null;
                            } else {
                                $params = explode('-', $customId);
                                $param0 = $params[0];
                                $param1 = $params[1];
                                $param2 = $shophistoryId = $params[2];
                            }
                            $amazonEntity->setNetwork($this->getAmazonNetwork());
                            $amazonEntity->setParam0($param0, $param0);
                            $amazonEntity->setParam1($param1, $param1);
                            $amazonEntity->setParam2($param2, $param2);
                            $amazonEntity->setShopHistory($this->getAmazonShopHistory($param2, $param2));
                            $amazonEntity->setProcessed('PENDING');
                            $amazonEntity->setProcessedDate(new \DateTime());
                            $em->persist($amazonEntity);
                            $em->flush();
                        }
                    }
                }

                unlink("$amazonDir/cashlets-21-earnings-report-$day.xml");
                $startDate = date('m/d/y', strtotime('+1 day', strtotime($startDate)));
            }
        } else:
            die('Network Not Available');
        endif;
    }
    protected function getAmazonNetwork()
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName(Network::AMAZON);
    }
    protected function getAmazonCurrency($CurrencyCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Currency')
            ->findOneByCode($CurrencyCode);
    }
    protected function getAmazonShopHistory($ShophistoryCode)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:shopHistory')
            ->findOneById($ShophistoryCode);
    }
}

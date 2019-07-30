<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Companies;
use iFlair\LetsBonusAdminBundle\Entity\Currency;
use iFlair\LetsBonusAdminBundle\Entity\Language;
use iFlair\LetsBonusAdminBundle\Entity\Network;
use iFlair\LetsBonusAdminBundle\Entity\Voucher;
use iFlair\LetsBonusAdminBundle\Entity\Tags;
use iFlair\LetsBonusAdminBundle\Entity\Groups;
use iFlair\LetsBonusAdminBundle\Entity\Administrator;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;
use iFlair\LetsBonusAdminBundle\Entity\Variation;
use iFlair\LetsBonusAdminBundle\Entity\FrontUser;
use iFlair\LetsBonusAdminBundle\Entity\parentCategory;
use iFlair\LetsBonusAdminBundle\Entity\Collection;
use iFlair\LetsBonusAdminBundle\Entity\Clicks;
use iFlair\LetsBonusAdminBundle\Entity\Searchlogs;
use iFlair\LetsBonusAdminBundle\Entity\cashbackSettings;
use iFlair\LetsBonusAdminBundle\Entity\cashbackTransactions;
use iFlair\LetsBonusAdminBundle\Entity\LetsBonusTransactions;
use Application\Sonata\MediaBundle\Entity\Media;
use \DateTime as DateTime;

class dataMigrationUserSpecificAffiliationCommand extends ContainerAwareCommand
{
    private $em;

    protected function configure()
    {
        $this->setName('affiliate:migration')->setDescription('Data Migrations');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        $this->em = $this->getContainer()->get('doctrine')->getManager('default');

        //$letsbonusUsers = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Users', 'letbonus')->findBy(array(),array(),20);
        //$letsbonusUsers = array(14,730,4671,4839,8280,10136,11373,11857,12635,16807,21535,21926,23343,25776,26224,27405,28814,11639,6448);
        /*$letsbonusUsers = array(14,730,4671,4839,8280,10136,11373,11857,12635,16807,21535,21926,23343,25776,26224,27405,28814);
        $letsbonusUsers = array(6448,11639);*/
        $letsbonusUsers = array(12118285,479199);
        foreach ($letsbonusUsers as $letsbonusUserId) {
            $shoppidayUser = $this->em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id'=>$letsbonusUserId));

            $letsbonusAffiliateTransactions = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Affiliatetransactions', 'letbonus')
                                    ->createQueryBuilder('c')
                                    //->select('DISTINCT c.transactionid, c.id')
                                    ->select('c')
                                    ->where('c.param0 = :param0')
                                    ->setParameter('param0', $letsbonusUserId)
                                    ->getQuery()
                                    ->getResult();
            /*$letsbonusAffiliateTransactions = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Affiliatetransactions', 'letbonus')->findBy(array('param0'=>$letsbonusUserId));*/
            if(count($letsbonusAffiliateTransactions)>0){
                foreach ($letsbonusAffiliateTransactions as $letsbonusAffiliateTransactionChild) {
                    $checkShoppidayLetsBonusTransaction = $this->em->getRepository('iFlairLetsBonusAdminBundle:LetsBonusTransactions')->findOneBy(array('id'=>$letsbonusAffiliateTransactionChild->getId()));
                    if(!$checkShoppidayLetsBonusTransaction) {
                        $shoppidayLetsBonusTransaction = new LetsBonusTransactions();
                        $shoppidayLetsBonusTransaction->setId($letsbonusAffiliateTransactionChild->getId());
                        $shoppidayLetsBonusTransaction->setTransactionId($letsbonusAffiliateTransactionChild->getTransactionid());
                        $shoppidayLetsBonusTransaction->setReferenceId($letsbonusAffiliateTransactionChild->getReferenceId());
                        $shoppidayLetsBonusTransaction->setAmount($letsbonusAffiliateTransactionChild->getAmount());
                        $shoppidayLetsBonusTransaction->setCommission($letsbonusAffiliateTransactionChild->getCommission());
                        $shoppidayLetsBonusTransaction->setStatus($letsbonusAffiliateTransactionChild->getStatus());
                        $shoppidayLetsBonusTransaction->setStatusName($letsbonusAffiliateTransactionChild->getStatusName());
                        $shoppidayLetsBonusTransaction->setStatusState($letsbonusAffiliateTransactionChild->getStatusState());
                        $shoppidayLetsBonusTransaction->setStatusMessage($letsbonusAffiliateTransactionChild->getStatusMessage());
                        $shoppidayLetsBonusTransaction->setLeadNumber($letsbonusAffiliateTransactionChild->getLeadnumber());
                        $shoppidayLetsBonusTransaction->setProcessed($letsbonusAffiliateTransactionChild->getProcessed());
                        $shoppidayLetsBonusTransaction->setProcessedDate($letsbonusAffiliateTransactionChild->getProcessDate());
                        $shoppidayLetsBonusTransaction->setDaystoautoapprove($letsbonusAffiliateTransactionChild->getDaystoautoapprove());
                        $shoppidayLetsBonusTransaction->setParam0($shoppidayUser->getId());
                        $shoppidayLetsBonusTransaction->setParam1($letsbonusAffiliateTransactionChild->getParam1());
                        $shoppidayLetsBonusTransaction->setParam2($letsbonusAffiliateTransactionChild->getParam2());
                        if ($letsbonusAffiliateTransactionChild->getNetworkId() != NULL) {
                            $letsbonusNetworks = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Networks', 'letbonus')->findOneBy(array('id' => $letsbonusAffiliateTransactionChild->getNetworkId()));
                            $shoppidayNetwork = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('name' => $letsbonusNetworks->getName()));
                            if ($shoppidayNetwork) {
                                $shoppidayLetsBonusTransaction->setNetwork($shoppidayNetwork);
                            } else {
                                $shoppidayLetsBonusTransaction->setNetwork(NULL);
                            }
                        } else {
                            $shoppidayLetsBonusTransaction->setNetwork(NULL);
                        }
                        $shoppidayCurrancy = $this->em->getRepository('iFlairLetsBonusAdminBundle:Currency')->findOneBy(array('code' => $letsbonusAffiliateTransactionChild->getCurrency()));
                        if ($shoppidayCurrancy) {
                            $shoppidayLetsBonusTransaction->setCurrency($shoppidayCurrancy);
                        } else {
                            $shoppidayLetsBonusTransaction->setCurrency(NULL);
                        }
                        $shoppidayLetsBonusTransaction->setCreated($letsbonusAffiliateTransactionChild->getCreated());
                        $shoppidayLetsBonusTransaction->setModified($letsbonusAffiliateTransactionChild->getModified());
                        $shoppidayLetsBonusTransaction->setClickDate($letsbonusAffiliateTransactionChild->getClickdate());
                        $shoppidayLetsBonusTransaction->setClickId($letsbonusAffiliateTransactionChild->getClickid());
                        $shoppidayLetsBonusTransaction->setClickInId($letsbonusAffiliateTransactionChild->getClickinid());
                        $shoppidayLetsBonusTransaction->setTrackingDate($letsbonusAffiliateTransactionChild->getTrackingdate());
                        $shoppidayLetsBonusTransaction->setTrackingUrl($letsbonusAffiliateTransactionChild->getTrackingurl());
                        $shoppidayLetsBonusTransaction->setOrderNumber($letsbonusAffiliateTransactionChild->getOrdernumber());
                        $shoppidayLetsBonusTransaction->setOrderValue($letsbonusAffiliateTransactionChild->getOrdervalue());
                        $shoppidayLetsBonusTransaction->setProgramId($letsbonusAffiliateTransactionChild->getProgramId());
                        $shoppidayLetsBonusTransaction->setProgramName($letsbonusAffiliateTransactionChild->getProgramName());
                        $shoppidayShopHistory = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $letsbonusAffiliateTransactionChild->getShopshistoryId()));
                        if ($shoppidayShopHistory) {
                            $shoppidayLetsBonusTransaction->setShopHistory($shoppidayShopHistory);
                        } else {
                            $shoppidayLetsBonusTransaction->setShopHistory(NULL);
                        }
                        $shoppidayLetsBonusTransaction->setModifiedDate($letsbonusAffiliateTransactionChild->getModifieddate());
                        $shoppidayLetsBonusTransaction->setProductName($letsbonusAffiliateTransactionChild->getProductname());
                        $this->em->persist($shoppidayLetsBonusTransaction);
                        $metadata = $this->em->getClassMetaData(get_class($shoppidayLetsBonusTransaction));
                        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
                        $this->em->flush();
                    }
                }
            }
        }        
    }    
}

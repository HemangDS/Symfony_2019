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

class dataMigrationUserSpecificCashbackCommand extends ContainerAwareCommand
{
    private $em;

    protected function configure()
    {
        $this->setName('cashback:migration')->setDescription('Data Migrations');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        $this->em = $this->getContainer()->get('doctrine')->getManager('default');

        //$letsbonusUsers = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Users', 'letbonus')->findBy(array(),array(),20);
        //$letsbonusUsers = array(14,730,4671,4839,6448,8280,10136,11373,11639,11857,12635,16807,21535,21926,23343,25776,26224,27405,28814);
        $letsbonusUsers = array(12118285,479199);
        foreach ($letsbonusUsers as $letsbonusUserId) {
            $shoppidayUser = $this->em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id'=>$letsbonusUserId));
            
            $letsbonusCashbackTransactions = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Cashbacktransactions', 'letbonus')->findBy(array('userId'=>$letsbonusUserId));
            if(count($letsbonusCashbackTransactions)>0){
                foreach ($letsbonusCashbackTransactions as $letsbonusCashbackTransaction) {
                    $shoppidayCashbackTransaction = new cashbackTransactions();
                    $shoppidayCashbackTransaction->setId($letsbonusCashbackTransaction->getId());
                    $shoppidayShop = $this->em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id'=>$letsbonusCashbackTransaction->getShopId()));
                    if($shoppidayShop){
                        $shoppidayCashbackTransaction->setShopId($shoppidayShop);
                    }else{
                        $shoppidayCashbackTransaction->setShopId(NULL);
                    }
                    $shoppidayShopHistory = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id'=>$letsbonusCashbackTransaction->getShopshistoryId()));
                    if($shoppidayShopHistory){
                        $shoppidayCashbackTransaction->setShopHistory($shoppidayShopHistory);
                    }else{
                        $shoppidayCashbackTransaction->setShopHistory(NULL);
                    }
                    $shoppidayCashbackTransaction->setUserId($shoppidayUser->getId());
                    $shoppidayCashbackTransaction->setTransactionId($letsbonusCashbackTransaction->getTransactionId());
                    if($letsbonusCashbackTransaction->getNetworkId()!=NULL){
                        $letsbonusNetworks = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Networks', 'letbonus')->findOneBy(array('id'=>$letsbonusCashbackTransaction->getNetworkId()));
                        $shoppidayNetwork = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('name'=>$letsbonusNetworks->getName()));
                        if($shoppidayNetwork){
                            $shoppidayCashbackTransaction->setNetworkId($shoppidayNetwork);
                        }else{
                            $shoppidayCashbackTransaction->setNetworkId(NULL);
                        }
                    }else{
                        $shoppidayCashbackTransaction->setNetworkId(NULL);
                    }                    
                    $shoppidayCashbackTransaction->setAmount($letsbonusCashbackTransaction->getAmount());
                    $shoppidayCashbackTransaction->setAffiliateAmount($letsbonusCashbackTransaction->getAffiliateAmount());
                    $shoppidayCashbackTransaction->setTotalAffiliateAmount($letsbonusCashbackTransaction->getTotalAffiliateAmount());
                    $shoppidayCashbackTransaction->setLetsbonusPct($letsbonusCashbackTransaction->getLetsbonusPct());
                    $shoppidayCashbackTransaction->setExtraAmount($letsbonusCashbackTransaction->getExtraAmount());
                    $shoppidayCashbackTransaction->setExtraPct($letsbonusCashbackTransaction->getExtraPct());
                    $shoppidayCurrancy = $this->em->getRepository('iFlairLetsBonusAdminBundle:Currency')->findOneBy(array('code'=>$letsbonusCashbackTransaction->getCurrency()));
                    if($shoppidayCurrancy){
                        $shoppidayCashbackTransaction->setCurrency($shoppidayCurrancy);
                    }else{
                        $shoppidayCashbackTransaction->setCurrency(NULL);
                    }
                    $shoppidayCashbackTransaction->setStatus($letsbonusCashbackTransaction->getStatus());
                    $shoppidayCashbackTransaction->setType($letsbonusCashbackTransaction->getType());
                    $shoppidayCashbackTransaction->setNetworkStatus($letsbonusCashbackTransaction->getNetworkStatus());
                    $shoppidayCashbackTransaction->setOrderReference($letsbonusCashbackTransaction->getOrderReference());
                    $shoppidayCashbackTransaction->setAffiliateAproveddate($letsbonusCashbackTransaction->getAffiliateAproveddate());
                    if($letsbonusCashbackTransaction->getAffiliateCanceldate()->getTimestamp()<=0){
                        $shoppidayCashbackTransaction->setAffiliateCanceldate(new \DateTime(date('Y-m-d H:i:s')));
                    }else{
                        $shoppidayCashbackTransaction->setAffiliateCanceldate($letsbonusCashbackTransaction->getAffiliateCanceldate());
                    }
                    if($letsbonusCashbackTransaction->getAprovalDate()->getTimestamp()<=0){
                        $shoppidayCashbackTransaction->setAprovalDate(new \DateTime(date('Y-m-d H:i:s')));
                    }else{
                        $shoppidayCashbackTransaction->setAprovalDate($letsbonusCashbackTransaction->getAprovalDate());
                    }
                    $shoppidayCashbackTransaction->setDate($letsbonusCashbackTransaction->getDate());
                    $shoppidayCashbackTransaction->setUserName($letsbonusCashbackTransaction->getUserName());
                    $shoppidayCashbackTransaction->setUserAddress($letsbonusCashbackTransaction->getUserAddress());
                    $shoppidayCashbackTransaction->setUserDni($letsbonusCashbackTransaction->getUserDni());
                    $shoppidayCashbackTransaction->setUserPhone($letsbonusCashbackTransaction->getUserPhone());
                    $shoppidayCashbackTransaction->setUserBankAccountNumber($letsbonusCashbackTransaction->getUserBankAccountNumber());
                    $shoppidayCashbackTransaction->setBic($letsbonusCashbackTransaction->getBic());
                    $shoppidayCompanies = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(array('id'=>$letsbonusCashbackTransaction->getCompanyId()));
                    if($shoppidayCompanies){
                        $shoppidayCashbackTransaction->setCompanyId($shoppidayCompanies);
                    }else{
                        $shoppidayCashbackTransaction->setCompanyId(NULL);
                    }
                    $shoppidayCashbackTransaction->setCashbacktransactionsChilds($letsbonusCashbackTransaction->getCashbacktransactionsChilds());
                    $shoppidayCashbackTransaction->setAdminuserId($letsbonusCashbackTransaction->getAdminuserId());
                    $shoppidayCashbackTransaction->setManualNumdaystoapprove($letsbonusCashbackTransaction->getManualNumdaystoapprove());
                    $shoppidayCashbackTransaction->setComments($letsbonusCashbackTransaction->getComments());
                    $shoppidayCashbackTransaction->setParentTransactionId($letsbonusCashbackTransaction->getParentTransactionId());
                    if($letsbonusCashbackTransaction->getCashbacksettingId()!=0) {
                        $shoppidayCashbackSettings = $this->em->getRepository('iFlairLetsBonusAdminBundle:cashbackSettings')->findOneBy(array('id'=>$letsbonusCashbackTransaction->getCashbacksettingId()));
                        if($shoppidayCashbackSettings){
                            $shoppidayCashbackTransaction->setCashbacksettingId($shoppidayCashbackSettings);
                        }else{
                            $shoppidayCashbackTransaction->setCashbacksettingId(NULL);
                        }
                    }else{
                        $shoppidayCashbackTransaction->setCashbacksettingId(NULL);
                    }
                    $shoppidayCashbackTransaction->setSepageneratedbyUserId($letsbonusCashbackTransaction->getSepageneratedbyUserId());
                    $shoppidayCashbackTransaction->setSepageneratedDate($letsbonusCashbackTransaction->getSepageneratedDate());
                    $shoppidayCashbackTransaction->setDeviceType($letsbonusCashbackTransaction->getDeviceType());
                    $shoppidayCashbackTransaction->setCreated($letsbonusCashbackTransaction->getCreated());
                    $shoppidayCashbackTransaction->setModified($letsbonusCashbackTransaction->getModified());
                    $this->em->persist($shoppidayCashbackTransaction);
                    $metadata = $this->em->getClassMetaData(get_class($shoppidayCashbackTransaction));
                    $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
                    $this->em->flush();
                }
            }
        }        
    }    
}

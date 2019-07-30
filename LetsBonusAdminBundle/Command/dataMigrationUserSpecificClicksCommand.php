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

class dataMigrationUserSpecificClicksCommand extends ContainerAwareCommand
{
    private $em;

    protected function configure()
    {
        $this->setName('click:migration')->setDescription('Data Migrations');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        $this->em = $this->getContainer()->get('doctrine')->getManager('default');

        //$letsbonusUsers = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Users', 'letbonus')->findBy(array(),array(),20);
        //$letsbonusUsers = array(14,730,4671,4839,6448,8280,10136,11373,11639,11857,12635,16807,21535,21926,23343,25776,26224,27405,28814);
        $letsbonusUsers = array(12118285,479199);
        foreach ($letsbonusUsers as $letsbonusUserId) {
            $shoppidayUser = $this->em->getRepository('iFlairLetsBonusAdminBundle:FrontUser')->findOneBy(array('id'=>$letsbonusUserId));

            $letsbonusClicks = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Clicks', 'letbonus')->findBy(array('userId'=>$letsbonusUserId));
            if(count($letsbonusClicks)>0){
                foreach ($letsbonusClicks as $letsbonusClicks) {
                    $shoppidayClicks = new Clicks();
                    $shoppidayShop = $this->em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id'=>$letsbonusClicks->getShopId()));
                    if($shoppidayShop){
                        $shoppidayClicks->setShopId($shoppidayShop);
                    }else{
                        $shoppidayClicks->setShopId(NULL);
                    }
                    $shoppidayClicks->setUserId($shoppidayUser->getId());
                    $shoppidayShopHistory = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id'=>$letsbonusClicks->getShopshistoryId()));
                    if($shoppidayShopHistory){
                        $shoppidayClicks->setShopshistoryId($shoppidayShopHistory);
                    }else{
                        $shoppidayClicks->setShopshistoryId(NULL);
                    }
                    $shoppidayClicks->setType($letsbonusClicks->getType());
                    $shoppidayClicks->setTabType($letsbonusClicks->getTabType());
                    $shoppidayClicks->setTabId($letsbonusClicks->getTabId());
                    $shoppidayClicks->setTabPosition($letsbonusClicks->getTabPosition());
                    $shoppidayClicks->setIp($letsbonusClicks->getIp());
                    $shoppidayClicks->setUserAgent($letsbonusClicks->getUserAgent());
                    $shoppidayCompanies = $this->em->getRepository('iFlairLetsBonusAdminBundle:Companies')->findOneBy(array('id'=>$shoppidayClicks->getCompanyId()));
                    if($shoppidayCompanies){
                        $shoppidayClicks->setCompanyId($shoppidayCompanies);
                    }else{
                        $shoppidayClicks->setCompanyId(NULL);
                    }
                    $shoppidayClicks->setCreated($letsbonusClicks->getCreated());
                    $shoppidayClicks->setModified($letsbonusClicks->getModified());
                    $this->em->persist($shoppidayClicks);
                    $this->em->flush();
                }
            }
        }        
    }    
}

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

class dataMigrationUserSpecificCommand extends ContainerAwareCommand
{
    private $em;

    protected function configure()
    {
        $this->setName('user:migration')->setDescription('Data Migrations');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        $this->em = $this->getContainer()->get('doctrine')->getManager('default');

        //$letsbonusUsers = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Users', 'letbonus')->findBy(array(),array(),20);
        //$letsbonusUsers = array(14,730,4671,4839,6448,8280,10136,11373,11639,11857,12635,16807,21535,21926,23343,25776,26224,27405,28814);
        $letsbonusUsers = array(12118285,479199);
        foreach ($letsbonusUsers as $letsbonusUserId) {
            $letsbonusUser = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Users', 'letbonus')->findOneBy(array('id'=>$letsbonusUserId));
            $shoppidayUser = new FrontUser();
            $shoppidayUser->setId($letsbonusUser->getId());
            $shoppidayUser->setName($letsbonusUser->getName());
            $shoppidayUser->setAlias($letsbonusUser->getName());
            $shoppidayUser->setSurname($letsbonusUser->getSurname());
            $shoppidayUser->setEmail($letsbonusUser->getEmail());
            $shoppidayUser->setPassword(NULL);
            $shoppidayUser->setEnabled($letsbonusUser->getEnabled());
            $shoppidayUser->setIsShoppiday(0);
            $shoppidayUser->setApiFlag(1);
            $shoppidayUser->setCompanyId($letsbonusUser->getCompanyId());
            $shoppidayUser->setUserCreateDate($letsbonusUser->getUsercreatedate());
            $shoppidayUser->setUserType($letsbonusUser->getUsertype());
            $shoppidayUser->setUserGender($letsbonusUser->getUsergender());
            $shoppidayUser->setIsSubscribed(0);
            $shoppidayUser->setImage(NULL);
            $shoppidayUser->setUserBirthDate($letsbonusUser->getUserbirthdate());
            $shoppidayUser->setCity(NULL);
            $shoppidayUser->setLoginType(1);
            $shoppidayUser->setFacebookId(NULL);
            $shoppidayUser->setGoogleId(NULL);
            $this->em->persist($shoppidayUser);
            $metadata = $this->em->getClassMetaData(get_class($shoppidayUser));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $this->em->flush();
        }        
    }    
}

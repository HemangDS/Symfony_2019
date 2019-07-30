<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;
use iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Entity\Network;

class setMissingVoucherProgramsAndIntoShopsCommand extends ContainerAwareCommand
{
    private $em;
    protected function configure()
    {
        $this->setName('network:setMissingVoucherProgramsAndIntoShops')->setDescription('set latest shop history slug');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //test
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $this->em->getConnection();
        $shoppidayShops = $this->em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findBy(array('vprogram' => null));
        $shoppidayNetwork = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('name' => 'Zanox'));
        foreach ($shoppidayShops as $shoppidayShop) {
            $letsbonusShop = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Shops', 'letbonus')->findOneBy(array('id'=>$shoppidayShop->getId()));
            $letsbonusNetwork = $this->em->getRepository('iFlairLetsBonusMigrationBundle:Networks', 'letbonus')->findOneBy(array('id'=>$letsbonusShop->getNetworkId()));
            $shoppidayVoucherProgram = $this->em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms')->findOneBy(array('programName'=>$letsbonusShop->getBrand()));
            if(!$shoppidayVoucherProgram){
                $shoppidayNetwork = $this->em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('name' => $letsbonusNetwork->getName()));
                $voucherPrograms = new VoucherPrograms();
                $voucherPrograms->setNprogramId($letsbonusShop->getProgramId());
                $voucherPrograms->setProgramName($letsbonusShop->getBrand());
                $voucherPrograms->setNetwork($shoppidayNetwork);
                $this->em->persist($voucherPrograms);
                $this->em->flush();
                $shoppidayShop->setVprogram($voucherPrograms);
                $this->em->persist($shoppidayShop);
                $this->em->flush();
            }else{
                $shoppidayShop->setVprogram($shoppidayVoucherProgram);
                $this->em->persist($shoppidayShop);
                $this->em->flush();
            }
        }
    }
}
?>


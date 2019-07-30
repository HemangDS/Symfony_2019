<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\VoucherPrograms;
use iFlair\LetsBonusAdminBundle\Entity\ZanoxConfig;
use iFlair\LetsBonusAdminBundle\Entity\Network;

class ZanoxFetchBrandsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:zanoxfetchbrands')->setDescription('Zanox voucher brands = programs');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $zanoxConfigConnectId = $em->getRepository('iFlairLetsBonusAdminBundle:ZanoxConfig')->findOneByConnectId($this->getContainer()->getParameter('zenox_connect_id'));

        //To check if site added
        if (count($zanoxConfigConnectId) > 0) {
            $zanoxProgramsJSON = file_get_contents('http://api.zanox.com/json/2011-03-01/programs?region='.$this->getContainer()->getParameter('zenox_region').'&connectid='.$zanoxConfigConnectId->getConnectId());
            $zanoxPrograms = json_decode($zanoxProgramsJSON)->programItems->programItem;

            foreach ($zanoxPrograms as $zanoxProgram) {
                $zanoxProgram = (array) $zanoxProgram; //type casting due to @id in key
                $programId = $zanoxProgram['@id'];
                $voucherPrograms = $this->checkIFVoucherProgramExists($programId);
                if (!$voucherPrograms) {
                    //create new voucher program
                    $voucherPrograms = new VoucherPrograms();
                    $voucherPrograms->setNprogramId($programId);
                    $voucherPrograms->setProgramName($zanoxProgram['name']);
                    $voucherPrograms->setLogoPath($zanoxProgram['image']);
                    $voucherPrograms->setNetwork($this->getZanoxNetwork());
                    $em->persist($voucherPrograms);
                    $em->flush();
                }
            }
        }
    }

    protected function checkIFVoucherProgramExists($programId)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms')
            //->findOneByNprogramId($programId);
            ->findOneBy(array('nprogramId' => $programId, 'network' => $this->getZanoxNetwork()));
    }

    //Assuming name=TradeDoubler
    protected function getZanoxNetwork()
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName(Network::ZANOX);
    }
}

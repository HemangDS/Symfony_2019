<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;

class SetVoucherProgramsFromMigratedShopsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:SetVoucherProgramsFromMigratedShops')->setDescription('Set voucher probramm for migrated shop which has no voucher programm set');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT a.id FROM lb_shop AS a WHERE a.vprogram_id IS NULL');
        $query->execute();
        $data = $query->fetchAll();
        if(count($data)==0){
            echo 'No more migrated shops remain to set voucher program';
        }else {
            foreach ($data as $key => $value) {
                $shops = $em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findOneBy(array('id' => $value['id']));
                if ($shops) {
                    $network = $shops->getNetwork();
                    $VoucherPrograms = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms')->findOneBy(
                        array('network' => $network->getId()),
                        array('id' => 'ASC'),
                        0,
                        0
                    );
                    if($VoucherPrograms) {
                        $shops->setVprogram($VoucherPrograms);
                    }else{
                        $network = $em->getRepository('iFlairLetsBonusAdminBundle:Network')->findOneBy(array('id' => 1));
                        $VoucherPrograms = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms')->findOneBy(
                            array('network' => $network->getId()),
                            array('id' => 'ASC'),
                            0,
                            0
                        );
                        $shops->setVprogram($VoucherPrograms);
                    }
                    $em->persist($shops);
                    $em->flush();
                }
            }
            echo 'All migrated shops updated with first voucher program of that network';
        }
    }
}

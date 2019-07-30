<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\Voucher;

class VoucherProcessCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:voucherprocess')->setDescription('Process vouchers and expire at midnight');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d').' 00:00:00'.'-1 day'));
        $endDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d').' 23:59:59'.'-1 day'));
        $modifiedDate = new \DateTime();

        $em = $this->getContainer()->get('doctrine')->getManager();
        $voucherRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Voucher');
        $vouchers = $voucherRepository->createQueryBuilder('iFlairLetsBonusAdminBundle:Voucher')
            ->select('partial voucher.{id, program}')
            ->from('iFlairLetsBonusAdminBundle:Voucher', 'voucher')
            ->where('voucher.status = :status')
            ->setParameter('status', Voucher::VOUCHERACTIVE)
            //->andWhere('voucher.publishEndDate BETWEEN :voucher_expire_start_date AND :voucher_expire_end_date')
            ->andWhere('voucher.publishEndDate < :voucher_expire_start_date')
            ->setParameter('voucher_expire_start_date', $startDate)
            //->setParameter('voucher_expire_end_date', $endDate)
            ->getQuery()
            ->execute()
            ;
        if($vouchers) {
            foreach ($vouchers as $voucher) {
                $voucherProgram = $voucher->getProgram();
                if($voucherProgram) {
                    $shops = $this->getVoucherProgramSpecificShops($voucherProgram);
                    if($shops) {
                        foreach($shops as $shop) {
                            $shop->removeVoucher($voucher);
                            $em->persist($shop);                            
                        }
                    }
                }
                $voucherQuery = $voucherRepository->createQueryBuilder('iFlairLetsBonusAdminBundle:Voucher')
                    ->update('iFlairLetsBonusAdminBundle:Voucher', 'voucherupdate')
                    ->set('voucherupdate.status', Voucher::VOUCHEREXPIRED)
                    ->set('voucherupdate.modified', ':modifiedDate')
                    ->setParameter(':modifiedDate', $modifiedDate)
                    ->where('voucherupdate.id = :voucherId')
                    ->setParameter(':voucherId', $voucher->getId())
                    ->getQuery()
                    ->execute()
                    ;
            }
            $em->flush();
        }
        //print $voucherQuery;
    }

    protected function getVoucherProgramSpecificShops($voucherProgram)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Shop')
            ->findBy(array(
                'vprogram' => $voucherProgram
            ));
    }
}

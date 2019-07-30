<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;

class setLatestShopHistorySlugCommand extends ContainerAwareCommand
{
    private $em;
    protected function configure()
    {
        $this->setName('network:setLatestShopHistorySlug')->setDescription('set latest shop history slug');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $this->em->getConnection();
        $shops = $this->em->getRepository('iFlairLetsBonusAdminBundle:Shop')->findAll();
        foreach ($shops as $shop) {
            $now = new \DateTime('now');
            $histories = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')
                            ->createQueryBuilder('c')
                            ->select('c.id,c.startDate')
                            ->where('c.shop = :shopId')
                            ->andWhere('c.startDate > :now')
                            ->setParameter('shopId', $shop->getId())
                            ->setParameter('now', $now)
                            ->orderBy('c.startDate', 'DESC')
                            ->getQuery()
                            ->setMaxResults(1)
                            ->getResult();
            if(count($histories)>0) {
                $allHistories = $this->em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findBy(array('shop' => $shop->getId()));
                foreach ($allHistories as $oneHistory) {
                    $shopHistorySlug = $this->em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryId' => $oneHistory->getId(), 'categoryType' => Constants::SHOP_IDENTIFIER));
                    if ($shopHistorySlug) {
                        $shopHistorySlug->setCategoryId($histories[0]['id']);
                        $this->em->persist($shopHistorySlug);
                        $this->em->flush();
                    }
                }
            }
        }
    }
}

<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use iFlair\LetsBonusAdminBundle\Entity\shopHistory;

class CreateMissingShopSlugsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:CreateMissingShopSlugs')->setDescription('Create missing shop slugs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT a.id FROM lb_shop_history AS a WHERE a.id NOT IN (SELECT b.categoryId FROM lb_slug AS b WHERE b.categoryType='.Constants::SHOP_IDENTIFIER.')');
        $query->execute();
        $data = $query->fetchAll();
        if(count($data)==0){
            echo 'No slugs remain to generate for shop history';
        }else {
            foreach ($data as $key => $value) {
                $shopHistories = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('id' => $value['id']));
                if (!$shopHistories->getUrl()) {
                    $shopHistories->setUrl($shopHistories->getTitle());
                    $em->persist($shopHistories);
                    $em->flush();
                }
                $this->generateCategory($em, $shopHistories->getUrl(), $shopHistories->getId());
            }
           /* foreach ($shopHistories as $shopHistory) {
                $shopRepo = $em->getRepository('iFlairLetsBonusAdminBundle:Slug');
                $shopSlugs = $shopRepo->findBy(array('categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $shopHistory->getId()));
                if (!$shopSlugs) {
                    if (!$shopHistory->getUrl()) {
                        $shopHistory->setUrl($shopHistory->getTitle());
                        $em->persist($shopHistory);
                        $em->flush();
                    }
                    $this->generateCategory($em, $shopHistory->getUrl(), $shopHistory->getId());
                }
            }*/
        }
    }

    public function generateCategory(&$em, $Category_name, $Category_id)
    {
        if($Category_name) {
            $arr1 = array();
            $missing = array();
            $qb = $em->createQueryBuilder()
                ->select('u')
                ->from('iFlairLetsBonusAdminBundle:Slug', 'u')
                ->where('u.slugName LIKE :searchterm')
                ->setParameter('searchterm', $Category_name.'%');

            $slug_checking = $qb->getQuery()->getResult();
            $newCounter = '';
            if (!empty($slug_checking)) {
                $i=0;
                $lastUpdated = '';
                foreach ($slug_checking as $key => $value) {                
                    $lastCounterArrTmp = explode($Category_name, $value->getSlugName());
                    if(count($lastCounterArrTmp)>1 && is_numeric($lastCounterArrTmp[1])) {
                        $arr1[] = $lastCounterArrTmp[1];
                    }
                    if(count($slug_checking)-1 == $i){
                        $lastUpdated = $value->getSlugName();
                    }
                    $i++;
                }
                if(count($arr1)>0) {
                    //if slug counter range is missed in between somewhere then go to if and pick first missing counter.
                    $usedSlugCountersRange = range(1, max($arr1));
                    $missing = array_diff($usedSlugCountersRange, $arr1);
                }

                if(count($missing)>0){
                    $newCounter = $missing[key($missing)];
                }else {
                    $arrlength = count($arr1);
                    if($arrlength>0){
                        sort($arr1);
                        for($x = 0; $x < $arrlength; $x++) {
                            $usedSlugCountersIndexing[] =  $arr1[$x];
                        }
                        $newCounter = $usedSlugCountersIndexing[$arrlength-1] + 1;
                    }else {
                        if (!empty($lastUpdated)) {
                            $lastCounterArr = explode($Category_name, $value->getSlugName());
                            //for newer slug use this.
                            if(is_array($lastCounterArr) && isset($lastCounterArr[1])) {
                                $lastCounter = $lastCounterArr[1];
                            }else{
                                $lastCounter = 1;
                            }
                            //for older slug[in case of HYPHEN (-)] ,do compare and then take as per o/p.
                            if (substr($lastCounter, 0, 1) == '-') {
                                $toCompare = ltrim($lastCounter);
                                if ($toCompare > $lastCounter) {
                                    $lastCounter = $toCompare;
                                }
                            }
                            $newCounter = $lastCounter + 1;
                        } else {
                            $newCounter = 1;
                        }
                    }
                }
            }

            $slug = new Slug();
            $slug->setCategoryType(Constants::SHOP_IDENTIFIER);
            $slug->setSlugName($Category_name.$newCounter);
            $slug->setCategoryId($Category_id);
            $em->persist($slug);
            $em->flush();
        }
    }
}

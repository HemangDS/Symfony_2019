<?php

namespace iFlair\LetsBonusAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use iFlair\LetsBonusAdminBundle\Entity\Slug;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use iFlair\LetsBonusAdminBundle\Entity\parentCategory;

class CreateMissingParentCategorySlugsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('network:CreateMissingParentCategorySlugs')->setDescription('Create missing parent category slugs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();
        $query = $connection->prepare('SELECT a.id FROM lb_parent_category AS a WHERE a.id NOT IN (SELECT b.categoryId FROM lb_slug AS b WHERE b.categoryType='.Constants::PARENT_CATEGORY_IDENTIFIER.')');
        $query->execute();
        $data = $query->fetchAll();
       
        if(count($data)==0){
           // echo 'No slugs remain to generate for parent category';
        }else {
            foreach ($data as $key => $value) 
            {
                $parentCategory = $em->getRepository('iFlairLetsBonusAdminBundle:parentCategory')->findOneBy(array('id' => $value['id']));
                if (!$parentCategory->getUrl()) 
                {
                    //echo "without url & without slug ====>>>>> ".$parentCategory->getId()."</br>";
                    $parentCategory->setUrl($parentCategory->getName());
                    $em->persist($parentCategory);
                    $em->flush();
                }
                $this->generateCategory($em, strtolower($parentCategory->getUrl()), $parentCategory->getId());
            }
        }

        $query1 = $connection->prepare('SELECT a.id FROM lb_parent_category AS a WHERE a.id IN (SELECT b.categoryId FROM lb_slug AS b WHERE b.categoryType='.Constants::PARENT_CATEGORY_IDENTIFIER.') AND a.url = ""');
        $query1->execute();
        $data1 = $query1->fetchAll();
        
        if(count($data1)==0){
            //echo 'No slugs remain to generate for parent category';
        }else {
            foreach ($data1 as $key => $value) 
            {
                $parentCategory = $em->getRepository('iFlairLetsBonusAdminBundle:parentCategory')->findOneBy(array('id' => $value['id']));
                
                //echo "without url & with slug ====>>>>> ".$parentCategory->getId()."</br>";
                $parentCategory->setUrl($parentCategory->getName());
                $em->persist($parentCategory);
                $em->flush();
              
                $this->generateCategory($em, strtolower($parentCategory->getUrl()), $parentCategory->getId());
            }
        }
    }

    public function generateCategory(&$em, $Category_name, $Category_id)
    {
        $Category_name = str_replace(',', '', $Category_name);
        $Category_name = str_replace(' ', '-', $Category_name);
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
                            $lastCounter = 0;
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
        //exit;
        $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(array('categoryType' => Constants::PARENT_CATEGORY_IDENTIFIER, 'categoryId'=> $Category_id));
      
        if (!$slug) 
        {
            $slug = new Slug();
            $slug->setCategoryType(Constants::PARENT_CATEGORY_IDENTIFIER);
            $slug->setSlugName($Category_name.$newCounter);
            $slug->setCategoryId($Category_id);
            $em->persist($slug);
            $em->flush();
        }
        else
        {
            $slug->setSlugName($Category_name.$newCounter);
            $em->persist($slug);
            $em->flush();
        }
        
    }
}

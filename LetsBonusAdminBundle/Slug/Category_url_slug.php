<?php

namespace iFlair\LetsBonusAdminBundle\Slug;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Category_url_slug extends Controller
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function urlVerification($cat_id, $Category_name, $entity_name, $entity_field_name, $constant_identifier, $db_store_name, $post_store_name)
    {
        $em = $this->em;
        $connection = $em->getConnection();
        $i = 1;
        $Category_name = strtolower($Category_name);
        $Category_name = str_replace(' ', '-', $Category_name);

        if ($db_store_name == $post_store_name) {
            // when field name is not modified

            $slug_checking = $em->getRepository('iFlairLetsBonusAdminBundle:'.$entity_name)->
            findOneBy(array('categoryId' => $cat_id, 'categoryType' => $constant_identifier));

            if (!empty($slug_checking)) {
                // when field name is same as before and its slug is exist

                return $slug_checking->getSlugName();
            } else {
                // when field name is same as before but its slug is not exist

                $Category_name = $this->generateCategory($em, $entity_name, $entity_field_name, $Category_name);
            }
        } else {
            $Category_name = $this->generateCategory($em, $entity_name, $entity_field_name, $Category_name);
        }

        $Category_name = $this->normalizeChars($Category_name);

        return $Category_name;
    }
    public function normalizeChars($string){
        $normalizeChars = array(
            'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
            'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
            'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
            'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
            'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
            'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
            'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
        );
        return strtr($string, $normalizeChars);
    }
    public function removeSlug($cat_type, $category_id, $entity_name, $entity_cate_type, $entity_cate_id)
    {
        $em = $this->em;
        $connection = $em->getConnection();

        $slug_checking = $em->getRepository('iFlairLetsBonusAdminBundle:'.$entity_name)->
        findOneBy(array($entity_cate_type => $cat_type, $entity_cate_id => $category_id));

        if ($slug_checking) {
            $em->remove($slug_checking);
            $em->flush();
        }
    }

    public function generateCategory($em, $entity_name, $entity_field_name, $Category_name)
    {
        $arr1 = array();
        $missing = array();
        $qb = $em->createQueryBuilder()
            ->select('u')
            ->from('iFlairLetsBonusAdminBundle:'.$entity_name, 'u')
            ->where('u.'.$entity_field_name.' LIKE :searchterm')
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
                        $lastCounter = $lastCounterArr[1];
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
        return $Category_name.$newCounter;
    }

    public function generateCategory1($em, $entity_name, $entity_field_name, $Category_name)
    {
        $data2 = array();
        $data1 = array();
        $qb = $em->createQueryBuilder()
            ->select('u')
            ->from('iFlairLetsBonusAdminBundle:'.$entity_name, 'u')
            ->where('u.'.$entity_field_name.' LIKE :searchterm')
            ->setParameter('searchterm', $Category_name.'%');

        $slug_checking = $qb->getQuery()->getResult();

        if (!empty($slug_checking)) {
            foreach ($slug_checking as $key => $value) {
                $slug_names[] = $value->getSlugName();
            }

            if (in_array($Category_name, $slug_names) == 1) {
                $slug_count = array();
                foreach ($slug_checking as $key => $value) {
                    $slug_count = explode($Category_name, $value->getSlugName());

                    if (!empty($slug_count[1])) {
                        $data[] = $slug_count[1];
                    }
                }

                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        if (strlen($value) == 2) {
                            $data1[] = explode('-', $value);
                        }
                    }

                    if (!empty($data1)) {
                        foreach ($data1 as $key => $value) {
                            foreach ($value as $key => $value1) {
                                if (is_numeric($value1)) {
                                    $data2[] = $value1;
                                }
                            }
                        }

                        $slug_max_count = explode('-', max($data2));
                        $i = max($slug_max_count) + 1;
                    } else {
                        $i = 1;
                    }
                } else {
                    $i = 1;
                }

                $Category_name = $Category_name.$i;
            }
        }

        return $Category_name;
    }
}

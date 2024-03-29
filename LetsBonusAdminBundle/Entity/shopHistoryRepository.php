<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

/**
 * shopHistoryRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class shopHistoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function hasToUpdateCashBackAmount(&$em, $formData, $object)
    {
        $query = 'SELECT * FROM lb_cachback_settings_shop AS css, lb_cashbackSettings AS c ';
        $condition = " WHERE css.shop_id={$formData['shop']} AND css.cashback_settings_id = c.id AND CURDATE() BETWEEN startDate AND endDate";
        $connection = $em->getConnection();
        $statement = $connection->prepare($query.$condition);
        $statement->execute();
        $numbers = $statement->fetch();
        if ($numbers['type'] == 'double') {
            $object->setCashbackPercentage($formData['cashbackPercentage'] * 2);
        } elseif ($numbers['type'] == 'triple') {
            $object->setCashbackPercentage($formData['cashbackPercentage'] * 3);
        }

        return $object;
    }

    public function addRowShopHistory(&$em, $object)
    {
        $shopHistory = new shopHistory();
        $shopHistory = $this->addRowObject($em, $object, $shopHistory);

        $slugRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Slug');
        $slug = $slugRepository->addSlug($em, $shopHistory);

        $variationRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Variation');
        $addVariation = $variationRepository->addRowVariation($em, $object, $shopHistory);
    }

    public function addRowObject(&$em, $object, $shopHistory)
    {
        $shopHistory->setShop($object);
        $shopHistory->setAdministrator($object->getAdministrator());
        $shopHistory->setTitle($object->getTitle());
        $shopHistory->setUrl($object->getUrl());
        $shopHistory->setIntroduction($object->getIntroduction());
        $shopHistory->setDescription($object->getDescription());
        $shopHistory->setTearms($object->getTearms());
        $shopHistory->setCashbackPrice($object->getCashbackPrice());
        $shopHistory->setCashbackPercentage($object->getCashbackPercentage());
        $shopHistory->setLetsBonusPercentage($object->getLetsBonusPercentage());
        $shopHistory->setUrlAffiliate($object->getUrlAffiliate());
        $shopHistory->setStartDate($object->getStartDate());
       // $shopHistory->setEndDate($object->getEndDate());
        $shopHistory->setTag($object->getTag());
        $shopHistory->setPrevLabelCrossedOut($object->getPrevLabelCrossedOut());
        $shopHistory->setShippingInfo($object->getShippingInfo());
        //$shopHistory->setVariation($object->getVariation());
        //$shopHistory->setVariation($object->getShopVariation());
        $em->persist($shopHistory);
        $em->flush();

        return $shopHistory;
    }

    public function updateRowShopHistory(&$em, $object, $formData)
    {
        $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $object->getId()));

        $slugRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Slug');

        if ($shopHistory) {
            //$slug = $slugRepository->updateSlug($em, $shopHistory, $formData);
            //$shopHistory = $this->addRowObject($em, $object, $shopHistory);
        } else {
            $shopHistory = new shopHistory();
            $shopHistory = $this->addRowObject($em, $object, $shopHistory);
            $slug = $slugRepository->updateSlug($em, $shopHistory, $formData);
        }

        $variationRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Variation');
        $addVariation = $variationRepository->updateRowVariation($em, $object, $shopHistory);

        //iFlair :: need to make more improvable
        /*$shvc = count($variations);
        $svc = count($object->getShopVariation());
        $start=0;
        if($svc=$shvc) {
            foreach ($variations as $variation) {
                if ($start != $shvc) {
                    foreach ($object->getShopVariation() as $shopVariation) {
                        $variation->setNumber($shopVariation->getNumber());
                        $variation->setTitle($shopVariation->getTitle());
                        $variation->setDate($shopVariation->getDate());
                    }
                    $variation->setShopHistory($shopHistory);
                    $em->persist($variation);
                    $em->flush();
                    $start++;
                }
            }
        }elseif($svc>$shvc) {
            foreach ($variations as $variation) {
                $em->remove($variation);
                $em->flush();
            }
            foreach ($object->getShopVariation() as $shopVariation) {
                $variation = new Variation();
                $variation->setNumber($shopVariation->getNumber());
                $variation->setTitle($shopVariation->getTitle());
                $variation->setDate($shopVariation->getDate());
                $variation->setShopHistory($shopHistory);
                $em->persist($variation);
                $em->flush();
            }

        }
        }else if($svc>$shvc) {
            $variation = new Variation();
            foreach ($object->getShopVariation() as $shopVariation) {
                $variation->setNumber($shopVariation->getNumber());
                $variation->setTitle($shopVariation->getTitle());
                $variation->setDate($shopVariation->getDate());
                $variation->setShopHistory($shopHistory);
                $em->persist($variation);
                $em->flush();
            }
            for ($i = 1; $i <= $svc; $i++) {
                if ($i != $start) {
                    foreach ($object->getShopVariation() as $shopVariation) {
                        $variation->setNumber($shopVariation->getNumber());
                        $variation->setTitle($shopVariation->getTitle());
                        $variation->setDate($shopVariation->getDate());
                        $start++;
                    }
                    $variation->setShopHistory($shopHistory);
                    $em->persist($variation);
                    $em->flush();
                }
            }
        }
        $variations = $em->getRepository('iFlairLetsBonusAdminBundle:Variation')->findBy(array('shop' => $object->getId()));
        if($variations){
            foreach($variations as $variation){
                $variation->setShopHistory($shopHistory);
                $em->persist($variation);
                $em->flush();
            }
        }*/
    }

    public function removeRowShopHistory(&$em, $object)
    {
        $shopHistory = $em->getRepository('iFlairLetsBonusAdminBundle:shopHistory')->findOneBy(array('shop' => $object->getId()));

        if (!$shopHistory) {
            throw $this->createNotFoundException('No shop history found for id '.$object->getId());
        }

        $variationRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Variation');
        $deleteVariation = $variationRepository->deleteRowVariation($em, $object, $shopHistory);

        $slugRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Slug');
        $slug = $slugRepository->removeSlug($em, $shopHistory);

        $em->remove($shopHistory);
        $em->flush();
    }
}

<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use iFlair\LetsBonusAdminBundle\Entity\Shop;
use iFlair\LetsBonusAdminBundle\Slug\Constants;
use iFlair\LetsBonusFrontBundle\Application\Utilities\GetImageUrlRequest;

class OfertasespecialesController extends FOSRestController
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOfertaseSpecialesAction()
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $connection = $em->getConnection();

        $query = $connection->prepare(
            'SELECT os.*,os.voucher_programs_id,
          (SELECT sh.title FROM lb_shop_history AS sh WHERE sh.shop = s.id ORDER BY sh.created DESC LIMIT 1) AS title,
          (SELECT sh.cashbackPercentage FROM lb_shop_history AS sh WHERE sh.shop = s.id ORDER BY sh.created DESC LIMIT 1) AS cashbackPercentage,
          (SELECT sh.cashbackPrice FROM lb_shop_history AS sh WHERE sh.shop = s.id ORDER BY sh.created DESC LIMIT 1) AS cashbackPrice,
          (SELECT sh.id FROM lb_shop_history AS sh WHERE sh.shop = s.id ORDER BY sh.created DESC LIMIT 1) AS shid
        FROM lb_offer_specials AS os
        INNER JOIN lb_shop s ON  (s.shopStatus = 1 AND s.vprogram_id = os.voucher_programs_id)
        WHERE os.status = 1 AND :now >= os.startDate AND :now <= os.endDate
        GROUP BY s.id
        ORDER BY os.created DESC, s.created DESC LIMIT 3'
        );
        $query->bindValue('now', (new \DateTime())->format(DATE_ISO8601));
        $query->execute();
        $offerSpecialsDatas = $query->fetchAll();
        $imageUrlService = $this->get('iflair_lets_bonus_front.application_utilities.get_image_url');
        $serviceRequest = new GetImageUrlRequest();
        $serviceRequest->imageType = 'special_offertas';
        foreach ($offerSpecialsDatas as $key => $value) {
            if (!empty($value['image_id'])) {
                $serviceRequest->imageId = $value['image_id'];
                $response = $imageUrlService->execute($serviceRequest);
                $offerSpecialsDatas[$key]['image_path'] = $response->path;
            }
        }

        return $this->render(
            'iFlairLetsBonusFrontBundle:offerSpecials:offerSpecial.html.twig',
            ['offerSpecialsDatas' => $offerSpecialsDatas]
        );
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getHomeDailyOfferAction()
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $connection = $em->getConnection();

        $query = $connection->prepare(
            'SELECT s.image_id, s.offers AS offer_type,vp.image_id AS logo_id, vp.program_name,
  (SELECT sh.title FROM lb_shop_history AS sh WHERE :now >= sh.startDate AND sh.shop = s.id ORDER BY sh.startDate DESC LIMIT 1) AS description,
  (SELECT sh.cashbackPercentage FROM lb_shop_history AS sh WHERE :now >= sh.startDate AND sh.shop = s.id ORDER BY sh.startDate DESC LIMIT 1) AS cashback_percentage,
  (SELECT sh.cashbackPrice FROM lb_shop_history AS sh WHERE :now >= sh.startDate AND sh.shop = s.id ORDER BY sh.startDate DESC LIMIT 1) AS cashback_price,
  (SELECT sh.id FROM lb_shop_history AS sh WHERE :now >= sh.startDate AND sh.shop = s.id ORDER BY sh.created DESC LIMIT 1) AS shid
FROM lb_shop s
INNER JOIN lb_voucher_programs vp ON  (vp.id = s.vprogram_id)
WHERE  s.highlightedOffer = :offerstatus AND s.shopStatus = :shopstatus
GROUP BY s.id
LIMIT 10'
        );
        $query->bindValue('now', (new \DateTime())->format(DATE_ISO8601));
        $query->bindValue('offerstatus', Shop::OFFER_ACTIVATED);
        $query->bindValue('shopstatus', Shop::SHOP_ACTIVATED);
        $query->execute();
        $offerSpecialsDatas = $query->fetchAll();
        $imageUrlService = $this->get('iflair_lets_bonus_front.application_utilities.get_image_url');
        $serviceRequest = new GetImageUrlRequest();
        $serviceRequest->imageType = 'offertas_del_dia_type';
        $key = mt_rand(0, count($offerSpecialsDatas)-1);
        $offer = $offerSpecialsDatas[$key];
        $serviceRequest->imageId = $offer['image_id'];
        $response = $imageUrlService->execute($serviceRequest);
        $offer['shop_image'] = $response->path;
        $serviceRequest->imageType = 'brand_on_shop';
        $serviceRequest->imageId = $offer['logo_id'];
        $response = $imageUrlService->execute($serviceRequest);
        $offer['brand_logo'] = $response->path;
        switch ($offer['offer_type']) {
            case 'cashback':
                $offer['type'] = 'Cashback';
                break;
            case 'voucher':
                $offer['type'] = 'Cupón';
                break;
            case 'offer':
                $offer['type'] = 'Oferta';
                break;
            case 'cashback/coupons':
                $offer['type'] = 'Cashback';
                $offer['offer_type'] = 'cashback';
                break;
        }

        $slug = $em->getRepository('iFlairLetsBonusAdminBundle:Slug')->findOneBy(
            ['categoryType' => Constants::SHOP_IDENTIFIER, 'categoryId' => $offer['shid']]
        );
        if (null !== $slug) {
            $offer['slug_name'] = $slug->getSlugName();
        }

        return $this->render(
            '::right_offer_block.html.twig',
            [
                'offer_array' => $offer,
                'expire_status' => '',
            ]
        );
    }
}

<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use iFlair\LetsBonusAdminBundle\Slug\Constants;

class ComofuncionaController extends Controller
{
    /**
     * @Route("/howitworks", name="How It Works")
     */
    public function howitworksAction(Request $request)
    {
        $top_image_url      = array();
        $middle_image_url   = array();
        $em                 = $this->getDoctrine()->getEntityManager();
        $connection         = $em->getConnection();
        $numberOfBrand      = 6;
        $numberOfCategory   = 6;
        $sliderTopRecord    = array();
        $sliderMiddleRecord= array();
        $sliderBottomRecord = array();


        /* Code to get top slider data start */
        $provider = $this->container->get('sonata.media.provider.image');
        $sliders_top = $em->createQueryBuilder()
            ->select('s')
            ->from('iFlairLetsBonusAdminBundle:Slider',  's')
            ->where(':date_from >= s.start_date')
            ->andWhere(':date_from <= s.end_date')
            ->andWhere('s.enabled = 1')
            ->andWhere('s.slider_area LIKE :sliderarea')
            ->andWhere('s.show_in_front = 1')
            ->setParameter('date_from', date('Y-m-d H:i:s'))
            ->setParameter('sliderarea', 'como functiona top')
            ->orderBy('s.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        foreach ($sliders_top as $sliderTop) {
            $sliderTopRecord = array();
            $media = $sliderTop->getImage();
            if(!empty($media)) {
                $format = $provider->getFormatName($media, 'como_functiona_top');
                $sliderTopRecord['image'] = $provider->generatePublicUrl($media, $format);
            } else {
                $sliderTopRecord['image'] ="";
            }
            $sliderTopRecord['title'] = $sliderTop->getTitle();
            $sliderTopRecord['url'] = $sliderTop->getUrl();
            $top_image_url[] = $sliderTopRecord;
        }
        /* Code to get top slider data end   */
        

        /* Code to get middle slider data start */
        $sliders_middle = $em->createQueryBuilder()
            ->select('s')
            ->from('iFlairLetsBonusAdminBundle:Slider',  's')
            ->where(':date_from >= s.start_date')
            ->andWhere(':date_from <= s.end_date')
            ->andWhere('s.enabled = 1')
            ->andWhere('s.slider_area LIKE :sliderarea')
            ->andWhere('s.show_in_front = 1')
            ->setParameter('date_from', date('Y-m-d H:i:s'))
            ->setParameter('sliderarea', 'como functiona middle')
            ->orderBy('s.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        foreach ($sliders_middle as $sliderMiddle) {
            $sliderMiddleRecord = array();
            $media = $sliderMiddle->getImage();
            if(!empty($media)) {
                $format = $provider->getFormatName($media, 'como_functiona_top');
                $sliderMiddleRecord['image'] = $provider->generatePublicUrl($media, $format);
            } else {
                $sliderMiddleRecord['image'] = "";
            }
            $sliderMiddleRecord['title'] = $sliderMiddle->getTitle();
            $sliderMiddleRecord['url'] = $sliderMiddle->getUrl();
            $middle_image_url[] = $sliderMiddleRecord;
        }
        /* Code to get middle slider data end */

        /* Code to get bottom slider data start */
        $sliders_bottom = $em->createQueryBuilder()
            ->select('s')
            ->from('iFlairLetsBonusAdminBundle:Slider',  's')
            ->where(':date_from >= s.start_date')
            ->andWhere(':date_from <= s.end_date')
            ->andWhere('s.enabled = 1')
            ->andWhere('s.slider_area LIKE :sliderarea')
            ->andWhere('s.show_in_front = 1')
            ->setParameter('date_from', date('Y-m-d H:i:s'))
            ->setParameter('sliderarea', 'como functiona bottom')
            ->orderBy('s.title', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;

        foreach ($sliders_bottom as $sliderBottom) {
            $sliderBottomRecord['image']="";
            $sliderBottomRecord = array();
            $media = $sliderBottom->getImage();
            if(!empty($media)) {
                $format = $provider->getFormatName($media, 'como_functiona_top');
                $sliderBottomRecord['image'] = $provider->generatePublicUrl($media, $format);
            }
            $sliderBottomRecord['title'] = $sliderBottom->getTitle();
            $sliderBottomRecord['description'] = $sliderBottom->getDescription();
            $sliderBottomRecord['url'] = $sliderBottom->getUrl();
        }
        /* Code to get bottom slider data end */

        /* Code to get brand data start       */
        $query = $connection->prepare('SELECT 
                    vp.id, vp.logo_path, vp.image_id,
                     slg.slugName 
                     FROM lb_shop_history AS sh 
                     JOIN lb_shop AS s ON sh.shop = s.id 
                     JOIN lb_voucher_programs AS vp ON s.vprogram_id = vp.id 
                     JOIN lb_slug AS slg ON slg.categoryId = sh.id 
                     WHERE sh.show_on_como_functiona= :comostatus 
                     AND s.shopStatus = :status 
                     AND CURDATE() 
                     BETWEEN s.startDate AND s.endDate 
                     AND CURDATE() >= sh.startDate
                     AND slg.categoryType = '.Constants::SHOP_IDENTIFIER.' limit '.$numberOfBrand);

        $query->bindValue('comostatus', 1);
        $query->bindValue('status', 1);

        $query->execute();
        $brandImageUrls = $query->fetchAll();


        foreach ($brandImageUrls as $key => $brandImageUrl) {
            $brand = $em->getRepository('iFlairLetsBonusAdminBundle:VoucherPrograms')->findOneById(array('id' => $brandImageUrl['id']));

            $media = $brand->getImage();
            if(!empty($media)) {
                $format = $provider->getFormatName($media, 'cashback_voucher_popup');
                $uploadedImage = $provider->generatePublicUrl($media, $format);
                if(!$uploadedImage) {
                    $format = $provider->getFormatName($media, 'preview');
                    $uploadedImage = $provider->generatePublicUrl($media, $format);
                }
                $brandImageUrls[$key]['logo_path'] = $uploadedImage;
            } else {
                $brandImageUrls[$key]['logo_path'] = $brandImageUrl['logo_path'];
            }
        }
        /* Code to get brand data end */

        /* Code to get category data start */
        $query = $connection->prepare('SELECT pc.id, pc.logoimage_id, pc.name, s.slugName from lb_parent_category as pc join lb_slug as s on pc.id = s.categoryId where show_on_como_functiona= :status AND categoryType= :category_type limit '.$numberOfCategory);
        $query->bindValue('status', 1);
        $query->bindValue('category_type', Constants::PARENT_CATEGORY_IDENTIFIER);

        $query->execute();
        $parentCategories = $query->fetchAll();
        
        foreach ($parentCategories as $key => $parentCategory) {
            $parentCat = $em->getRepository('iFlairLetsBonusAdminBundle:parentCategory')->findOneById(array('id' => $parentCategory['id']));

            $media = $parentCat->getLogoimage();
            if(!empty($media)) {
                $format = $provider->getFormatName($media, 'preview');
                $parentCategories[$key]['image'] = $provider->generatePublicUrl($media, $format);
            } else {
                $parentCategories[$key]['image'] = "";
            }
        }
        /* Code to get category data end   */

        return $this->render('iFlairLetsBonusFrontBundle:Comofunciona:howitworks.html.twig', array(
            'top_image_url' => $top_image_url, 'middle_image_url' => $middle_image_url, 'bottom_image_url' => $sliderBottomRecord, 'brand_image_urls' => $brandImageUrls, 'parentCategories' => $parentCategories
        ));
    }
}

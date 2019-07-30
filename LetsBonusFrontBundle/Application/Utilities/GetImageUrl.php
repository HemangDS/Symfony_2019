<?php

namespace iFlair\LetsBonusFrontBundle\Application\Utilities;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\MediaBundle\Provider\Pool;

/**
 * Class GetImageUrl
 *
 * @package iFlair\LetsBonusFrontBundle\Application\Utilities
 */
class GetImageUrl
{
    /** @var  EntityManagerInterface */
    private $entityManager;
    /** @var Pool */
    private $mediaManager;


    public function __construct($entityManager, $mediaManager)
    {
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
    }

    public function execute(GetImageUrlRequest $request)
    {
        $response = new GetImageUrlResponse();
        $media = $this->entityManager->getRepository('ApplicationSonataMediaBundle:Media')->find($request->imageId);
        if (null !== $media) {
            $provider = $this->mediaManager->getProvider($media->getProviderName());
            $format = $provider->getFormatName($media, $request->imageType);
            $response->path = $provider->generatePublicUrl($media, $format);
        }

        /*-------------------*/
//        $fieldName = 'nimage';
//        if ($definedType == 'voucherprogram') {
//            if ($imageType == 'brand_on_shop') {
//                $fieldName = 'image';
//            }
//            if ($imageType == 'cashback_voucher_popup') {
//                $fieldName = 'popUpImage';
//            }
//        }
//        $media = $entities->findOneBy([$fieldName => $imageId]);
//        $imageUrl = '';
//        if (!empty($media) && !empty($imageId)) {
//            if ($definedType == 'category') {
//                $media = $media->getnImage();
//            } elseif ($definedType == 'voucherprogram') {
//                if ($imageType == 'brand_on_shop') {
//                    $media = $media->getImage();
//                }
//                if ($imageType == 'cashback_voucher_popup') {
//                    $media = $media->getPopUpImage();
//                }
//            }
//            $mediaManager = $this->get('sonata.media.pool');
//            $provider = $mediaManager->getProvider($media->getProviderName());
//            $format = $provider->getFormatName($media, $imageType);
//            $imageUrl = $provider->generatePublicUrl($media, $format);
//        }


        return $response;
    }
}

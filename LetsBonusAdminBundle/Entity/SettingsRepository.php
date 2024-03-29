<?php

namespace iFlair\LetsBonusAdminBundle\Entity;

/**
 * SettingsRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SettingsRepository extends \Doctrine\ORM\EntityRepository
{
    public function getMediaPreviewOverEditMode($media, $container)
    {
        $mediaManager = $container->get('sonata.media.pool');
        $provider = $mediaManager->getProvider($media->getProviderName());
        $mediaPreviewUrl = $provider->generatePublicUrl($media, 'default_preview');
        if ($mediaPreviewUrl) {
            $imageMediaPreview = '<img src="'.$mediaPreviewUrl.'" class="admin-preview" />';
        } else {
            $imageMediaPreview = '';
        }

        return $imageMediaPreview;
    }

    public function getSettingsBanner($mediaType, $code, &$em){
        $settingsRepository = $em->getRepository('iFlairLetsBonusAdminBundle:Settings');
        $settings = $settingsRepository->findOneBy(array(
            'status' => Settings::YES,
            'code' => $code,
        ));
        if ($settings) {
            $settingsMedia = $settings->getImage();
            if ($settingsMedia) {
                return $this->getMediaURL($settingsMedia, $mediaType);
            }
        }
    }

    public function getMediaURL($media, $mediaImageType)
    {
        $mediaManager = $this->get('sonata.media.pool');
        $mediaprovider = $mediaManager->getProvider($media->getProviderName());
        $mediaUrl = $mediaprovider->generatePublicUrl($media, $mediaImageType);

        return $mediaUrl;
    }
}

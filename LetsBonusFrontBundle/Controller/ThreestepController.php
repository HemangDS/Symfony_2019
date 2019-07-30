<?php

namespace iFlair\LetsBonusFrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ThreestepController extends Controller
{
    public function getAllStepHtmlAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $connection = $em->getConnection();

        $query = $connection->query(
            'SELECT * FROM lb_three_step ts1
            WHERE created IN (SELECT MAX(created) FROM lb_three_step GROUP BY step HAVING id = ts1.id)
            AND CURDATE() BETWEEN startDate AND endDate 
            AND status = 1
            ORDER BY step LIMIT 3'
        );
        $query->execute();
        $threestepDatas = $query->fetchAll();
        $entity = 'ThreeStep';
        $imageType = 'default_big';
        $entities = $em->getRepository('iFlairLetsBonusAdminBundle:'.$entity);

        foreach ($threestepDatas as $key => $value) {
            if (!empty($value['image_id'])) {
                $imageUrl = $this->getImageUrl($entities, $value['image_id'], $imageType);
                $threestepDatas[$key]['image_path'] = $imageUrl;
            }
        }

        return $this->render(
            'iFlairLetsBonusFrontBundle:Threestep:threestep.html.twig',
            ['threestepDatas' => $threestepDatas]
        );
    }

    public function getImageUrl($entities, $imageId, $imageType = 'default_big')
    {
        $media = $entities->findOneBy(['image' => $imageId]);
        $imageUrl = '';
        if (!empty($media) && !empty($imageId)) {
            $media = $media->getImage();
            $mediaManager = $this->get('sonata.media.pool');
            $provider = $mediaManager->getProvider($media->getProviderName());
            $imageUrl = $provider->generatePublicUrl($media, $imageType);
        }

        return $imageUrl;
    }
}

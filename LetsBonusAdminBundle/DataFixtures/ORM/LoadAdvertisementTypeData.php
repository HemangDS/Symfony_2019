<?php

namespace iFlair\LetsBonusAdminBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use iFlair\LetsBonusAdminBundle\Entity\AdvertisementType;

class LoadAdvertisementTypeData extends LoadContainerFixtures
{
    public function load(ObjectManager $manager)
    {
        $AdvertisementTypes = array(
            AdvertisementType::ADTYPEHEADER,
            AdvertisementType::ADTYPEMIDDLECONTENT,
            AdvertisementType::ADTYPEFOOTER,
        );
        foreach ($AdvertisementTypes as $AdvertisementType) {
            if (!$adType = $this->checkIFAdvertisementTypeExists($AdvertisementType)) {
                $adType = new AdvertisementType();
                $adType->setAdvTypeName($AdvertisementType);
                $manager->persist($adType);
                $manager->flush();
            }
            $this->addReference('AdvertisementType'.$AdvertisementType, $adType);
        }
    }

    protected function checkIFAdvertisementTypeExists($type)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('iFlairLetsBonusAdminBundle:AdvertisementType')
            ->findOneBy(array('advTypeName' => $type));
    }
}

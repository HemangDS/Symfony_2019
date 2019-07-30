<?php

namespace Galop\AdminBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Galop\AdminBundle\Entity\NewsRegion;

class RegionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $regionArray = array(
        	1 => "Regional",
			2 => "National",
			3 => "International"
        );

        foreach ($regionArray as $id => $regionName) {
            $dataExist = $manager->getRepository('GalopAdminBundle:NewsRegion')->findOneBy(array('region' => $regionName));
            if(empty($dataExist)) {
            	$region = new NewsRegion();
            	$region->setId($id);	
                $region->setRegion($regionName);    
            	$manager->persist($region);
                $metadata = $manager->getClassMetaData(get_class($region));
                $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            	$manager->flush();
            }
        }

    }
}

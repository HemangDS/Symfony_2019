<?php

namespace Galop\AdminBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Galop\AdminBundle\Entity\NewsDescipline;

class DesciplineFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $desciplineArray = array(
        	1 => "Algemeen",
			2 => "Dressuur",
			3 => "Eventing",
			4 => "Fokkerij",
			5 => "Galop",
			6 => "Jumping",
			7 => "KBSRF",
			8 => "LRV",
			9 => "Mennen"
        );

        foreach ($desciplineArray as $id => $desciplineName) {
            $dataExist = $manager->getRepository('GalopAdminBundle:NewsDescipline')->findOneBy(array('descipline' => $desciplineName));
            if(empty($dataExist)) {
            	$descipline = new NewsDescipline();
            	$descipline->setId($id);	
                $descipline->setDescipline($desciplineName);    
            	$manager->persist($descipline);
                $metadata = $manager->getClassMetaData(get_class($descipline));
                $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            	$manager->flush();
            }
        }

    }
}

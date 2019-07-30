<?php

namespace iFlair\LetsBonusAdminBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use iFlair\LetsBonusAdminBundle\Entity\Language;

class LoadLanguageData extends LoadContainerFixtures
{
    public function load(ObjectManager $manager)
    {
        if (!$Language = $this->checkIFLanguageExists(Language::DEFAULTLANG)) {
            $Language = new Language();
            $Language->setName(Language::DEFAULTLANGNAME);
            $Language->setCode(Language::DEFAULTLANG);
            $manager->persist($Language);
            $manager->flush();
        }
        $this->addReference('Language', $Language);
    }

    protected function checkIFLanguageExists($code)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getRepository('iFlairLetsBonusAdminBundle:Language')
            ->findOneByCode($code);
    }
}

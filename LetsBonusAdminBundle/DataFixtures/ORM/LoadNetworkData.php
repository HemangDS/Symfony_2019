<?php

namespace iFlair\LetsBonusAdminBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use iFlair\LetsBonusAdminBundle\Entity\Network;

class LoadNetworkData extends LoadContainerFixtures
{
    public function load(ObjectManager $manager)
    {
        $LNetworks = array(
            Network::TRADEDOUBLER => array(
                'name' => Network::TRADEDOUBLER,
                'url' => Network::TRADEDOUBLERURL,
            ),
            Network::ZANOX => array(
                'name' => Network::ZANOX,
                'url' => Network::ZANOXURL,
            ),
            Network::TDI => array(
                'name' => Network::TDI,
                'url' => Network::TDIURL,
            ),
            Network::EBAY => array(
                'name' => Network::EBAY,
                'url' => Network::EBAYURL,
            ),
            Network::LINKSHAREOLD => array(
                'name' => Network::LINKSHAREOLD,
                'url' => Network::LINKSHAREOLDURL,
            ),
            Network::LINKSHARE => array(
                'name' => Network::LINKSHARE,
                'url' => Network::LINKSHAREURL,
            ),
            Network::WEBGAINS => array(
                'name' => Network::WEBGAINS,
                'url' => Network::WEBGAINSURL,
            ),
            Network::CJ => array(
                'name' => Network::CJ,
                'url' => Network::CJURL,
            ),
            Network::AMAZON => array(
                'name' => Network::AMAZON,
                'url' => Network::AMAZONURL,
            ),
        );

        foreach ($LNetworks as $LNetwork) {
            if (!$Network = $this->checkIFNetworkExists($LNetwork['name'])) {
                $Network = new Network();
                $Network->setName($LNetwork['name']);
                $Network->setUrl($LNetwork['url']);
                $manager->persist($Network);
                $manager->flush();
            }
            $this->addReference('Network'.$LNetwork['name'], $Network);
        }
    }

    protected function checkIFNetworkExists($name)
    {
        return $this->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository('iFlairLetsBonusAdminBundle:Network')
            ->findOneByName($name);
    }
}

<?php
// src/iFlair/LetsBonusAdminBundle/Twig/cashbackSettingsExtension.php

namespace iFlair\LetsBonusAdminBundle\Twig;

class cashbackSettingsExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'getShopDetails' => new \Twig_Filter_Method($this, 'getShopDetails'),
        );
    }

    public function getShopDetails($cashbackSettings)
    {
        echo '<pre>';
        print_r($cashbackSettings->getShop()->getId());
        exit;

        return $cashbackSettingsId.'text xx yy';
    }

    public function getName()
    {
        return 'cashbackSettings_extension';
    }
}

<?php

/**
 * Copyright © 2017 Simi . All rights reserved.
 */

namespace Simi\Simipromoteapp\Block\Mobileapplication;

use Simi\Simipromoteapp\Block\BaseBlock;
use Simi\Simipromoteapp\Helper\Email;

class Index extends BaseBlock
{

    public $hello = 'Hello World1';

    public function getImageHtml($type)
    {
        $helper       = $this->simiObjectManager->get('Simi\Simipromoteapp\Helper\Data');
        $image_first  = $this->getCurrentStoreConfigValue(Email::XML_IMAGE_FIRST_BLOCK);
        $image_second = $this->getCurrentStoreConfigValue(Email::XML_IMAGE_SECOND_BLOCK);
        $image_small  = $this->getCurrentStoreConfigValue(Email::XML_IMAGE_SMALL_THIRD_BLOCK);
        $image_large  = $this->getCurrentStoreConfigValue(Email::XML_IMAGE_LARGE_THIRD_BLOCK);
        $image_fourth = $this->getCurrentStoreConfigValue(Email::XML_IMAGE_FOURTH_BLOCK);

        if ($type == 1) {
            return '<img class="main-img" title="" src="' . $helper->getImageLink($image_first) . '" alt="" />';
        } elseif ($type == 2) {
            return '<img class="main-img" title="" src="' . $helper->getImageLink($image_second) . '" alt="" />';
        } elseif ($type == 3) {
            return '<img title="" src="' . $helper->getImageLink($image_small) . '" alt="" />';
        } elseif ($type == 4) {
            return '<img class="main-img" title="" src="' . $helper->getImageLink($image_large) . '" alt="" />';
        } elseif ($type == 5) {
            return '<img class="main-img" title="" src="' . $helper->getImageLink($image_fourth) . '" alt="" />';
        } else {
            return '#';
        }
    }
}

<?php

namespace Simi\Simifbconnect\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper{

    public function checkIfHasChild($category)
    {
        $categoryChildrenCount = $category->getChildrenCount();
        if ($categoryChildrenCount > 0)
            $categoryChildrenCount = 1;
        else
            $categoryChildrenCount = 0;
        if (!$categoryChildrenCount) {
            return '0';
        }

        return '1';
    }

    public function getStoreConfig($path){
        return $this->scopeConfig->getValue($path);
    }
}
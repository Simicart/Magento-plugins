<?php
/**
 * Created by PhpStorm.
 * User: macos
 * Date: 11/12/18
 * Time: 10:01 AM
 */

namespace Simi\Simiapicache\Model\Config;

class Apicache implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'home_api',
                'label' => 'Home Page'
            ],
            [
                'value' => 'products_detail',
                'label' => 'Product Detail'
            ],
            [
                'value' => 'products_list',
                'label' => 'Products List'
            ],
            [
                'value' => 'other_api',
                'label' => 'Other Page'
            ]
        ];
    }
}
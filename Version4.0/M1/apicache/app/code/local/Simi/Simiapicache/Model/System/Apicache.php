<?php
/**
 * Created by PhpStorm.
 * User: macos
 * Date: 10/31/18
 * Time: 9:57 AM
 */
class Simi_Simiapicache_Model_System_Apicache
{
    public function toOptionArray(){
        return array(
            array(
                'value' => 'home_api',
                'label' => 'Home Page'
            ),
            array(
                'value' => 'products_detail',
                'label' => 'Product Detail'
            ),
            array(
                'value' => 'products_list',
                'label' => 'Products List'
            ),
            array(
                'value' => 'other_api',
                'label' => 'Other Page'
            )
        );
    }
}
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
                'value' => '',
                'label' => 'None'
            ),
            array(
                'value' => 'home_api',
                'label' => 'Home Api'
            ),
            array(
                'value' => 'products_api',
                'label' => 'Products ,Category Api'
            ),
            array(
                'value' => 'another_api',
                'label' => 'Another Api'
            )
        );
    }
}
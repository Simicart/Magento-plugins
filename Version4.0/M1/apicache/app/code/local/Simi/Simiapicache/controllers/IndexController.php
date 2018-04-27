<?php

class Simi_Simiapicache_IndexController extends Mage_Core_Controller_Front_Action
{
    public function flushAction() {
        Mage::helper('simiapicache')->flushCache();
        echo Mage::helper('simiapicache')->__('Api Cache has been Flushed.');
    }
}
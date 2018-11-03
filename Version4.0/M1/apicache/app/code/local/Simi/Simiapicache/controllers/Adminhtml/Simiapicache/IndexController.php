<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 11/22/17
 * Time: 11:11 AM
 */
class Simi_Simiapicache_Adminhtml_Simiapicache_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simiapicache');
    }
    
    public function flushAction() {
        try{
            $api_cache = Mage::getStoreConfig('simiapicache/apicache/model_api');
            if(!$api_cache || $api_cache == ''){
                throw new Exception(Mage::helper('simiapicache')->__('Please select api cache to flush cache !'));
            }
            $api_cache = explode(',',$api_cache);
            foreach ($api_cache as $api){
                $path = Mage::getBaseDir('media') . DS . 'simiapicache' . DS . 'simiapi_json' . DS . $api;
                Mage::helper('simiapicache')->flushCache($path);
            }
            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('simiapicache')->__('Api Cache has been Flushed.'));

        }catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('adminhtml/system_config/edit/section/simiapicache');
        return;
    }

}
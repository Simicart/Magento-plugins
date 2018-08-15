<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 8/2/17
 * Time: 11:36 AM
 */

class Simi_Simideeplink_Model_Observer
{
    public function simiSimiconnectorModelServerInitialize($observer)
    {
        $observerObject = $observer->getObject();

        $observerObjectData = $observerObject->getData();

        if ($observerObjectData['resource'] == 'deeplinks') {
            $observerObjectData['module'] = 'simideeplink';
        }
        $observerObject->setData($observerObjectData);
    }

    public function addUrlSEOInforProduct($observer){
        $productAPIModel = $observer->getObject();
        $data = $productAPIModel->getData();
        $product_id = $data['resourceid'];
        $store = Mage::app()->getStore();
        $path = Mage::getResourceModel('core/url_rewrite')
            ->getRequestPathByIdPath('product/'.$product_id, $store);

        $url = $store->getBaseUrl($store::URL_TYPE_WEB) . $path;
        $detail_info = $productAPIModel->detail_info;
        $detail_info['product']['simi_seo_url'] = $url;
        $productAPIModel->detail_info = $detail_info;
    }

}
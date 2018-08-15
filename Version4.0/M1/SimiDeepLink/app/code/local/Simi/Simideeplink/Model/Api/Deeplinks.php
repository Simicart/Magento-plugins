<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 8/2/17
 * Time: 11:38 AM
 */
class Simi_Simideeplink_Model_Api_Deeplinks extends Simi_Simiconnector_Model_Api_Abstract
{

    // type: 1-category;  2-product; 3-cms

    public function setBuilderQuery()
    {
        // TODO: Implement setBuilderQuery() method.
    }

    public function index()
    {

        $data = $this->getData();
        $parameters = $data['params'];
        if (isset($parameters['url'])) {
            $url = $parameters['url'];
            $storeId = Mage::app()->getStore()->getId();
            $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
            $base_url_path = parse_url($baseUrl, PHP_URL_PATH);

            $path_url = parse_url($url, PHP_URL_PATH);

            $length_base_url_path = strlen($base_url_path);
            $new_url = substr($path_url, $length_base_url_path);
         
            //$new_url = str_replace($base_url_path, "", $path_url);



            $data = $this->getDataFromUrlForCanifa($new_url);
            return array('deeplink'=>$data);
        }
    }

    protected function getDataFromUrlForCanifa($url){

        // product
        $product_id = $this->getProductIdFromUrl($url);
        if($product_id){
            $data = array(
                'type'=>'2',
                'id'=>$product_id,
            );
            return $data;
        }

        // cms
        $cms_id = $this->getCMSIdFromUrl($url);
        if($cms_id){
            $data = array(
                'type'=>'3',
                'id'=>$cms_id,
            );
            return $data;
        }

        // category
        $cat_id = $this->getCategoryIdFromUrl($url);
        if($cat_id){
            $cate_model = Mage::getModel('catalog/category')->load($cat_id);
            $has_child = '0';
            if ($cate_model->getChildrenCount() > 0) {
                $has_child = '1';
            }
            $cate_name = $cate_model->getName();
            $data = array(
                'type' => '1',
                'id' => $cat_id,
                'has_child' => $has_child,
                'name'=>$cate_name,
            );

            return $data;

        }

        return array(
            'type'=>'4',
        );
    }

    protected function getProductIdFromUrl($url){
        if(strpos($url,'catalog/product/view/id') !== false){
            $array_url = explode('/',$url);
            $is_found_id = false;

            foreach ($array_url as $char)
            {
                if($is_found_id){
                    return $char;
                }
                if(strpos($char,'id') !== false){
                    $is_found_id = true;
                }

            }
        }
        else{
            return null;
        }

    }

    protected function getCMSIdFromUrl($url){
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getResourceModel('sitemap/cms_page')->getCollection($storeId);
        foreach ($collection as $item)
        {
            $cms_url = $item->getUrl();
            if (strcasecmp($cms_url, $url) == 0) {
                return $item->getId();
            }
        }

        return null;
    }

    protected function getCategoryIdFromUrl($url){
        $array_url = explode('/',$url);

        foreach ($array_url as $item_url){
            if(strpos($item_url,'html') !== false){
                $cat_url = $item_url;
            }
        }

        if($cat_url){
            $cat_url_key = str_replace('.html','',$cat_url);
            $category = Mage::getModel ('catalog/category')
                ->getCollection ()
                ->addAttributeToFilter ('url_key', $cat_url_key)
                ->getFirstItem ();

            if($category){
                return $category->getId();
            }

        }

        return null;


    }

    protected function getDataFromUrl($url)
    {
        $storeId = Mage::app()->getStore()->getId();

        // check categories
        $collection = Mage::getResourceModel('sitemap/catalog_category')->getCollection($storeId);
        $categories = new Varien_Object();
        $categories->setItems($collection);

        foreach ($categories->getItems() as $item) {
            $cate_url = $item->getUrl();
            if (strcasecmp($cate_url, $url) == 0) {
                $cate_model = Mage::getModel('catalog/category')->load($item->getId());
                $has_child = '0';
                if ($cate_model->getChildrenCount() > 0) {
                    $has_child = '1';
                }
                $cate_name = $cate_model->getName();
                $data = array(
                    'type' => '1',
                    'id' => $item->getId(),
                    'has_child' => $has_child,
                    'name'=>$cate_name,
                );

                return $data;

            }
        }

        // check product
        $collection = Mage::getResourceModel('sitemap/catalog_product')->getCollection($storeId);
        $products = new Varien_Object();
        $products->setItems($collection);
        $array_path_url = explode('/',$url);
        $temp_url = $array_path_url[count($array_path_url)-1];

        foreach ($products->getItems() as $item)
        {
            $product_url = $item->getUrl();
            if (strcasecmp($product_url, $temp_url) == 0) {
                $data = array(
                    'type'=>'2',
                    'id'=>$item->getId(),
                );
                return $data;
            }
        }


        // check cms
        $collection = Mage::getResourceModel('sitemap/cms_page')->getCollection($storeId);
        foreach ($collection as $item)
        {
            $cms_url = $item->getUrl();
            if (strcasecmp($cms_url, $url) == 0) {
                $data = array(
                    'type'=>'3',
                    'id'=>$item->getId(),
                );
                return $data;
            }


        }

        return array(
            'type'=>'4',
        );

    }


}
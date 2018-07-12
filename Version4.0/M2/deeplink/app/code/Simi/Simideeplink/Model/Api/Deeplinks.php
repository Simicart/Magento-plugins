<?php
/**
 * Created by PhpStorm.
 * User: liam
 * Date: 5/9/18
 * Time: 9:30 AM
 */
namespace Simi\Simideeplink\Model\Api;

use \Magento\Framework\DataObject;
use Simi\Simiconnector\Model\Api\Apiabstract;

class Deeplinks extends Apiabstract{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Simi\Simiconnector\Helper\Data $helper
    )
    {
        parent::__construct($simiObjectManager);
        $this->simiconnectorHelper = $helper;
    }
    public function setBuilderQuery()
    {
        // TODO: Implement setBuilderQuery() method.
    }

    public function index()
    {
        $data = $this->getData();
        $parameters = $data['params'];
        $url = $parameters['url'];
        $storeId = $this->storeManager->getStore()->getId();
        $baseUrl = $this->storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
        $base_url_path = parse_url($baseUrl, PHP_URL_PATH);

        $path_url = parse_url($url, PHP_URL_PATH);

        $length_base_url_path = strlen($base_url_path);
        $new_url = substr($path_url, $length_base_url_path);
        $deepLinksData = $this->getDataFromUrl($new_url);
        return array('deeplink'=>$deepLinksData);
    }

    private function getDataFromUrl($url){
        $storeId = $this->storeManager->getStore()->getId();

        //check category
        $collection = $this->simiObjectManager->get('Magento\Sitemap\Model\ResourceModel\Catalog\Category')->getCollection($storeId);
        $categories = new DataObject();
        $categories->setItems($collection);
        foreach ($categories->getItems() as $item) {
            $cate_url = $item->getUrl();
            if (strcasecmp($cate_url, $url) == 0) {
                $cateModel = $this->simiObjectManager->create('Magento\Catalog\Model\Category')->load($item->getId());
                $data = array(
                    'type' => '1',
                    'id' => $item->getId(),
                    'has_child' => $cateModel->getChildrenCount()?'1':'0',
                    'name'=> $cateModel->getName()
                );
                return $data;

            }
        }

        //check product
        $collection = $this->simiObjectManager->get('Magento\Sitemap\Model\ResourceModel\Catalog\Product')->getCollection($storeId);
        $products = new DataObject();
        $products->setItems($collection);
        $array_path_url = explode('/',$url);
        $temp_url = $array_path_url[count($array_path_url)-1];
        foreach ($products->getItems() as $item) {
            $product_url = $item->getUrl();
            if (strcasecmp($product_url, $temp_url) == 0) {
                $data = array(
                    'type' => '2',
                    'id' => $item->getId(),
                    'name' => $item->getName()
                );
                return $data;
            }
        }

        //check cms
        $collection = $this->simiObjectManager->get('Magento\Sitemap\Model\ResourceModel\Cms\Page')->getCollection($storeId);
        $cmsPages = new DataObject();
        $cmsPages->setItems($collection);
        foreach ($cmsPages->getItems() as $item) {
            $cmsUrl = $item->getUrl();
            if (strcasecmp($cmsUrl, $url) == 0) {
                $data = array(
                    'type'=>'3',
                    'id'=>$item->getId(),
                );
                return $data;
            }
        }


        return array('type'=>'4');
    }
}
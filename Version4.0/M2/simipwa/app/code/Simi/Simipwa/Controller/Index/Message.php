<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 1/30/18
 * Time: 3:47 PM
 */

namespace Simi\Simipwa\Controller\Index;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;

class Message extends \Magento\Framework\App\Action\Action
{

    public $storeManager;

    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $endpoint = $data['endpoint'];
        $message = $this->_objectManager->get('Simi\Simipwa\Model\Notification')->getMessage($endpoint);

        $message_info = $message->getData();
        $img = null;
        if ($message_info['type'] == 1) {
            $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($message->getProductId());
            $message_info['notice_url'] = $product->getUrlPath() . "?id=" . $message_info["product_id"];
        }
        if ($message_info['type'] == 2) {
            $cate = $this->_objectManager->get('Magento\Catalog\Model\Category')->load($message->getCategoryId());
            $message_info['notice_url'] = $cate->getUrlPath() . "?cat=" . $message_info["category_id"];
        }
        if ($message_info['image_url']) {
            $img = $this->getMediaUrl($message_info['image_url']);
            $message_info['image_url'] = $img;
        }
        $result = array(
            "notification" => $message_info
        );
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($result));
    }

    public function getMediaUrl($media_path)
    {
        return $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . $media_path;
    }
}
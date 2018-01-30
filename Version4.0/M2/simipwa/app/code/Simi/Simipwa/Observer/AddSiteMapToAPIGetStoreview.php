<?php
/**
 * Created by PhpStorm.
 * User: scott
 * Date: 1/29/18
 * Time: 9:28 PM
 */

namespace Simi\Simipwa\Observer;

use Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;

class AddSiteMapToAPIGetStoreview implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface as ObjectManager
     */
    private $simiObjectManager;

    public function __construct(ObjectManager $simiObjectManager)
    {
        $this->simiObjectManager = $simiObjectManager;
    }

    /**
     * Add site map data to api get storeview
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $observerObject = $observer->getObject();
        $data = $observerObject->getData();
        if (isset($data['params']) && isset($data['params']['pwa'])) {
            $obj = $observer['object'];
            $info = $obj->storeviewInfo;
            $siteMap = $this->simiObjectManager->get('\Simi\Simipwa\Helper\Data')->getSiteMaps($data['resourceid']);
            if ($siteMap && isset($siteMap['sitemaps']))
                $info['urls'] = $siteMap['sitemaps'];
            $obj->storeviewInfo = $info;
        }
    }
}
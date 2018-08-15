<?php
/**
 * Created by PhpStorm.
 * User: liam
 * Date: 5/11/18
 * Time: 11:37 AM
 */
namespace Simi\Simideeplink\Observer;

use Magento\Framework\Event\ObserverInterface;

class SimiSimiconnectorModelServerInitialize implements ObserverInterface
{

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'deeplinks') {
            $observerObjectData['module'] = 'simideeplink';
        }
        $observerObject->setData($observerObjectData);
    }
}
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Simi\Simirewardpoints\Observer\Simiobserver;

use Magento\Framework\Event\ObserverInterface;

/**
 * Description of ChangeApiResource
 *
 * @author scott
 */
class SimiSimiconnectorModelServerInitialize implements ObserverInterface
{

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();
        if ($observerObjectData['resource'] == 'simirewardpoints') {
            $observerObjectData['module'] = 'simirewardpoints';
        } elseif ($observerObjectData['resource'] == 'simirewardpointstransactions') {
            $observerObjectData['module'] = 'simirewardpoints';
        }
        $observerObject->setData($observerObjectData);
    }
}

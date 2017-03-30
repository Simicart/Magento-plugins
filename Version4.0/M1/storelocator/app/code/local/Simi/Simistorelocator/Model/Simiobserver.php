<?php

class Simi_Simistorelocator_Model_Simiobserver {

    public function simiSimiconnectorModelServerInitializeSimistorelocator($observer) {
        $observerObject = $observer->getObject();
        $observerObjectData = $observerObject->getData();        
        if ($observerObjectData['resource'] == 'storelocations') {
            $observerObjectData['module'] = 'simistorelocator';
        } 
        if ($observerObjectData['resource'] == 'storelocatortags') {
            $observerObjectData['module'] = 'simistorelocator';
        }
        $observerObject->setData($observerObjectData);
    }
}

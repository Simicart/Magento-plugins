<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/3/16
 * Time: 9:37 PM
 */
class Simi_Simistorelocator_Model_Api_Storelocatortags extends Simi_Simiconnector_Model_Api_Abstract {

    protected $_DEFAULT_ORDER = 'tag_id';

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            $storeCollection = Mage::getModel('simistorelocator/simistorelocator')->getCollection()
                    ->addFieldToFilter('status', 1);
            $typeID = Mage::helper('simiconnector')->getVisibilityTypeId('storelocator');
            $visibilityTable = Mage::getSingleton('core/resource')->getTableName('simiconnector/visibility');
            $storeCollection->getSelect()
                    ->join(array('visibility' => $visibilityTable), 'visibility.item_id = main_table.simistorelocator_id AND visibility.content_type = ' . $typeID . ' AND visibility.store_view_id =' . Mage::app()->getStore()->getId());


            if (!$storeCollection->getSize()) {
                throw new Exception(Mage::helper('catalog')->__('There is No Store'), 4);
            }
            $storeIds = $storeCollection->getAllIds();
            $tagCollection = Mage::getModel('simistorelocator/tag')->getCollection()
                    ->addFieldToFilter('simistorelocator_id', $storeIds);
            $tagCollection->getSelect()->group('value');
            $this->builderQuery = $tagCollection;
        }
    }

}

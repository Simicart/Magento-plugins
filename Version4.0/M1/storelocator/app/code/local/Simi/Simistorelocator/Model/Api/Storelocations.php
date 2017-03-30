<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/3/16
 * Time: 9:37 PM
 */
class Simi_Simistorelocator_Model_Api_Storelocations extends Simi_Simiconnector_Model_Api_Abstract {

    protected $_DEFAULT_ORDER = 'name';
    public $ylat;
    public $ylng;

    public function setBuilderQuery() {
        $data = $this->getData();
        $this->ylat = $data['params']['lat'];
        $this->ylng = $data['params']['lng'];

        if ($data['resourceid']) {
            
        } else {
            $this->builderQuery = $this->getStoreList();
        }
    }

    public function getStoreList() {
        $data = $this->getData();
        $storelocatorcollections = Mage::getModel('simistorelocator/simistorelocator')->getCollection()
                ->addFieldToFilter('status', 1);
        $typeID = Mage::helper('simiconnector')->getVisibilityTypeId('storelocator');
        $visibilityTable = Mage::getSingleton('core/resource')->getTableName('simiconnector/visibility');
        $storelocatorcollections->getSelect()
                ->join(array('visibility' => $visibilityTable), 'visibility.item_id = main_table.simistorelocator_id AND visibility.content_type = ' . $typeID . ' AND visibility.store_view_id =' . Mage::app()->getStore()->getId());
        $this->searchArea($data, $storelocatorcollections);

        $storeIds = array();
        if (isset($data['params']['tag'])) {
            $storeIds = $this->getStoreToTag($data['params']['tag']);
        }
        if (count($storeIds))
            $storelocatorcollections->addFieldToFilter('simistorelocator_id', array('in' => $storeIds));
        return $storelocatorcollections;
    }

    /*
     * Return Store Information
     */

    public function show() {
        $data = $this->getData();
        $result = parent::show();
        return $result;
    }

    /*
     * Show Store List
     */

    public function index() {
        $storeArray = array();
        if ($this->ylng != 0 && $this->ylat != 0) {
            foreach ($this->getStoreList() as $item) {
                $latitude = $item->getLatitude();
                $longtitude = $item->getLongtitude();
                $distance = $this->calculationByDistance($this->ylat, $this->ylng, $latitude, $longtitude);
                $storeArray[(string) $item->getId()] = $distance;
            }
            asort($storeArray);
            $this->builderQuery->getSelect()->order(new Zend_Db_Expr('FIELD(simistorelocator_id, "' . implode('","', array_keys($storeArray)) . '") ASC'));
        }
        $result = parent::index();
        foreach ($result['storelocations'] as $index => $storeReturn) {
            $distance = 0;
            $item = Mage::getModel('simistorelocator/simistorelocator')->load($storeReturn['simistorelocator_id']);
            $latitude = $item->getLatitude();
            $longtitude = $item->getLongtitude();
            if ($this->ylng != 0 && $this->ylat != 0) {
                $distance = $this->calculationByDistance($this->ylat, $this->ylng, $latitude, $longtitude);
            }
            $storeReturn["special_days"] = Mage::helper('simistorelocator')->getSpecialDays($item->getId());
            $storeReturn["holiday_days"] = Mage::helper('simistorelocator')->getHolidayDays($item->getId());
            $storeReturn["country_name"] = $item->getCountryName();
            $storeReturn["distance"] = $distance;
            $storeReturn["image"] = Mage::helper("simistorelocator")->getBigImagebyStore($storeReturn['simistorelocator_id']);
            $result['storelocations'][$index] = $storeReturn;
        }
        return $result;
    }

    public function calculationByDistance($mlat, $mlng, $lat, $lng) {
        $latFrom = deg2rad($mlat);
        $lonFrom = deg2rad($mlng);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);
        $earthRadius = 6371000;
        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
                pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }

    public function searchArea($data, $collection) {
        $data = (object) $data['params'];
        if (isset($data->country) && $data->country && $data->country != "") {
            $collection->addFieldToFilter('country', array('like' => '%' . $data->country . '%'));
        }
        if (isset($data->city) && ($data->city != null) && $data->city != "") {
            $city = trim($data->city);
            $collection->addFieldToFilter('city', array('like' => '%' . $city . '%'));
        }
        if (isset($data->state) && ($data->state != null) && $data->state != "") {
            $state = trim($data->state);
            $collection->addFieldToFilter('state', array('like' => '%' . $state . '%'));
        }
        if (isset($data->zipcode) && ($data->zipcode != null) && $data->zipcode != "") {
            $zipcode = trim($data->zipcode);
            $collection->addFieldToFilter('zipcode', array('like' => '%' . $zipcode . '%'));
        }
        return $collection;
    }

    public function getStoreToTag($value) {
        $storeIds = array();
        $tagCollection = Mage::getModel('simistorelocator/tag')->getCollection()
                ->addFieldToFilter('value', $value);

        foreach ($tagCollection as $item) {
            if (!in_array($item->getData("simistorelocator_id"), $storeIds)) {
                $storeIds[] = $item->getData("simistorelocator_id");
            }
        }
        return $storeIds;
    }

    protected function _order($parameters) {
        return;
    }

}

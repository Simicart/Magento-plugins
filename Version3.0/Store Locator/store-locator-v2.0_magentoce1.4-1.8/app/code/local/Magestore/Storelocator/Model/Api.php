<?php

class Magestore_Storelocator_Model_Api extends Simi_Connector_Model_Abstract {

    const DISTANCEURL = 'http://maps.googleapis.com/maps/api/distancematrix/json?';
    const GPARAM = "&sensor=true&key=";

    public $_arrStore = array();

    //function getlist store    
    public function getStoreList($data) {
        return $this->getStoreByDistance($data);
    }

    public function getStoreListCollection() {
        $collections = Mage::getModel('storelocator/storelocator')->getCollection()
                ->addFieldToFilter('status', 1);
        $collections->setOrder('name', 'ASC');
        return $collections;
    }

    public function convertData($collection) {
        $data = array();
        $info = array();
        foreach ($collection as $item) {
            $storeInfo = $item->getData();
            $storeInfo["special_days"] = Mage::helper('storelocator')->getSpecialDays($item->getId());
            $storeInfo["holiday_days"] = Mage::helper('storelocator')->getHolidayDays($item->getId());
            $data[] = $storeInfo;
        }
        $info["stores"] = $data;
        //$info["tags"] = $this->getTagList($collection);
        $status = $this->statusSuccess();
        $status["data"] = $info;
        return $status;
    }

    public function getSearchConfig() {
        $choose_search = Mage::helper('storelocator')->getConfig('choose_search');
        $search_config = explode(',', $choose_search);
        $status = $this->statusSuccess();
        $status["data"] = $search_config;
        return $status;
    }
	
	public function getSearchConfigIos() {
        $choose_search = Mage::helper('storelocator')->getConfig('choose_search');
        $search_config = array();
		$search_config["config"] = explode(',', $choose_search);	
        $status = $this->statusSuccess();
        $status["data"] = $search_config;
        return $status;
    }
	
    public function getTagList($data) {
        $list = array();
        $limit = $data->limit;
        $offset = $data->offset;
        $check_limit = 0;
        $check_offset = 0;
        $storeCollection = $this->getStoreListCollection();
        if (!$storeCollection->getSize()) {
            $status = $this->statusSuccess();
            $status["data"] = $list;
            return;
        }

        $storeIds = $storeCollection->getAllIds();

        $tagCollection = Mage::getModel('storelocator/tag')->getCollection()
                ->addFieldToFilter('storelocator_id', $storeIds);

        $tagCollection->getSelect()->group('value');
        foreach ($tagCollection as $tag) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;
            $list[] = array(
                'value' => $tag->getValue(),
                    // 'ids' => $this->getIdsToTag($tag->getValue())
            );
        }


        $status = $this->statusSuccess();
        $status["data"] = $list;
        return $status;
    }

    public function getStoreToTag($value) {

        $storeIds = array();
        $tagCollection = Mage::getModel('storelocator/tag')->getCollection()
                ->addFieldToFilter('value', $value);

        foreach ($tagCollection as $item) {
            if (!in_array($item->getData("storelocator_id"), $storeIds)) {
                $storeIds[] = $item->getData("storelocator_id");
            }
        }
		return $storeIds;
    }

    public function getStoreByDistance($data) {
        $storeIds = array();
        if (isset($data->tag)) {
            $storeIds = $this->getStoreToTag($data->tag);
        }
		
        $ylat = 0;
        if (isset($data->lat)) {
            $ylat = $data->lat;
        }

        $ylng = 0;
        if (isset($data->lng)) {
            $ylng = $data->lng;
        }
        
        $limit = $data->limit;
        $offset = $data->offset;
        $storeList = array();
        $collection = $this->getStoreListCollection();
        $this->searchArea($data, $collection);
        
        foreach ($collection as $item) {			
			if(count($storeIds) != 0){				
				if (in_array($item->getId(), $storeIds)){
					$data = $item->getData();
					$latitude = $item->getLatitude();
					$longtitude = $item->getLongtitude();
					$distance = 0;
					if ($ylng != 0 && $ylat != 0) {
						$distance = $this->calculationByDistance($ylat, $ylng, $latitude, $longtitude);
					}
					$data["country_name"] = $item->getCountryName();
					$data["distance"] = $distance;
					$data["image"] = Mage::helper("storelocator")->getBigImagebyStore($item->getId());
					$this->sortList($distance, $data);
				}				
			}else{
				$data = $item->getData();
				$latitude = $item->getLatitude();
				$longtitude = $item->getLongtitude();
				$distance = 0;
				if ($ylng != 0 && $ylat != 0) {
					$distance = $this->calculationByDistance($ylat, $ylng, $latitude, $longtitude);
				}
				$data["distance"] = $distance;
				$data["image"] = Mage::helper("storelocator")->getBigImagebyStore($item->getId());
				$data["country_name"] = $item->getCountryName();				
				$this->sortList($distance, $data);
			}            
        }
        $check_limit = 0;
        $check_offset = 0;
        foreach ($this->_arrStore as $store) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;

            $store["special_days"] = Mage::helper('storelocator')->getSpecialDays($store["storelocator_id"]);
            $store["holiday_days"] = Mage::helper('storelocator')->getHolidayDays($store["storelocator_id"]);
            $storeList[] = $store;
        }

        $status = $this->statusSuccess();
        $status["data"] = $storeList;
        return $status;
    }
	
	public function getStoreByDistanceMap($data){
		$ylat = 0;
        if (isset($data->lat)) {
            $ylat = $data->lat;
        }

        $ylng = 0;
        if (isset($data->lng)) {
            $ylng = $data->lng;
        }
        
        $limit = $data->limit;
        $offset = $data->offset;
        $storeList = array();
		$collection = $this->getStoreListCollection();
		foreach ($collection as $item) {
            $data = $item->getData();
            $latitude = $item->getLatitude();
            $longtitude = $item->getLongtitude();
            $distance = 0;
            if ($ylng != 0 && $ylat != 0) {
                $distance = $this->calculationByDistance($ylat, $ylng, $latitude, $longtitude);
            }
            $data["distance"] = $distance;            
            $this->sortList($distance, $data);
        }
        $check_limit = 0;
        $check_offset = 0;
        foreach ($this->_arrStore as $store) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;
           
            $storeList[] = $store;
        }

        $status = $this->statusSuccess();
        $status["data"] = $storeList;
		return $status;
	}
	
	public function calculationByDistanceXY($mlat, $mlng, $lat, $lng){
		$x = abs($mlat - $lat);
		$y = abs($mlng - $lng);
		$distance = sqrt($x*$x + $y*$y);
		return $distance;
	}
	
	
    public function calculationByDistance($mlat, $mlng, $lat, $lng) {
        // convert from degrees to radians
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
        // Zend_debug::dump($angle * $earthRadius);die();
        return $angle * $earthRadius;
    }

    public function sortList($distance, $infoStore) {
        foreach ($this->_arrStore as $key => $item) {
            if ($item["distance"] > $distance) {
                $this->insertArray($key, $infoStore);
                return;
            }
        }
        // die("xxxxx");
        $this->_arrStore[] = $infoStore;
        return;
    }

    public function insertArray($position, $infoStore) {
        $count = count($this->_arrStore);
        $cache = $this->_arrStore[$position];
        $this->_arrStore[$position] = $infoStore;
        for ($i = $position + 1; $i <= $count; $i++) {
            $cahe2 = $this->_arrStore[$i];
            $this->_arrStore[$i] = $cache;
            $cache = $cahe2;
        }
    }

    public function getAllowedCountries() {
        $list = array();
        $country_default = Mage::helper('storelocator')->getConfig('default_country');
        $countries = Mage::getResourceModel('directory/country_collection')->loadByStore();
        $cache = null;
        foreach ($countries as $country) {
            if ($country_default == $country->getId()) {
                $cache = array(
                    'country_code' => $country->getId(),
                    'country_name' => $country->getName(),
                    'states' => $this->getStates($country->getId(), 0),
                );
            } else {
                $list[] = array(
                    'country_code' => $country->getId(),
                    'country_name' => $country->getName(),
                    'states' => $this->getStates($country->getId(), 0),
                );
            }
        }
        if ($cache) {
            array_unshift($list, $cache);
        }
        $information = $this->statusSuccess();
        $information['data'] = $list;

        return $information;
    }

    public function getStates($data, $key = 1) {
        $code = $data;
        if ($key == 1) {
            $code = $data->country_code;
        }
        $list = array();
        if ($code) {
            $states = Mage::getModel('directory/country')->loadByCode($code)->getRegions();
            foreach ($states as $state) {
                $list[] = array(
                    'state_id' => $state->getRegionId(),
                    'state_name' => $state->getName(),
                    'state_code' => $state->getCode(),
                );
            }
            if ($key == 0)
                return $list;
            $information = $this->statusSuccess();
            $information['data'] = $list;
            return $information;
        } else {
            $information = $this->statusError();
            return $information;
        }
    }

    public function searchArea($data, $collection) {
        if (isset($data->country) && $data->country && $data->country != "") {
            $collection->addFieldToFilter('country', array('like' => '%' . $data->country . '%'));
        }
        if (isset($data->city) && ($data->city != null) && $data->city != "") {
            $city = trim($data->state);
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

}

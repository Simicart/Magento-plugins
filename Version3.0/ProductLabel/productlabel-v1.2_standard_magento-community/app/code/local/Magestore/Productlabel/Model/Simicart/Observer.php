<?php

class Magestore_Productlabel_Model_Simicart_Observer {

    public function setProduct($observer) {
        $storeId = Mage::app()->getStore()->getId();
        if (!Mage::helper('productlabel')->getIsActiveLabel($storeId) || (int)Mage::getStoreConfig('productlabel/general/enable') == 0) {
            return;
        }
        $object = $observer->getObject();
        $product = $observer->getProduct();
        $productId = $product->getId();
        $image_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'productlabel/label/';
        $collection = Mage::getModel('productlabel/productlabelentity')->getCollection()->setStoreId($storeId);
        $label = $collection->addFieldToFilter('product_id', $productId)->getFirstItem();
        $product_label = array();
        $check = false;
        if ($label->getId() && $label->getDisplay() == 1) {			
            if ($label->getImage() == null)
                $image_url .= 'default/default_productdetail_label.png';
            else
                $image_url .= $label->getImage();
            $content = Mage::helper('productlabel')->convertBackendString($label->getText(), $product);
            $product_label[] = array(
                'image' => $image_url,
                'content' => $content,
                'position' => $label->getPosition(),
            );
            $check = true;
        }else {
            $labels = $this->getAllCategoryLabel($productId, $storeId);
            foreach ($labels as $l) {
                $flag = 1;
                $current_date = strtotime(date('Y-m-d H:m:s'));
                if ($l->getToTime()) {

                    if ($l->getFromTime() <= $current_date && $current_date <= $l->getToTime()) {
                        $flag = 1;
                    }
                    else
                        $flag = 0;
                }
                else {
                    if ($l->getFromTime()) {
                        if ($l->getFromTime() > $current_date)
                            $flag = 0;
                    }
                }
				if ($flag) {
					$image_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'productlabel/label/';
					if ($l->getImage() == null)
						$image_url .= 'default/default_productdetail_label.png';
					else
						$image_url .= $l->getImage();
					$content = Mage::helper('productlabel')->convertBackendString($l->getText(), $product);
					$product_label[] = array(
						'image' => $image_url,
						'content' => $content,
						'position' => $l->getPosition(),
					);
					$check = true;
				}
            }
            
        }
	
        if ($check) {
            $data = $object->getCacheData();
            $data['product_label'] = $product_label;			
            $object->setCacheData($data, "magestore_productlabel");
        }
        return;
    }

    public function setProducts($observer) {
        $this->setProductCategory($observer);
        return;
    }
	
	public function setSpotProduct($observer){
		$this->setProductCategory($observer);
        return;
	}
	
	public function setSearchProduct($observer){
	    $this->setProductCategory($observer);
        return;
	}
	
    public function setProductCategory($observer) {
        $storeId = Mage::app()->getStore()->getId();
        if (!Mage::helper('productlabel')->getIsActiveLabel($storeId) || (int)Mage::getStoreConfig('productlabel/general/enable') == 0) {
            return;
        }

        $object = $observer->getObject();
        $product = $observer->getProduct();
        $productId = $product->getId();
        $image_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'productlabel/label/';
        $collection = Mage::getModel('productlabel/productlabelentity')->getCollection()->setStoreId($storeId);
        $label = $collection->addFieldToFilter('product_id', $productId)->getFirstItem();
        $product_label = array();
        $check = false;
        if ($label->getId() && $label->getCategoryDisplay() == 1) {
            if ($label->getCategoryImage() == null)
                $image_url .= 'default/default_category_label.png';
            else
                $image_url .= $label->getCategoryImage();
            $content = Mage::helper('productlabel')->convertBackendString($label->getCategoryText(), $product);
            $product_label[] = array(
                'image' => $image_url,
                'content' => $content,
                'position' => $label->getCategoryPosition(),
            );
            $check = true;
        } else {
            $labels = $this->getAllCategoryLabel($productId, $storeId);
            foreach ($labels as $l) {
                $flag = 1;
                $current_date = strtotime(date('Y-m-d H:m:s'));
                if ($l->getToTime()) {

                    if ($l->getFromTime() <= $current_date && $current_date <= $l->getToTime()) {
                        $flag = 1;
                    }
                    else
                        $flag = 0;
                }
                else {
                    if ($l->getFromTime()) {
                        if ($l->getFromTime() > $current_date)
                            $flag = 0;
                    }
                }
                if ($flag) {
                    $image_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'productlabel/label/';
                    if ($l->getCategoryImage() == null)
                        $image_url .= 'default/default_category_label.png';
                    else
                        $image_url .= $l->getCategoryImage();
                    $content = Mage::helper('productlabel')->convertBackendString($l->getCategoryText(), $product);
                    $product_label[] = array(
                        'image' => $image_url,
                        'content' => $content,
                        'position' => $l->getCategoryPosition(),
                    );
                    $check = true;
                }
            }
        }

        if ($check) {
            $data = $object->getCacheData();
            $data['product_label'] = $product_label;
            $object->setCacheData($data, "magestore_productlabel");
        }
        return;
    }

    public function getAllCategoryLabel($productId, $storeId) {

        $product_label_flat_data = Mage::getModel('core/resource')->getTableName('product_label_flat_data');
        $select = '(select category_position,product_id,max(priority) 
            from ' . $product_label_flat_data . ' group by store_id,category_position, product_id )';
        $colection = Mage::getModel('productlabel/productlabelflatdata')->getCollection();
        $colection->addFieldToFilter('product_id', $productId)->addFieldToFilter('category_display', 1)->addFieldToFilter('store_id', $storeId)->getSelect()->where('(category_position,product_id,priority) IN ' . $select)->group(array('category_position', 'priority'));
        return $colection;
    }

}
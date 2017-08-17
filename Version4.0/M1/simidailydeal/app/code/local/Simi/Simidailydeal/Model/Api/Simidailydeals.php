<?php
class Simi_Simidailydeal_Model_Api_Simidailydeals extends Simi_Simiconnector_Model_Api_Abstract {
    protected $_layer = array();
    protected $_allow_filter_core = false;
    protected $_helperProduct;
    protected $_sortOrders = array();
    protected $_DEFAULT_ORDER = 'id';
    public $detail_info;

    public function setBuilderQuery(){
        $data = $this->getData();
        $this->builderQuery = Mage::getModel('simidailydeal/dailydeal')->getDailydeals();
    }

    public function index(){
        $collection = $this->builderQuery;
        $this->filter();
        $data = $this->getData();
        $parameters = $data['params'];
        $page = 1;
        if (isset($parameters[self::PAGE]) && $parameters[self::PAGE]) {
            $page = $parameters[self::PAGE];
        }

        $limit = self::DEFAULT_LIMIT;
        if (isset($parameters[self::LIMIT]) && $parameters[self::LIMIT]) {
            $limit = $parameters[self::LIMIT];
        }
        $offset = $limit * ($page - 1);
        if (isset($parameters[self::OFFSET]) && $parameters[self::OFFSET]) {
            $offset = $parameters[self::OFFSET];
        }
        $collection->setPageSize($offset + $limit);

        $all_ids = array();
        $info = array();
        $total = $collection->getSize();

        if ($offset > $total)
            throw new Exception($this->_helper->__('Invalid method.'), 4);

        $fields = array();
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }

        $fields = array();
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }

        $check_limit = 0;
        $check_offset = 0;

        if (isset($parameters['image_width'])){
            $image_width = $parameters['image_width'];
            $image_height = $parameters['image_height'];
        } else {
            $image_width = 600;
            $image_height = 600;
        }

        foreach ($collection as $dailydeal) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;

            $product = Mage::getModel('catalog/product')->load($dailydeal->getProductId());
            $info_detail = $product->toArray($fields);

            $all_ids[] = $product->getId();

            $info_dailydeal = $dailydeal->toArray();
            $info_dailydeal['title'] = Mage::helper('simidailydeal')->getDailydealTitle($info_dailydeal['title'],$info_dailydeal['product_name'],$info_dailydeal['save']);
            $info_dailydeal['deal_price'] = Mage::app()->getStore()->convertPrice($info_dailydeal['deal_price']);

            $deal_time = Mage::getModel('core/date')->timestamp($info_dailydeal['close_time'])-Mage::getModel('core/date')->timestamp($info_dailydeal['start_time']);
            $info_dailydeal['deal_time'] = $deal_time;
            $info_dailydeal['time_left'] = (Mage::getModel('core/date')->timestamp($info_dailydeal['close_time']) - Mage::getModel('core/date')->timestamp(time()));
            $info_dailydeal['currency'] = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

            $images = array();
            $imagelink = Mage::helper('simiconnector/products')->getImageProduct($product, null, $image_width, $image_height);
            //$sizes = getimagesize($imagelink);
            $images[] = array(
                'url' => $imagelink,
                'position' => 1
            );

            $ratings = Mage::helper('simiconnector/review')->getRatingStar($product->getId());
            $total_rating = Mage::helper('simiconnector/review')->getTotalRate($ratings);
            $avg = Mage::helper('simiconnector/review')->getAvgRate($ratings, $total_rating);

            $info_detail['images'] = $images;
            $info_detail['app_prices'] = Mage::helper('simiconnector/price')->formatPriceFromProduct($product, true);
            $info_detail['app_reviews'] = array(
                'rate' => $avg,
                'number' => $ratings[5],
            );
            $info_detail['product_label'] = Mage::helper('simiconnector/productlabel')->getProductLabel($product);
            $info_detail['dailydeal'] = $info_dailydeal;
            $info[] = $info_detail;
        }
        return $this->getList($info, $all_ids, $total, $limit, $offset);
    }

    public function setPluralKey(){
        $this->pluralKey = 'products';
        return $this;
    }
}
?>
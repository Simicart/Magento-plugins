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
        {}
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

        $check_limit = 0;
        $check_offset = 0;

        if (isset($parameters['image_width'])){
            $image_width = $parameters['image_width'];
            $image_height = $parameters['image_height'];
        } else {
            $image_width = 600;
            $image_height = 600;
        }



        foreach ($collection as $giftcode) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;

            $info_detail = $giftcode->toArray($fields);
            $all_ids[] = $giftcode->getId();
            $product = Mage::getModel('catalog/product')->load($info_detail['product_id']);

            $info_detail['title'] = Mage::helper('simidailydeal')->getDailydealTitle($info_detail['title'],$info_detail['product_name'],$info_detail['save']);

            $images = array();
            $imagelink = Mage::helper('simiconnector/products')->getImageProduct($product, null, $image_width, $image_height);
            //$sizes = getimagesize($imagelink);
            $images[] = array(
                'url' => $imagelink,
                'position' => 1
            );

            $info_detail['product_info'] = array(
                'price' => Mage::app()->getStore()->convertPrice($product->getPrice()),
                'url_key' => $product->getUrlKey(),
                'url_path'  => $product->getUrlPath(),
                'images'    => $images
            );
            $info_detail['deal_price'] = Mage::app()->getStore()->convertPrice($info_detail['deal_price']);

            $deal_time = Mage::getModel('core/date')->timestamp($info_detail['close_time'])-Mage::getModel('core/date')->timestamp($info_detail['start_time']);
            $info_detail['deal_time'] = $deal_time;
            $info_detail['time_left'] = (Mage::getModel('core/date')->timestamp($info_detail['close_time']) - Mage::getModel('core/date')->timestamp(time()));
            $info_detail['currency'] = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
            $info[] = $info_detail;
        }
        return $this->getList($info, $all_ids, $total, $limit, $offset);
    }
}
?>
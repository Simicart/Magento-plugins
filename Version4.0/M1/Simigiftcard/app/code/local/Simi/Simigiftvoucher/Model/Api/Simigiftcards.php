<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/5/17
 * Time: 9:37 AM
 */
class Simi_Simigiftvoucher_Model_Api_Simigiftcards extends Simi_Simiconnector_Model_Api_Abstract{
    protected $_DEFAULT_ORDER = 'giftcard_product_id';
    protected $_helperProduct;
    protected $_layer = array();
    protected $_allow_filter_core = false;
    protected $_sortOrders = array();
    public $detail_info;

    public function setBuilderQuery(){
        $data = $this->getData();

        $this->_helperProduct = Mage::helper('simiconnector/products');
        $this->_helperProduct->setData($data);
        if (isset($data['resourceid']) && $data['resourceid'] && $data['resourceid'] != 'uploadimage'){
            $this->builderQuery = $this->_helperProduct->getProduct($data['resourceid']);
        } else {
            $this->builderQuery = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('type_id','simigiftvoucher');
        }
    }

    /**
     * @param $info
     * @param $all_ids
     * @param $total
     * @param $page_size
     * @param $from
     * @return array
     * override
     */
    public function getList($info, $all_ids, $total, $page_size, $from)
    {
        return array(
            'all_ids' => $all_ids,
            $this->getPluralKey() => $info,
            'total' => $total,
            'page_size' => $page_size,
            'from' => $from,
            'layers' => $this->_layer,
            'orders' => $this->_sortOrders,
        );

    }

    /**
     * @return collection
     * override
     */
    protected function filter()
    {

        if (!$this->FILTER_RESULT)
            return;
        $data = $this->_data;

        $parameters = $data['params'];
        if ($this->_allow_filter_core) {
            $query = $this->builderQuery;
            $this->_whereFilter($query, $parameters);

        }

        if (isset($parameters['dir']) && isset($parameters['order'])){

            $this->_order($parameters);
        }

        return null;
    }

    /**
     * @return array
     * @throws Exception
     * override
     */
    public function index()
    {
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

        $check_limit = 0;
        $check_offset = 0;

        if (isset($parameters['image_width'])){
            $image_width = $parameters['image_width'];
            $image_height = $parameters['image_height'];
        } else {
            $image_width = 600;
            $image_height = 600;
        }

        foreach ($collection as $entity) {
            $entity_product = Mage::getModel('catalog/product')->load($entity->getId());
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;
            $info_detail = $entity_product->toArray($fields);
            $all_ids[] = $entity->getId();
            //zend_debug::dump($);die('xx');
            $images = array();
            $imagelink = $this->_helperProduct->getImageProduct($entity, null, $image_width, $image_height);
            //$sizes = getimagesize($imagelink);
            $images[] = array(
                'url' => $imagelink,
                'position' => 1,
//                'image_width' => $sizes[0],
//                'image_height' => $sizes[1],
            );
            $ratings = Mage::helper('simiconnector/review')->getRatingStar($entity->getId());
            $total_rating = Mage::helper('simiconnector/review')->getTotalRate($ratings);
            $avg = Mage::helper('simiconnector/review')->getAvgRate($ratings, $total_rating);

            $info_detail['images'] = $images;

            $info_detail['app_prices'] =  Mage::helper('simigiftvoucher/giftproduct')->formatPriceFromProduct($entity_product, true);
            $info_detail['app_reviews'] = array(
                'rate' => $avg,
                'number' => $ratings[5],
            );
            $info_detail['product_label'] = Mage::helper('simiconnector/productlabel')->getProductLabel($entity);
            $info[] = $info_detail;


        }

        return $this->getList($info, $all_ids, $total, $limit, $offset);
    }

    /**
     * @return array
     * override
     */
    public function show()
    {
        $entity = $this->builderQuery;
        $data = $this->getData();
        $parameters = $data['params'];
        $fields = array();
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        $info = $entity->toArray($fields);
        $media_gallery = $entity->getMediaGallery();
        $images = array();

        if (isset($parameters['image_width'])){
            $image_width = $parameters['image_width'];
            $image_height = $parameters['image_height'];
        } else {
            $image_width = 600;
            $image_height = 600;
        }

        foreach ($media_gallery['images'] as $image) {
            // Zend_debug::dump($image['disabled']);
            if ($image['disabled'] == 0) {
                $imagelink = $this->_helperProduct->getImageProduct($entity, $image['file'], $image_width, $image_height);
                //$sizes = getimagesize($imagelink);
                $images[] = array(
                    'url' => $imagelink,
                    'position' => $image['position'],
//                    'image_width' => $sizes[0],
//                    'image_height' => $sizes[1],
                );
            }
        }
        if (count($images) == 0) {
            $imagelink = $this->_helperProduct->getImageProduct($entity, null, $image_width, $image_height);
            //$sizes = getimagesize($imagelink);
            $images[] = array(
                'url' => $imagelink,
                'position' => 1,
//                'image_width' => $sizes[0],
//                'image_height' => $sizes[1],
            );
        }
        if (!Mage::registry('product') && $entity->getId()) {
            Mage::register('product', $entity);
        }

        $block_att = Mage::getBlockSingleton('catalog/product_view_attributes');
        $_additional = $block_att->getAdditionalData();

        $ratings = Mage::helper('simiconnector/review')->getRatingStar($entity->getId());
        $total_rating = Mage::helper('simiconnector/review')->getTotalRate($ratings);
        $avg = Mage::helper('simiconnector/review')->getAvgRate($ratings, $total_rating);

        // Add field product type gift card

        $info['description'] = Mage::helper('catalog/output')->productAttribute($entity, $entity->getDescription(), 'description');
        $info['simigift_template_ids'] = Mage::getModel('simigiftvoucher/simimapping')->getTemplateInProduct($info['simigift_template_ids']);

        /*$info['conditions'] = Mage::getModel('simigiftvoucher/simimapping')->getConditionProduct($entity->getId());
        $info['conditions']['conditions_serialized'] = unserialize($info['conditions']['conditions_serialized']);
        $info['conditions']['actions_serialized'] = unserialize($info['conditions']['actions_serialized']);*/

        $info['additional'] = $_additional;
        $info['images'] = $images;
        $info['app_prices'] =  Mage::helper('simigiftvoucher/giftproduct')->formatPriceFromProduct($entity, true);
        $info['giftcard_prices'] = Mage::getModel('simigiftvoucher/simimapping')->getGiftAmount(Mage::getModel('catalog/product')->load($entity->getId()));
        //$info['app_tier_prices'] = Mage::helper('simiconnector/tierprice')->formatTierPrice($entity);
        $info['app_reviews'] = array(
            'rate' => $avg,
            'number' => $ratings[5],
            '5_star_number' => $ratings[4],
            '4_star_number' => $ratings[3],
            '3_star_number' => $ratings[2],
            '2_star_number' => $ratings[1],
            '1_star_number' => $ratings[0],
            'form_add_reviews' => Mage::helper('simiconnector/review')->getReviewToAdd(),
        );

        $info['app_options'] = Mage::helper('simiconnector/options')->getOptions($entity);

        $info['wishlist_item_id'] = Mage::helper('simiconnector/wishlist')->getWishlistItemId($entity);
        $info['product_label'] = Mage::helper('simiconnector/productlabel')->getProductLabel($entity);
        $info['product_video'] = Mage::helper('simiconnector/simivideo')->getProductVideo($entity);

        $enableCustomDesign = Mage::helper('simigiftvoucher')->getInterfaceConfig('custom_image');
        $info['simigiftcard_settings']= array(
            'simigift_template_upload' => $enableCustomDesign,
            'simigift_postoffice' => Mage::helper('simigiftvoucher')->getInterfaceConfig('postoffice'),
            'simigift_message_max' => Mage::helper('simigiftvoucher')->getInterfaceConfig('max'),
            'is_day_to_send' => Mage::helper('simigiftvoucher')->getInterfaceConfig('schedule')
        );
        //$info['timezones'] = Mage::getModel('core/locale')->getOptionTimezones();
        $this->detail_info = $this->getDetail($info);
        Mage::dispatchEvent('simi_simiconnector_model_api_giftcard_show_after', array('object' => $this, 'data' => $this->detail_info));
        return $this->detail_info;
    }

    /**
     * Upload image template gift card  *
     */
    public function store(){
        $data = $this->getData();
        if ($data['resourceid'] == 'uploadimage'){
            $result = array();
            if (isset($_FILES['image'])) {
                //echo json_encode($_FILES['image']);die;
                $error = $_FILES["image"]["error"];
                if ($_FILES['image']['size'] > 2097152){
                    throw new Exception(Mage::helper('simigiftvoucher')->__('The uploaded image exceeds 2Mb !'),4);
                }
                if ( $error > 0 ) {
                    throw new Exception(Mage::helper('simigiftvoucher')->__('The uploaded image error! Please try again.'),4);
                }
                try {
                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    Mage::helper('simigiftvoucher')->createImageFolderHaitv('', '', true);
                    $fileName = $_FILES['image']['name'];
                    $result = $uploader->save(Mage::getBaseDir('media') . DS . 'tmp' . DS . 'simigiftvoucher' . DS .
                        'images' . DS, $fileName);
                    $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
                    $result['path'] = str_replace(DS, "/", $result['path']);
                    $result['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA,array('_secure'=>true)) .
                        'tmp/simigiftvoucher/images/' . $result['file'];

                    $result['filename'] = $fileName;
                    $result['sucess'] = true;
                } catch (Exception $e) {
                    throw new Exception(array('error' => $e->getMessage(), 'errorcode' => $e->getCode()),4);
                }
            } else {

                throw new Exception(Mage::helper('simigiftvoucher')->__('Image Saving Error!'),4);
            }
            return array('images'=>$result);
        }
    }
}
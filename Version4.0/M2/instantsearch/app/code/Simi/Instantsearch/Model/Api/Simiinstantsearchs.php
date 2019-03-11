<?php
namespace Simi\Instantsearch\Model\Api;

class Simiinstantsearchs extends \Simi\Simiconnector\Model\Api\Apiabstract
{

    public function setBuilderQuery()
    {

        $data                 = $this->getData();
        $parameters           = $data['params'];
        $this->searchHelper = $this->simiObjectManager->get('\Simi\Instantsearch\Helper\Search');
        $this->searchHelper->setData($data);

        if ($data['resourceid']) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Query is invalid.'), 4);
        } else {
            if (isset($parameters['q'])) {
                $this->is_search = 1;
                $this->setFilterByQuery();
            } else {
                throw new \Simi\Simiconnector\Helper\SimiException(__('Query is invalid.'), 4);
            }
        }
    }

    public function filter()
    {
        
        $data       = $this->data;
        $parameters = $data['params'];
        
        $this->_order($parameters);

        return null;
    }

    public function index()
    {
        $collection = $this->builderQuery;
        


        $this->filter();

        $data       = $this->getData();
        $parameters = $data['params'];
        $page       = 1;
        if (isset($parameters[self::PAGE]) && $parameters[self::PAGE]) {
            $page = $parameters[self::PAGE];
        }

        if (!isset($parameters['backUrl'])) {
            $limit = self::DEFAULT_LIMIT;
            if (isset($parameters[self::LIMIT]) && $parameters[self::LIMIT]) {
                $limit = $parameters[self::LIMIT];
            }
        } else {
            $limit = $this->searchHelper->getSearchResult();
        }

        $offset = $limit * ($page - 1);
        if (isset($parameters[self::OFFSET]) && $parameters[self::OFFSET]) {
            $offset = $parameters[self::OFFSET];
        }
        

        $all_ids = [];
        $info    = [];
        $total   = $collection->getSize();

        if ($offset > $total) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Invalid method.'), 4);
        }

        $fields = [];
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }

        $check_limit  = 0;
        $check_offset = 0;

        foreach ($collection as $entity) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit) {
                break;
            }
            $info_detail = $entity->toArray($fields);

            $images       = [];
            if (!$entity->getData('media_gallery'))
                $entity = $this->simiObjectManager
                    ->create('Magento\Catalog\Model\Product')->load($entity->getId());
            $media_gallery = $entity->getMediaGallery();
            foreach ($media_gallery['images'] as $image) {
                if ($image['disabled'] == 0) {
                    $images[] = [
                        'url'      => $this->searchHelper
                            ->getImageProduct($entity, $image['file'], $parameters['image_width'], $parameters['image_height']),
                        'position' => $image['position'],
                    ];
                    break;
                }
            }
            if ($this->simiObjectManager->get('Simi\Simiconnector\Helper\Data')->countArray($images) == 0) {
                $images[]     = [
                    'url'      => $this->searchHelper
                        ->getImageProduct($entity, null, $parameters['image_width'], $parameters['image_height']),
                    'position' => 1,
                ];
            }


            $info_detail['images']        = $images;
            $info_detail['app_prices']    = $this->simiObjectManager->get('\Simi\Simiconnector\Helper\Price')
                ->formatPriceFromProduct($entity);

            

            // add to cart url
            $block      = $this->simiObjectManager->create('Magento\Catalog\Block\Product\ListProduct');
            
            $info_detail['add_to_cart_url'] = $block->getAddToCartUrl($entity);
            if(isset($parameters['backUrl'])) {
                $info_detail['add_to_cart_url'].= "?return_url=".$parameters['backUrl'];
            }

            //short description
            if(strlen($info_detail['description']) > 80)
            {
                $info_detail['short_description'] = substr($info_detail['description'], 0,80);
                $info_detail['short_description'] .= "...";
            }

            
            $hasName = 0;
            $hasSku = 0;
            $hasImage = 0;
            $hasDes = 0;
            $hasShortDes = 0;
            $hasPrice = 0;
            $hasAddToCart = 0;
            $hasReview = 0;
            if(isset($parameters['backUrl'])) {
                $storeManagerInterface = $this->simiObjectManager->create('\Magento\Store\Model\StoreManagerInterface');
                $reviewFactory = $this->simiObjectManager->create('Magento\Review\Model\Review');
                $product = $this->simiObjectManager->create('Magento\Catalog\Model\Product')->load($entity->getId());
                $storeId = $storeManagerInterface->getStore()->getStoreId();
                $reviewFactory->getEntitySummary($product, $storeId);
                $info_detail['reviewCount'] = $product->getRatingSummary()->getReviewsCount();
                $info_detail['ratingSummary'] = $product->getRatingSummary()->getRatingSummary();

                $info_detail['productUrl'] = $product->getProductUrl();

                $searchFields = $this->searchHelper->getSearchFieldAsArray();

                for($i = 0; $i < count($searchFields); $i++)
                {
                    switch ($searchFields[$i]) {
                      case 'name':
                        $hasName = 1;
                        break;
                      case 'sku':
                        $hasSku = 1;
                        break;
                      case 'image':
                        $hasImage = 1;
                        break;
                      case 'reviews_rating':
                        $hasReview = 1;
                        break;
                      case 'description':
                        $hasDes = 1;
                        break;
                      case 'short_description':
                        $hasShortDes = 1;
                        break;
                      case 'price':
                        $hasPrice = 1;
                        break;
                      case 'add_to_cart':
                        $hasAddToCart = 1;
                        break;
                      default:
                        # code...
                        break;
                    }
                }
                if($hasName == 0) unset($info_detail['name']);
                if($hasSku == 0) unset($info_detail['sku']);
                if($hasImage == 0) unset($info_detail['images']);
                if($hasReview == 0) {
                    unset($info_detail['reviewCount']);
                    unset($info_detail['ratingSummary']);
                }
                if($hasDes == 0) unset($info_detail['description']);
                if($hasShortDes == 0) unset($info_detail['short_description']);
                if($hasPrice == 0) unset($info_detail['app_prices']['price']);
                if($hasAddToCart == 0) unset($info_detail['add_to_cart_url']);
            }
            
            $info[]                       = $info_detail;
            $all_ids[] = $entity->getId();
        }
        $suggestion = $this->getSuggestProductSearch();
        return $this->getListResultSearch($suggestion, $info, $all_ids, $total, $limit, $offset);
    }

    public function show()
    {
        $entity     = $this->builderQuery;
        $data       = $this->getData();
        $parameters = $data['params'];
        $fields     = [];
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }
        $info          = $entity->toArray($fields);
        $media_gallery = $entity->getMediaGallery();
        $images        = [];

        foreach ($media_gallery['images'] as $image) {
            if ($image['disabled'] == 0) {
                $images[] = [
                    'url'      => $this->searchHelper
                        ->getImageProduct($entity, $image['file'], $parameters['image_width'], $parameters['image_height']),
                    'position' => $image['position'],
                ];
            }
        }
        if ($this->simiObjectManager->get('Simi\Simiconnector\Helper\Data')->countArray($images) == 0) {
            $images[] = [
                'url'      => $this->helperProduct
                    ->getImageProduct($entity, null, $parameters['image_width'], $parameters['image_height']),
                'position' => 1,
            ];
        }

        $registry = $this->simiObjectManager->get('\Magento\Framework\Registry');
        if (!$registry->registry('product') && $entity->getId()) {
            $registry->register('product', $entity);
        }

        $info['images']           = $images;

        $info['app_prices']       = $this->simiObjectManager
            ->get('\Simi\Simiconnector\Helper\Price')->formatPriceFromProduct($entity, true);

        $this->detail_info        = $this->getDetail($info);
        $this->eventManager->dispatch(
            'simi_simiconnector_model_api_products_show_after',
            ['object' => $this, 'data' => $this->detail_info]
        );
        return $this->detail_info;
    }

    public function setFilterByQuery()
    {
        $data       = $this->getData();
        $parameters = $data['params'];
        //check search type config
        $searchType = $this->searchHelper->getSearchingType(); 

        if(isset($parameters['backUrl'])) {
               
            if($searchType == 'magento') {
                $data               = $this->getData();
                $this->searchHelper->setLayers(1);
                $this->layer       = $this->searchHelper
                    ->getLayerNavigator($this->searchHelper->getBuilderQuery());
                $this->builderQuery = $this->searchHelper->getBuilderQuery();
                if(isset($parameters['dir']) && !isset($parameters['order'])) {
                    $this->builderQuery = $this->builderQuery->addAttributeToSort('entity_id',$parameters['dir']);
                }
                $this->sortOrders  = $this->searchHelper->getStoreQrders();
            } else {

                $searchterm = $this->simiObjectManager->get('\Simi\Instantsearch\Model\ResourceModel\Searchterm');

                $this->builderQuery = $searchterm->getProductCollection($parameters['q']);
                // print_r($this->builderQuery->getData());die();
                $this->layer = [];
                $this->sortOrders  = $this->searchHelper->getStoreQrders();
            }
        } else {
            $data               = $this->getData();
            $this->searchHelper->setLayers(1);
            $this->layer       = $this->searchHelper
                ->getLayerNavigator($this->searchHelper->getBuilderQuery());
            $this->builderQuery = $this->searchHelper->getBuilderQuery();
            $this->sortOrders  = $this->searchHelper->getStoreQrders();
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
    public function getListResultSearch($suggestion, $info, $all_ids, $total, $page_size, $from)
    {
        $data                 = $this->getData();
        $parameters           = $data['params'];
        // gen form_key to avoid shopping cart empty
        $genKey = $this->simiObjectManager->get('\Magento\Framework\Data\Form\FormKey');
        $formKey = $genKey->getFormKey();

        $simiinstantsearchs = [];
        if(isset($parameters['hasSuggestion']) && $parameters['hasSuggestion'] == 1) {
            $simiinstantsearchs['suggestion'] = $suggestion;
        } elseif(isset($parameters['hasSuggestion']) && $parameters['hasSuggestion'] == 0) {

        } else {
            $simiinstantsearchs['suggestion'] = $suggestion;
        }

        if(isset($parameters['hasProduct']) && $parameters['hasProduct'] == 1) {
            $simiinstantsearchs['instant_search'] = [
                'all_ids'             => $all_ids,
                'products'            => $info,
                'total'               => $total,
                'page_size'           => $page_size,
                'from'                => $from,
                'form_key'            =>$formKey,
            ];
        } elseif(isset($parameters['hasProduct']) && $parameters['hasProduct'] == 0) {

        } else {
            $simiinstantsearchs['instant_search'] = [
                'all_ids'             => $all_ids,
                'products'            => $info,
                'total'               => $total,
                'page_size'           => $page_size,
                'from'                => $from,
                'layers'              => $this->layer,
                'orders'              => $this->sortOrders,
            ];
        }
        
        return $simiinstantsearchs;
    }

    public function getSuggestProductSearch()
    {

        $dataHelper = $this->simiObjectManager->create('Magento\Search\Helper\Data');
        $resultSuggest = $this->searchHelper->getSearchSuggest();
        $autocomplete = $this->simiObjectManager->create('Magento\Search\Model\AutocompleteInterface')->getItems();

        if(count($autocomplete) > 0) {
            $autocomplete = array_slice($autocomplete, 0, $resultSuggest);
            foreach ($autocomplete as $item) {
              $item                   = $item->toArray();
              $item['url']            = $dataHelper->getResultUrl($item['title']);
              $suggestion[] = $item;
            }
        } else {
            $suggestion[] = null;
        }
        return $suggestion;
    }
}
?>
<?php

namespace Simi\Instantsearch\Controller\Search;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var type 
     */
    protected $context;
    /**
     *
     * @var type 
     */
    protected $searchterm;
    /**
     *
     * @var type 
     */
    protected $product;
    /**
     *
     * @var type 
     */
    protected $storeManagerInterface;

    public $simiObjectManager;

    /**
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Simi\Instantsearch\Model\ResourceModel\Searchterm $searchterm
     * @param \Magento\Catalog\Model\ProductFactory $product
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Simi\Instantsearch\Model\ResourceModel\Searchterm $searchterm,
       \Magento\Catalog\Model\ProductFactory $product,
       \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
        ) 
    {
        parent::__construct($context);
        $this->searchterm = $searchterm;
        $this->product    = $product;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->simiObjectManager = $this->_objectManager;
    }
    public function execute()
    {
      
      $reviewFactory = $this->simiObjectManager->create('Magento\Review\Model\Review');
      $storeId = $this->storeManagerInterface->getStore()->getStoreId();
      // $stockState = $this->simiObjectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');

      //helper
      $helperSearch = $this->simiObjectManager->create('Simi\Instantsearch\Helper\Search');

      //check config
      $limit = $helperSearch->getSearchResult();
      $popUp = $helperSearch->getSearchPopupAsArray();
      $searchFields = $helperSearch->getSearchFieldAsArray();
      $isActive = $helperSearch->isActive();
      $searchingType = $helperSearch->getSearchingType();

          if ($this->_request->getParam('q') == '')
          {
              return '';
          }
          
          $param = $this->_request->getParams();
          $q = $param['q'];
          
          $prodReturnData = array();
          $i = 0;
          
          

          $currentStore = $this->storeManagerInterface->getStore();
          $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
          $webUrl   = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

          //suggest Product
          $searchHelper = $this->simiObjectManager->create('Magento\Search\Helper\Data');
          $resultSuggest = $helperSearch->getSearchResult();

          foreach ($popUp as $choice) {
            if($choice == 'suggest') 
            {             
              $autocomplete = $this->simiObjectManager->create('Magento\Search\Model\AutocompleteInterface')->getItems();

              if(count($autocomplete) > 0) {
                $autocomplete = array_slice($autocomplete, 0, $resultSuggest);
                foreach ($autocomplete as $item) {
                  $item                   = $item->toArray();
                  $item['url']            = $searchHelper->getResultUrl($item['title']);
                  $suggestProduct[] = $item;
                }
              } else {
                $suggestProduct = null;
              }
              break;
            }
          }

          //result product
          foreach ($popUp as $choice) {
            if($choice == 'product')
            {
              // $allProducts= $this->searchterm->getResultProduct($q, $limit);
              // get type of searching
              if($searchingType == 'magento') {
                //get product by default magento searching logic
                $allProducts = $this->searchterm->getDefaultResultProduct($q, $limit);
              } else {
                // get product by name and description
                $allProducts = $this->searchterm->getProductCollection($q, $limit);
              }

              //get addToCartUrl
              $cartHelper = $this->simiObjectManager->create('Magento\Checkout\Helper\Cart');
              $postData   = $this->simiObjectManager->create('Magento\Framework\Data\Helper\PostHelper');
              $block      = $this->simiObjectManager->create('Magento\Catalog\Block\Product\ListProduct');
              
              if($allProducts != null) {
                foreach($allProducts as $eachProd)
                {

                  $productData = $this->product->create()->load($eachProd['entity_id']);

                   
                  $productId = $eachProd['entity_id'];
                  //get image url
                  if ($productData->getImage())
                  {
                      $fullImageUrl = $mediaUrl.'catalog/product'.$productData->getImage();
                  }
                  else
                  {
                      $fullImageUrl = $webUrl."pub/static/frontend/Magento/luma/en_US/Simi_Instantsearch/images/h.jpg";
                  }
                  
                  //get description
                  $description = $productData->getDescription();
                  if(strlen($description) > 50)
                  {
                    $shortDescription = substr($description, 0,80);
                    $shortDescription .= "...";
                  }

                  //get rating star and total review
                  $product = $this->simiObjectManager->create('Magento\Catalog\Model\Product')->load($eachProd['entity_id']);
                  $reviewFactory->getEntitySummary($product, $storeId);
                  $ratingSummary = $product->getRatingSummary()->getRatingSummary();
                  $reviewCount = $product->getRatingSummary()->getReviewsCount();

                  if($reviewCount == null) $reviewCount = 0;
                  if($ratingSummary == null) $ratingSummary = 0;


                  for($i = 0; $i < count($searchFields); $i++)
                  {
                    switch ($searchFields[$i]) {
                      case 'name':
                        $detailProduct['name'] = $productData->getName();
                        break;
                      case 'sku':
                        $detailProduct['sku'] = $productData->getSku();
                        break;
                      case 'image':
                        $detailProduct['imageUrl'] = $fullImageUrl;
                        break;
                      case 'reviews_rating':
                        $detailProduct['ratingSummary'] = $ratingSummary;
                        $detailProduct['reviewCount'] = $reviewCount;
                        break;
                      case 'description':
                        $detailProduct['description'] = $description;
                        break;
                      case 'short_description':
                        $detailProduct['short_description'] = $shortDescription;
                        break;
                      case 'price':
                        // if($productData->getPrice() != 0) {
                        //   $detailProduct['price'] = $productData->getPrice();
                        // } else $detailProduct['price'] = $productData->getFinalPrice();
                        $detailProduct['price'] = $productData->getFinalPrice();
                        break;
                      case 'add_to_cart':
                        $detailProduct['add_to_cart_url'] = $block->getAddToCartUrl($productData);
                        $detailProduct['add_to_cart_url'].= "?return_url=".$param['backUrl'];
                        break;
                      default:
                        # code...
                        break;
                    }
                  } 

                  $detailProduct['productUrl'] = $productData->getProductUrl();       
                  // $detailProduct['status'] = $stockState->getStockQty($productData->getId(), $productData->getStore()->getWebsiteId());
                  $resultProduct[] = $detailProduct;
                }
              } else {
                $resultProduct = null;
              }
              
              break;
            }
          }
          
          $data = [];
                    
          if(isset($suggestProduct)) {
            $data['suggestProduct'] = $suggestProduct;
          }

          if(isset($resultProduct)) {
            $data['resultProduct'] = $resultProduct;
          }

          // $moduleManager = $this->simiObjectManager->get('\Magento\Framework\Module\Manager');
          // $data['check'] = $moduleManager->isEnabled('Inchoo_Tutorial');

          try {
            $this->getResponse()->setHeader('Content-Type', 'application/json');
            return $this->getResponse()->setBody(json_encode($data));
          } catch (\Exception $e) {
            return;
          }
    }
}

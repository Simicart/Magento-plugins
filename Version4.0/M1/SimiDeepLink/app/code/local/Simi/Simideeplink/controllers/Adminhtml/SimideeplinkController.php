<?php

class Simi_Simideeplink_Adminhtml_SimideeplinkController extends Mage_Adminhtml_Controller_Action
{

    protected $_countLinkSuccess = 0;

    /**
     * init layout and set active for current menu
     *
     * @return Simi_Simideeplink_Adminhtml_SimideeplinkController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('simideeplink/simideeplink')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $simideeplinkId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('simideeplink/simideeplink')->load($simideeplinkId);

        if ($model->getId() || $simideeplinkId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('simideeplink_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('simideeplink/simideeplink');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item News'),
                Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('simideeplink/adminhtml_simideeplink_edit'))
                ->_addLeft($this->getLayout()->createBlock('simideeplink/adminhtml_simideeplink_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('simideeplink')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $this->_countLinkSuccess = 0;

            if ($data['type'] == 1) {

                $this->generateLinkForProduct($data);
            }
            else if($data['type'] == 2){
                $this->generateLinkForCategory($data);
            }
//            else if($data['type'] == 3){
//
//            }

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('simideeplink')->__('%s link(s) was successfully generated.', $this->_countLinkSuccess)
            );

            $this->_redirect('*/*/');
            return;

        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('simideeplink')->__('Can\'t generate link. ')
        );
        $this->_redirect('*/*/');

    }

    protected function validateConfigData()
    {

    }

    protected function generateLinkForProduct($data)
    {
        $list_ids = explode(',', $data['product_id']);

        foreach ($list_ids as $product_id) {

            $product_url = $this->getProductUrl($product_id);

            $product_url .= '?simi_product_id=' . $product_id;
            $long_link = $this->generateLongDynamicLink($product_url, $data);

            $short_url = $this->callFirebase($long_link);
            if ($short_url) {
                $data['link'] = $short_url;

                $this->saveDeepLink($data);
            }
        }

    }

    protected function getProductUrl($product_id)
    {

        $product_model = new Mage_Catalog_Model_Product();
        $product_model->load($product_id);
        $product_url = $product_model->getProductUrl();

        return $product_url;
    }

    protected function generateLinkForCategory($data){

        $cat_id = $data['category_id'];
        $category = Mage::getModel ('catalog/category')->load($cat_id);

        $has_child = '0';
        if ($category->getChildrenCount() > 0) {
            $has_child = '1';
        }
        $cate_name = $category->getName();
        $cat_url =  $category->getUrl().'?simi_cate_name='.$cate_name.'&simi_has_child='.$has_child.'$simi_cate_id='.$cat_id;

        $long_link = $this->generateLongDynamicLink($cat_url,$data);
        $short_url = $this->callFirebase($long_link);
        if ($short_url) {
            $data['link'] = $short_url;
            $this->saveDeepLink($data);
        }

    }

    protected function generateLinkForCMS($data){
        $cms_id = $data['cms_id'];
        $cmsModel = Mage::getModel('cms/page')->load($cms_id);
        $cat_url = $cmsModel->getUrl();
        echo $cat_url; die('CAT URL ');

    }

    protected function generateLongDynamicLink($url, $data)
    {
        $base_dynamic_link = Mage::getStoreConfig('simideeplink/general/firebase_base_dynamic_link');


        $long_link = $base_dynamic_link . '?link=' . $url;

        $android_package_name = Mage::getStoreConfig('simideeplink/app_parameters/android_package_name');
        if ($android_package_name) {
            $long_link .= '&apn=' . $android_package_name;
        }

        $ios_bundle_id = Mage::getStoreConfig('simideeplink/app_parameters/ios_bundle_id');
        if ($ios_bundle_id) {
            $long_link .= '&ibi=' . $ios_bundle_id;
        }

        $app_store_id = Mage::getStoreConfig('simideeplink/app_parameters/ios_store_id');
        if ($app_store_id) {
            $long_link .= '&isi=' . $app_store_id;
        }

        if (isset($data['social_title']) && $data['social_title']) {
            $long_link .= '&st=' . $data['social_title'];
        }

        if (isset($data['social_description']) && $data['social_description']) {
            $long_link .= '&sd=' . $data['social_description'];
        }

        if (isset($data['social_image']) && $data['social_image']) {
            $long_link .= '$si=' . $data['social_image'];
        }

        if (isset($data['utm_source']) && $data['utm_source']) {
            $long_link .= '&utm_source=' . $data['utm_source'];
        }

        if (isset($data['utm_medium']) && $data['utm_medium']) {
            $long_link .= '&utm_medium=' . $data['utm_medium'];
        }

        if (isset($data['utm_campaign']) && $data['utm_campaign']) {
            $long_link .= '&utm_campaign=' . $data['utm_campaign'];
        }

        if (isset($data['utm_term']) && $data['utm_term']) {
            $long_link .= '&utm_term=' . $data['utm_term'];
        }

        if (isset($data['utm_content']) && $data['utm_content']) {
            $long_link .= '&utm_content=' . $data['utm_content'];
        }

        if (isset($data['gclid']) && $data['gclid']) {
            $long_link .= '&gclid=' . $data['gclid'];
        }

        if (isset($data['at']) && $data['at']) {
            $long_link .= '&at=' . $data['at'];
        }

        if (isset($data['ct']) && $data['ct']) {
            $long_link .= '&ct=' . $data['ct'];
        }

        if (isset($data['mt']) && $data['mt']) {
            $long_link .= '&mt=' . $data['mt'];
        }

        if (isset($data['pt']) && $data['pt']) {
            $long_link .= '&pt=' . $data['pt'];
        }

        return $long_link;
    }

    protected function callFirebase($long_url)
    {
        $firebase_web_key = Mage::getStoreConfig('simideeplink/general/firebase_web_api_key');

        $url = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . $firebase_web_key;
        $headers = array(
            'Content-Type: application/json');
        $fields = array(
            'longDynamicLink' => $long_url,
        );

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarly
            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            throw new Exception("Fail:" . $e->getMessage(), 1);
        }

        $result = json_decode($result, true);
        return $result['shortLink'];

    }


    protected function saveDeepLink($data)
    {
        $model = Mage::getModel('simideeplink/simideeplink');
        $model->setData($data)
            ->setId($this->getRequest()->getParam('id'));

        try {
            if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                $model->setCreatedTime(now())
                    ->setUpdateTime(now());
            } else {
                $model->setUpdateTime(now());
            }
            $model->save();
            $this->_countLinkSuccess++;
        } catch (Exception $e) {
Zend_Debug::dump($e->getMessage());die('saveDeepLink');
        }
    }

    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('simideeplink/simideeplink');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $simideeplinkIds = $this->getRequest()->getParam('simideeplink');
        if (!is_array($simideeplinkIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($simideeplinkIds as $simideeplinkId) {
                    $simideeplink = Mage::getModel('simideeplink/simideeplink')->load($simideeplinkId);
                    $simideeplink->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                        count($simideeplinkIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction()
    {
        $simideeplinkIds = $this->getRequest()->getParam('simideeplink');
        if (!is_array($simideeplinkIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($simideeplinkIds as $simideeplinkId) {
                    Mage::getSingleton('simideeplink/simideeplink')
                        ->load($simideeplinkId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($simideeplinkIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'simideeplink.csv';
        $content = $this->getLayout()
            ->createBlock('simideeplink/adminhtml_simideeplink_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'simideeplink.xml';
        $content = $this->getLayout()
            ->createBlock('simideeplink/adminhtml_simideeplink_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simideeplink');
    }


    public function chooserMainProductsAction()
    {
        $request = $this->getRequest();
        $block = $this->getLayout()->createBlock(
            'simideeplink/adminhtml_simideeplink_edit_tab_products', 'promo_widget_chooser_sku', array('js_form_object' => $request->getParam('form'),
        ));
        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }

    public function chooserMainCategoriesAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('selected', array());
        $block = $this->getLayout()->createBlock('simiconnector/adminhtml_siminotification_edit_tab_categories', 'maincontent_category', array('js_form_object' => $request->getParam('form')))
            ->setCategoryIds($id);

        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }


}
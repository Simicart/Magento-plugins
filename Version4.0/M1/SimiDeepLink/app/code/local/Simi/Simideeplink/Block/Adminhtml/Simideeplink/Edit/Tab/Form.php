<?php

class Simi_Simideeplink_Block_Adminhtml_Simideeplink_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Simi_Simideeplink_Block_Adminhtml_Simideeplink_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getSimideeplinkData()) {
            $data = Mage::getSingleton('adminhtml/session')->getSimideeplinkData();
            Mage::getSingleton('adminhtml/session')->setSimideeplinkData(null);
        } elseif (Mage::registry('simideeplink_data')) {
            $data = Mage::registry('simideeplink_data')->getData();
        }


//        if($data->getId()){
//            if($data[])
//        }

        //simideeplink_id
        $fieldset = $form->addFieldset('simideeplink_form', array(
            'legend' => Mage::helper('simideeplink')->__('Item information')
        ));

        $fieldset->addField('type', 'select', array(
            'label' => Mage::helper('simideeplink')->__('Type'),
            'name' => 'type',
            'values' => Mage::getSingleton('simideeplink/typelink')->getOptionHash(),
            'onchange' => 'onChangeType(this.value)',
            'after_element_html' => '<script> Event.observe(window, "load", function(){onChangeType(\'' . $data['type'] . '\');});</script>',
        ));

        if ($data['link']) {
            $fieldset->addField('link', 'text', array(
                'label' => Mage::helper('simideeplink')->__('DynamicLink'),
                'name' => 'Dynamic Link',
                'readonly' => true,

            ));
        }


        $data_title = array(
            'label' => Mage::helper('simideeplink')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        );
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $data_title['readonly'] = true;
        }
        $fieldset->addField('title', 'text', $data_title);

        $productIds = implode(", ", Mage::getResourceModel('catalog/product_collection')->getAllIds());


        $productdata = array(
            'name' => 'product_id',
            'class' => 'required-entry',
            'required' => true,
            'label' => Mage::helper('simiconnector')->__('Product ID'),
            'note' => Mage::helper('simiconnector')->__('Choose a product'),
            'after_element_html' =>
                '<a id="product_link" href="javascript:void(0)" onclick="toggleMainProducts()"><img src="' . $this->getSkinUrl('images/rule_chooser_trigger.gif') . '" alt="" class="v-middle rule-chooser-trigger" title="Select Products"></a>
                    <input type="hidden" value="' . $productIds . '" id="product_all_ids"/>
                    <div id="main_products_select" style="display:none;width:640px"></div>
                <script type="text/javascript">
                
                    function toggleMainProducts(){
                        if($("main_products_select").style.display == "none"){
                            var url = "' . $this->getUrl('simideeplinkadmin/adminhtml_simideeplink/chooserMainProducts') . '";
                            console.log(url);
                            var params = $("product_id").value.split(", ");
                            var parameters = {"form_key": FORM_KEY,"selected[]":params };
                            var request = new Ajax.Request(url,
                            {
                                evalScripts: true,
                                parameters: parameters,
                                onComplete:function(transport){
                                    $("main_products_select").update(transport.responseText);
                                    $("main_products_select").style.display = "block"; 
                                }
                            });
                        }else{
                            $("main_products_select").style.display = "none";
                        }
                    };
                    var grid;
                   
                    function constructData(div){
                        grid = window[div.id+"JsObject"];
                        if(!grid.reloadParams){
                            grid.reloadParams = {};
                            grid.reloadParams["selected[]"] = $("product_id").value.split(", ");
                        }
                    }
                    
                    function toogleCheckAllProduct(el){
                        
                         if(el == true){
                            $$("#main_products_select input[type=checkbox][class=checkbox]").each(function(e){
                                
                                if(e.name != "check_all"){
                                    if(!e.checked){
                                        if($("product_id").value == "")
                                            $("product_id").value = e.value;
                                        else
                                            $("product_id").value = $("product_id").value + ", "+e.value;
                                        e.checked = true;
                                        grid.reloadParams["selected[]"] = $("product_id").value.split(", ");
                                    }
                                }
                            });
                        }else{
                            $$("#main_products_select input[type=checkbox][class=checkbox]").each(function(e){
                                if(e.name != "check_all"){
                                    if(e.checked){
                                        var vl = e.value;
                                        if($("product_id").value.search(vl) == 0){
                                            if($("product_id").value == vl) $("product_id").value = "";
                                            $("product_id").value = $("product_id").value.replace(vl+", ","");
                                        }else{
                                            $("product_id").value = $("product_id").value.replace(", "+ vl,"");
                                        }
                                        e.checked = false;
                                        grid.reloadParams["selected[]"] = $("product_id").value.split(", ");
                                    }
                                }
                            });
                            
                        }
                    }
                  
                    function selectProduct(e) {
                        if(e.checked == true){
                            
                            if($("product_id").value == "")
                                            $("product_id").value = e.value;
                                        else
                                            $("product_id").value = $("product_id").value + ", "+e.value;
                                        grid.reloadParams["selected[]"] = $("product_id").value.split(", ");
                            
                        }else{
                            
                            var vl = e.value;
                                        if($("product_id").value.search(vl) == 0){
                                            if($("product_id").value == vl) $("product_id").value = "";
                                            $("product_id").value = $("product_id").value.replace(vl+", ","");
                                        }else{
                                            $("product_id").value = $("product_id").value.replace(", "+ vl,"");
                                        }
                                        e.checked = false;
                                        grid.reloadParams["selected[]"] = $("product_id").value.split(", ");
                        
                        }
                        
                    }
                </script>'
        );
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $productdata['readonly'] = true;
        }
        $fieldset->addField('product_id', 'text', $productdata);

        $categorydata = array(
            'name' => 'category_id',
            'class' => 'required-entry',
            'required' => false,
            'label' => Mage::helper('simiconnector')->__('Category ID'),
            'note' => Mage::helper('simiconnector')->__('Choose a category'),
            'after_element_html' => '<a id="category_link" href="javascript:void(0)" onclick="toggleMainCategories()"><img src="' . $this->getSkinUrl('images/rule_chooser_trigger.gif') . '" alt="" class="v-middle rule-chooser-trigger" title="Select Category"></a>
                <div id="main_categories_select" style="display:none"></div>
                    <script type="text/javascript">
                    function toggleMainCategories(check){
                        var cate = $("main_categories_select");
                        if($("main_categories_select").style.display == "none" || (check ==1) || (check == 2)){
                            var url = "' . $this->getUrl('simideeplinkadmin/adminhtml_simideeplink/chooserMainCategories') . '";                        
                            if(check == 1){
                                $("category_id").value = $("category_all_ids").value;
                            }else if(check == 2){
                                $("category_id").value = "";
                            }
                            var params = $("category_id").value.split(", ");
                            var parameters = {"form_key": FORM_KEY,"selected[]":params };
                            var request = new Ajax.Request(url,
                                {
                                    evalScripts: true,
                                    parameters: parameters,
                                    onComplete:function(transport){
                                        $("main_categories_select").update(transport.responseText);
                                        $("main_categories_select").style.display = "block"; 
                                    }
                                });
                        if(cate.style.display == "none"){
                            cate.style.display = "";
                        }else{
                            cate.style.display = "none";
                        } 
                    }else{
                        cate.style.display = "none";                    
                    }
                };
        </script>
            '
        );
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $categorydata['readonly'] = true;
        }
        $fieldset->addField('category_id', 'text', $categorydata);


//        $list_cms = $this->getListCMS();
//        $fieldset->addField('cms_id', 'select', array(
//            'label' => Mage::helper('simiconnector')->__('CMS'),
//            'class' => 'required-entry',
//            'required' => true,
//            'name' => 'cms_id',
//            'values' => $list_cms,
//        ));


        $socialMetaTagFieldset = $form->addFieldset('simideeplink_social_tag_form', array(
            'legend' => Mage::helper('simideeplink')->__('Social Meta Tag parameters')
        ));
        $socialTitleData = array(
            'label' => Mage::helper('simiconnector')->__('Social Title'),
            'note' => Mage::helper('simiconnector')->__('The title to use when the Dynamic Link is shared in a social post.'),
            'name' => 'social_title',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $socialTitleData['readonly'] = true;
        }
        $socialMetaTagFieldset->addField(
            'social_title', 'text', $socialTitleData);


        $socialDescriptionData = array(
            'label' => Mage::helper('simiconnector')->__('Social Description'),
            'note' => Mage::helper('simiconnector')->__('The description to use when the Dynamic Link is shared in a social post.'),
            'name' => 'social_description',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $socialDescriptionData['readonly'] = true;
        }
        $socialMetaTagFieldset->addField('social_description', 'text', $socialDescriptionData);

        $socialImageData = array(
            'label' => Mage::helper('simiconnector')->__('Social Description'),
            'note' => Mage::helper('simiconnector')->__('The URL to an image related to this link. The image should be at least 300x200 px, and less than 300 KB.'),
            'name' => 'social_image',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $socialImageData['readonly'] = true;
        }
        $socialMetaTagFieldset->addField('social_image', 'text', $socialImageData);

        $analyticsGooglePlayParameterFieldset = $form->addFieldset('simideeplink_analytics_googleplay_param_form', array(
            'legend' => Mage::helper('simideeplink')->__('Google Play analytics parameters')
        ));

        $utm_source = array(
            'label' => Mage::helper('simiconnector')->__('utm_source'),
            'name' => 'utm_source',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $utm_source['readonly'] = true;
        }
        $analyticsGooglePlayParameterFieldset->addField(
            'utm_source', 'text', $utm_source);

        $utm_medium = array(
            'label' => Mage::helper('simiconnector')->__('utm_medium'),
            'name' => 'utm_medium',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $utm_medium['readonly'] = true;
        }
        $analyticsGooglePlayParameterFieldset->addField(
            'utm_medium', 'text', $utm_medium);

        $utm_campaign = array(
            'label' => Mage::helper('simiconnector')->__('utm_campaign'),
            'name' => 'utm_campaign',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $utm_campaign['readonly'] = true;
        }

        $analyticsGooglePlayParameterFieldset->addField(
            'utm_campaign', 'text', $utm_campaign);

        $utm_term = array(
            'label' => Mage::helper('simiconnector')->__('utm_term'),
            'name' => 'utm_term',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $utm_term['readonly'] = true;
        }
        $analyticsGooglePlayParameterFieldset->addField(
            'utm_term', 'text', $utm_term);

        $utm_content = array(
            'label' => Mage::helper('simiconnector')->__('utm_content'),
            'name' => 'utm_content',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $utm_content['readonly'] = true;
        }
        $analyticsGooglePlayParameterFieldset->addField(
            'utm_content', 'text',$utm_content );

     $gclid =   array(
            'label' => Mage::helper('simiconnector')->__('gclid'),
            'name' => 'gclid',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $gclid['readonly'] = true;
        }
        $analyticsGooglePlayParameterFieldset->addField(
            'gclid', 'text',$gclid );

        $analyticsItunesParameterFieldset = $form->addFieldset('simideeplink_analytics_itunes_param_form', array(
            'legend' => Mage::helper('simideeplink')->__('iTunes Connect analytics parameters')
        ));

        $at =  array(
            'label' => Mage::helper('simiconnector')->__('at'),
            'name' => 'at',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $at['readonly'] = true;
        }
        $analyticsItunesParameterFieldset->addField(
            'at', 'text',$at);

        $ct = array(
            'label' => Mage::helper('simiconnector')->__('ct'),
            'name' => 'ct',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $ct['readonly'] = true;
        }
        $analyticsItunesParameterFieldset->addField(
            'ct', 'text',$ct );

        $mt = array(
            'label' => Mage::helper('simiconnector')->__('mt'),
            'name' => 'mt',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $mt['readonly'] = true;
        }
        $analyticsItunesParameterFieldset->addField(
            'mt', 'text', $mt);

        $pt = array(
            'label' => Mage::helper('simiconnector')->__('pt'),
            'name' => 'pt',);
        if (isset($data['simideeplink_id']) && $data['simideeplink_id']) {
            $pt['readonly'] = true;
        }

        $analyticsItunesParameterFieldset->addField(
            'pt', 'text',$pt );

        $form->setValues($data);
        return parent::_prepareForm();
    }

    protected function getListCMS()
    {
        $collection = Mage::getModel('simiconnector/cms')->getCollection()->addFieldToFilter('cms_status', '1');
        $array_cms = array();
        foreach ($collection as $cms) {
            $array_cms[$cms->getData('cms_id')] = $cms->getData('cms_title');
        }
        return $array_cms;
    }

}
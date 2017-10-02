<?php
/**
 * Simi
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Simicart.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Simicart.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @module     Giftvoucher
 * @author      Simi Developer
 *
 * @copyright   Copyright (c) 2016 Simi (http://www.Simicart.com/)
 * @license     http://www.Simicart.com/license-agreement.html
 *
 */

/**
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Logo
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Logo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * @param Varien_Object $row
     * @return mixed|null|string
     * @throws Exception
     */
    public function render(Varien_Object $row) {
        $actionName = $this->getRequest()->getActionName();
        $image = $row->getData($this->getColumn()->getIndex());

        if (strpos($actionName, 'export') === 0) {
            return $image;
        }
        if ($image) {
            return '<img src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'simigiftvoucher/template/logo/' . $image . ' " width="40 px" height="40px" />';
        } else {
            return null;
        }
    }

}
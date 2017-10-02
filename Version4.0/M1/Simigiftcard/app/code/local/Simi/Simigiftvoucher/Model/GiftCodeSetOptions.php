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
 * Giftvoucher View Block
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */

class Simi_Simigiftvoucher_Model_GiftCodeSetOptions extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableGiftcodeSets()
    {
        $giftcodeSets = Mage::getModel('simigiftvoucher/giftcodeset')->getCollection();

//            ->addFieldToFilter('status', '2');
        $listGiftcodeSets = array();
        foreach ($giftcodeSets as $giftcodeSet) {
            $listGiftcodeSets[] = array('label' => $giftcodeSet->getSetName(),
                'value' => $giftcodeSet->getSetId());
        }
        return  $listGiftcodeSets;
    }

    /**
     * Get model option as array
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (is_null($this->_options)) {
            $this->_options = $this->getAvailableGiftcodeSets();
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => Mage::helper('simigiftvoucher')->__('-- Please Select --'),
            ));
        }
        return $options;
    }

}

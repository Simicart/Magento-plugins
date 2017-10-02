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
 * Giftvoucher history resource collection
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Model_Mysql4_History_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('simigiftvoucher/history');
    }

    /**
     * @return $this
     */
    public function joinGiftVoucher()
    {
        if ($this->hasFlag('join_giftvoucher') && $this->getFlag('join_giftvoucher')) {
            return $this;
        }
        $this->setFlag('join_giftvoucher', true);
        $this->getSelect()->joinLeft(
            array('simigiftvoucher' => $this->getTable('simigiftvoucher/giftvoucher')),
            'main_table.giftvoucher_id = simigiftvoucher.giftvoucher_id', array('gift_code')
        )->where('main_table.action = ?', Simi_Simigiftvoucher_Model_Actions::ACTIONS_SPEND_ORDER);
        return $this;
    }

}

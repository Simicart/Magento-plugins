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
 * Giftvoucher Session Model
 * 
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @author      Simi Developer
 */
class Simi_Simigiftvoucher_Model_Session extends Mage_Core_Model_Session_Abstract
{

    /**
     * Init Gift Card session
     */
    public function __construct()
    {
        $namespace = 'simigiftvoucher';
        $this->init($namespace);
    }

}

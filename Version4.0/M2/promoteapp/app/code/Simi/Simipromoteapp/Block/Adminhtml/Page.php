<?php

/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Simi\Simipromoteapp\Block\Adminhtml;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Dashboard Month-To-Date Day starts Field Renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Page extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @param AbstractElement $element
     * @return string
     */
    public $simiObjectManager;
    public $urlHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Framework\Url $urlHelper
    ) {
    
        $this->simiObjectManager = $simiObjectManager;
        $this->urlHelper = $urlHelper;
        parent::__construct($context);
    }

    public function getFrontendUrl($routePath, $routeParams)
    {
        return $this->urlHelper->getUrl($routePath, $routeParams);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $frontend_link = $url = $this->getFrontendUrl(
            'simipromoteapp/mobileapplication',
            ['_nosid' => true]
        );
        return '<a href="' . $frontend_link . '" target="_blank" title="Promote App">Page Preview</a>';
    }
}

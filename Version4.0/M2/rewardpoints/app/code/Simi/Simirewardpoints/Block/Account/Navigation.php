<?php

/**
 * Simirewardpoints Navigation
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Account;

class Navigation extends \Magento\Framework\View\Element\Template
{

    protected $_links = [];
    protected $_activeLink = false;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simirewardpoints\Helper\Policy $globalConfig
    ) {

        parent::__construct($context, []);
    }

    /**
     * Add link to navigation
     *
     * @param string $name
     * @param string $path
     * @param string $label
     * @param boolean $enable
     * @param int $order
     * @return \Simi\Simirewardpoints\Block\Account\Navigation
     */
    public function addLink($name, $path, $label, $enable = true, $order = 0)
    {
        while (isset($this->_links[$order])) {
            $order++;
        }

        $this->_links[$order] = new \Magento\Framework\DataObject([
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'enable' => $enable,
            'order' => $order,
            'url' => $this->getUrl($path)
        ]);

        return $this;
    }

    /**
     * get Sorted links (by order)
     *
     * @return array
     */
    public function getLinks()
    {
        ksort($this->_links);
        return $this->_links;
    }

    /**
     * Set active link on navigation
     *
     * @param string $path
     * @return \Simi\Simirewardpoints\Block\Account\Navigation
     */
    public function setActive($path)
    {
        $this->_activeLink = $this->_completePath($path);
        return $this;
    }

    /**
     * Check activate link
     *
     * @param string link
     * @return boolean
     */
    public function isActive($link)
    {
        if (empty($this->_activeLink)) {
            $this->_activeLink = $this->getRequest()->getActionName('/');
        }
        if ($this->_completePath($link->getPath()) == $this->_activeLink) {
            return true;
        }
        return false;
    }

    /**
     * Repare complete path
     *
     * @param string $path
     * @return string
     */
    protected function _completePath($path)
    {
        $path = rtrim($path, '/');
        switch (sizeof(explode('/', $path))) {
            case 1:
                $path .= '/index';
            case 2:
                $path .= '/index';
        }
        return $path;
    }
}

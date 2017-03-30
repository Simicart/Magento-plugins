<?php

/**
 * @category Simi
 * @package  Simi_Simirewardpoints
 * @author   Simicart Developer
 */

namespace Simi\Simirewardpoints\Controller\Checkout;

class RefreshPoint extends \Simi\Simirewardpoints\Controller\AbstractAction
{

    /**
     * @return mixed
     */
    public function execute()
    {
        return $this->getResponse()->setBody($this->_helperSpend->getRulesJson($this->_helperSpend->getSliderRules()));
    }
}

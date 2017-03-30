<?php

/**
 * SimirewardPoints Name and Image Block
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Welcome;

class Name extends \Simi\Simirewardpoints\Block\Name
{

    public function _toHtml()
    {
        parent::_toHtml();
        return $this->_objectManager->create('Simi\Simirewardpoints\Helper\Point')->getPluralName();
    }
}

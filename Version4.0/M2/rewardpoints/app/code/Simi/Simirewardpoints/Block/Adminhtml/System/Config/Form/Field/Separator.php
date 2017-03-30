<?php

/**
 * Simirewardpoints Config Field Separator Block
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Adminhtml\System\Config\Form\Field;

class Separator extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * render separator config row
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $fieldConfig = $element->getFieldConfig();
        $htmlId = $element->getHtmlId();
        $html = '<tr id="row_' . $htmlId . '">'
                . '<td class="label" colspan="3">';
        $customStyle = '';
        if (isset($fieldConfig['style']) && $fieldConfig['style']) {
            $customStyle = $fieldConfig['style'];
        }

        $html .= '<div style="margin-top:10px; font-weight: bold; border-bottom: 1px solid #dfdfdf;text-align:left;'
                . $customStyle . '">';
        $html .= $element->getLabel();
        $html .= '</div></td></tr>';
        return $html;
    }
}

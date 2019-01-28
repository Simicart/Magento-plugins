<?php
class Simicart_Simihr_Block_Adminhtml_Renderer_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $checked = '';
        $id  = (int) $this->getRequest()->getParam('id');
        $job = Mage::getResourceModel('simihr/department_collection')->addFieldToFilter('id', $id)->getData();
        $ids = explode(",",$job[0]['job_offer_ids']);
//        print_r(array_values($ids));die();
        if (in_array($row->getId(), $ids))
        {
            $checked = 'checked';
        }

        $html = '<input type="checkbox" ' . $checked .
            ' name="selected" id="' . $row->getId() .
            '" value="' . $row->getId() .
            '" class="checkbox" onclick="getSelectedJob(this.id)">';
//        $html.= "<script> function getSelectedJob(id) { var ids = window.document.getElementById('departmentjob_offer_ids');if(document.getElementById(id).checked == true){ids.value +=','+id;} else {var text = ','+id;if ((ids.value).indexOf(text) != -1) {ids.value = ids.value.replace(text,'');} else {ids.value.replace(id,'');}}}</script>";
        return sprintf('%s', $html);

    }
}
?>
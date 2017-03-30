<?php
class Simi_Simistorelocator_Model_Adminhtml_Facebookcomment
{
    public function getCommentText(){
        $comment = 'To register a Facebook API key, please follow the guide <a href="'.Mage::getBlockSingleton('adminhtml/widget')->getUrl('simistorelocatoradmin/adminhtml_guide/index/').'">here</a>';
        return $comment;
    }
}

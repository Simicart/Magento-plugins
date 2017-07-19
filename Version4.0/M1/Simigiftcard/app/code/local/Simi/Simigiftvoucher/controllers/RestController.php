<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 7/4/17
 * Time: 10:12 AM
 */
class Simi_Simigiftvoucher_RestController extends Simi_Simiconnector_RestController
{
    public function v2Action(){
        ob_start();
        try{
            $result = $this->_getServer()
                ->init($this)->run();
            $this->_printData($result);
        }catch (Exception $e){
            $result = array();
            $result['error'] = array(
                'code' => $e->getCode(),
                'message'=> $e->getMessage(),
            );
            $this->_printData($result);
        }
        exit();
        ob_end_flush();
    }

}
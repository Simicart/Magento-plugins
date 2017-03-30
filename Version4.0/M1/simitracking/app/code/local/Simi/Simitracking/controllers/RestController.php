<?php

class Simi_Simitracking_RestController extends Simi_Simiconnector_Controller_Action {
    public function preDispatch()
    {
        parent::preDispatch();
        $enable = (int)Mage::getStoreConfig("simitracking/general/enable");
        
        if (!$enable) {
            echo 'SimiTracking Connector was disabled!';
            header("HTTP/1.0 503");
            exit();
        }
    }
    
    protected function isHeader() {
        return true;
    }
    
    
    public function v2Action() {
        ob_start();
        try {
            $result = $this->_getServer()
                            ->init($this)->run();
            $this->_printData($result);
        } catch (Exception $e) {
            $results = array();
            $result = array();
            if (is_array($e->getMessage())) {
                $messages = $e->getMessage();
                foreach ($messages as $message) {
                    $result[] = array(
                        'code' => $e->getCode(),
                        'message' => $message,
                    );
                }
            } else {
                $result[] = array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                );
            }
            $results['errors'] = $result;
            $this->_printData($results);
        }
        exit();
        ob_end_flush();
    }
        
    protected function _getServer(){
        return Mage::getSingleton('simitracking/server');
    }

}

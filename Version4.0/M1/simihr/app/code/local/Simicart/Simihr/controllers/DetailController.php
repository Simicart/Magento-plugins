<?php
class Simicart_Simihr_DetailController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();
     	$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->_initLayoutMessages('core/session'); 
        $this->renderLayout();

        if (isset($_POST['submit'])) {
//            print_r($_POST);
//            die();
            if (isset($_FILES['resume_cv']['name']) && $_FILES['resume_cv']['name'] != '') {
                try {
                    
                    $_FILES['resume_cv']['name'] = self::stripVN($_FILES['resume_cv']['name']);
                    $_FILES['resume_cv']['name'] = str_replace(" ", "_", $_FILES['resume_cv']['name']);
                    $fileName       = $_FILES['resume_cv']['name'];
                    $fileExt        = strtolower(substr(strrchr($fileName, ".") ,1));
                    $fileNamewoe    = rtrim($fileName, $fileExt);
                    // $fileName       = preg_replace('/\s+', '', $fileNamewoe) . time() . '.' . $fileExt;
                    $uploader       = new Varien_File_Uploader('resume_cv');
                    $uploader->setAllowedExtensions(array('doc', 'docx','pdf'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::getBaseDir('media') ;
                    if(!is_dir($path)){
                        mkdir($path, 0777, true);
                    }
                    $uploader->save($path . DS . 'simihr' . DS . 'submissions' . DS, $fileName );
                    $path1 = $path . DS . 'simihr' . DS . 'submissions' . DS. $fileName;
                    $filename1 = $fileName;

                } catch (Exception $e) {
                    $error = true;
                }
            }

            if (isset($_FILES['cover_letter']['name']) && $_FILES['cover_letter']['name'] != '') {
                try {
                    $_FILES['cover_letter']['name'] = self::stripVN($_FILES['cover_letter']['name']);
                    $_FILES['cover_letter']['name'] = str_replace(" ", "_", $_FILES['cover_letter']['name']);
                    $fileName       = $_FILES['cover_letter']['name'];
                    $fileExt        = strtolower(substr(strrchr($fileName, ".") ,1));
                    $fileNamewoe    = rtrim($fileName, $fileExt);
                    // $fileName       = preg_replace('/\s+', '', $fileNamewoe) . time() . '.' . $fileExt;
                    $uploader       = new Varien_File_Uploader('cover_letter');
                    $uploader->setAllowedExtensions(array('doc', 'docx','pdf'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::getBaseDir('media') ;
                    if(!is_dir($path)){
                        mkdir($path, 0777, true);
                    }
                    $uploader->save($path . DS . 'simihr' . DS . 'submissions' . DS, $fileName );
                    $path2 = $path . DS . 'simihr' . DS . 'submissions' . DS. $fileName;
                    $filename2 = $fileName;
                } catch (Exception $e) {
                    $error = true;
                }

            }

            // add to submission to DB
            $path = Mage::getBaseDir('media') ;
            if(isset($_GET['job'])) {
                $job_applied = $_GET['job'];
            } else {
                $job_applied = '';
            }

            if($_FILES['cover_letter']['name'] != '') {
                $cover_letter_path = $_FILES['cover_letter']['name'];
            } else {
                $cover_letter_path = '';
            }

            $resume_cv_path = $_FILES['resume_cv']['name'];
            $data = array(
                'first_name' => $_POST['first_name'],
                'last_name'  => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone'  => $_POST['phone'],
                'job_applied' => $job_applied,
                'comment'  => $_POST['sourceinfo'],
                'resume_cv_path' => $resume_cv_path,
                'cover_letter_path'  => $cover_letter_path
            );
            $email_applied = $_POST['email'];
            $model = Mage::getModel('simihr/submissions');

            try {
                $model->setData($data)
                ->save();
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $data = [];
            if (isset($_POST['job_name'])) {
                $data['job_name'] = $_POST['job_name'];
                $data['email_applied'] = $email_applied;
                $data['first_name'] = $_POST['first_name'];
                $data['last_name'] = $_POST['last_name'];
                $data['phone'] =$_POST['phone'];
                $data['sourceinfo'] = $_POST['sourceinfo'];
            }
       
            self::sendMail($data,$title,$path1,$path2,$filename1,$filename2);
            echo "<script>alert('Your submisstion has been send.')</script>";

        }
	}

	public function submitAction()
	{
		
	}

    public function stripVN($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);

        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        return $str;
    }

    public function sendMail($data, $title,$path1 = null,$path2 = null,$filename1,$filename2) {
         // Mage::log("Run cron to send mail!");
        $ids = Mage::getResourceModel('simihr/content_collection')->addFieldToFilter('name', 'transactional_email_id')->getData();
        if (isset($ids[0])) {
            $id = (int)$ids[0]['note'];
        } else $id = 179;

        $templateId = $id;
        // get store and config
        $store = Mage::app()->getStore();
        $config = array(
            'area' => 'frontend',
            'store' => $store->getId()
        );

        $sender = array(
            'name' => 'Simihr Notice',
            'email' => 'simihrhr@simicart.com',
        );

        $recipient_email = 'hr@simicart.com';
        $recipient_name = 'hr';

        // add variable
        $vars = array('store' => $store);
        if (sizeof($data) > 0) {
            foreach ($data as $key => $value) {
                $vars[$key] = $value;
            }
        }

        // send transaction email
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $storeId = Mage::app()->getStore()->getId();

        $add_cc=array("hieu@simicart.com");
        $mail = Mage::getModel('core/email_template');
        $mail->getMail()->addCc($add_cc);
        if (file_exists($path1)) {
            $mail->getMail()
                ->createAttachment(
                    file_get_contents($path1),
                    Zend_Mime::TYPE_OCTETSTREAM,
                    Zend_Mime::DISPOSITION_ATTACHMENT,
                    Zend_Mime::ENCODING_BASE64,
                    basename($filename1)
                );
        }
        if (file_exists($path2)) {
            $mail->getMail()
                ->createAttachment(
                    file_get_contents($path1),
                    Zend_Mime::TYPE_OCTETSTREAM,
                    Zend_Mime::DISPOSITION_ATTACHMENT,
                    Zend_Mime::ENCODING_BASE64,
                    basename($filename2)
                );
        }
        $mail->setDesignConfig($config)
            ->sendTransactional($templateId, $sender, $recipient_email, $recipient_name, $vars, $storeId);
        $translate->setTranslateInline(true);
        Mage::log("Simihr sent mail to hr@simicart.com and max@simicart.com");
    }


	
}
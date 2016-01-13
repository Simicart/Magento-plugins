<?php
class Simicart_UserManagement_TestController extends Mage_Core_Controller_Front_Action
{
	
	public function createImageAction(){
		$file = Mage::getBaseDir('media'). DS . 'simicart' . DS . 'languages'. DS.'210f8511708eee01582b78ff241481a6/it_IT/default.csv';
		
		$csv = new Varien_File_Csv();
		$data = $csv->getData($file);
		
		$codes = $data[0];
		print_r($codes);
		return;
		$image_file = $_GET['src'];
		$corner_radius = isset($_GET['radius']) ? $_GET['radius'] : 20; // The default corner radius is set to 20px
		$angle = isset($_GET['angle']) ? $_GET['angle'] : 0; // The default angle is set to 0º
		$topleft = (isset($_GET['topleft']) and $_GET['topleft'] == "no") ? false : true; // Top-left rounded corner is shown by default
		$bottomleft = (isset($_GET['bottomleft']) and $_GET['bottomleft'] == "no") ? false : true; // Bottom-left rounded corner is shown by default
		$bottomright = (isset($_GET['bottomright']) and $_GET['bottomright'] == "no") ? false : true; // Bottom-right rounded corner is shown by default
		$topright = (isset($_GET['topright']) and $_GET['topright'] == "no") ? false : true; // Top-right rounded corner is shown by default
		 
		$images_dir = '';
		$corner_source = imagecreatefrompng('rounded_corner.png');
		$corner_width = imagesx($corner_source);  
		$corner_height = imagesy($corner_source);  
		$corner_resized = imagecreatetruecolor($corner_radius, $corner_radius);
		imagecopyresampled($corner_resized, $corner_source, 0, 0, 0, 0, $corner_radius, $corner_radius, $corner_width, $corner_height);
		 
		$corner_width = imagesx($corner_resized);  
		$corner_height = imagesy($corner_resized);  
		$image = imagecreatetruecolor($corner_width, $corner_height);  
		$image = imagecreatefromjpeg($images_dir . $image_file); // replace filename with $_GET['src'] 
		$size = getimagesize($images_dir . $image_file); // replace filename with $_GET['src'] 
		$white = imagecolorallocate($image,255,255,255);
		$black = imagecolorallocate($image,0,0,0);
		 
		// Top-left corner
		if ($topleft == true) {
			$dest_x = 0;  
			$dest_y = 0;  
			imagecolortransparent($corner_resized, $black); 
			imagecopymerge($image, $corner_resized, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);
		} 
		 
		// Bottom-left corner
		if ($bottomleft == true) {
			$dest_x = 0;  
			$dest_y = $size[1] - $corner_height; 
			$rotated = imagerotate($corner_resized, 90, 0);
			imagecolortransparent($rotated, $black); 
			imagecopymerge($image, $rotated, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);  
		}
		 
		// Bottom-right corner
		if ($bottomright == true) {
			$dest_x = $size[0] - $corner_width;  
			$dest_y = $size[1] - $corner_height;  
			$rotated = imagerotate($corner_resized, 180, 0);
			imagecolortransparent($rotated, $black); 
			imagecopymerge($image, $rotated, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);  
		}
		 
		// Top-right corner
		if ($topright == true) {
			$dest_x = $size[0] - $corner_width;  
			$dest_y = 0;  
			$rotated = imagerotate($corner_resized, 270, 0);
			imagecolortransparent($rotated, $black); 
			imagecopymerge($image, $rotated, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);  
		}
		 
		// Rotate image
		$image = imagerotate($image, $angle, $white);
		 
		// Output final image
		imagejpeg($image);
		 
		// Remove temp files
		imagedestroy($image);  
		imagedestroy($corner_source);
		
	}
	
	public function convertAndResizeImage($imageFile, $newWidth, $newHeight, $name){
		if(strpos($imageFile, '.jpg') || strpos($imageFile, '.jpeg'))
			$image = imagecreatefromjpeg($imageFile);
		elseif(strpos($imageFile, '.gif'))
			$image = imagecreatefromgif($imageFile);
		elseif(strpos($imageFile, '.png'))
			$image = imagecreatefrompng($imageFile);
		
		list($width, $height) = getimagesize($imageFile);
		$newImage = imagecreatetruecolor($width, $height);
		if($image){
			imagecopyresized($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
			imagepng($newImage, $name); 
		}
	}
	
	public function runRubyUploading($account, $password, $appFile){
		$ruby = Mage::app()->getLayout()
				->createBlock('core/template')
				->setTemplate('usermanagement/ruby-upload-app.phtml')
				->setAccount($account)
				->setPassword($password)
				->setAppFile($appFile);
				
		$tempDir = md5(now());
		$rubyFile = "$tempDir.rb";
		$rubyUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$rubyFile;
		
		$html = $ruby->toHtml();		
		
		try{
			$handle = fopen($rubyFile, 'w');
			fwrite($handle, $html);
		}catch(Exception $e){	
		}
		
		$ssh = Mage::getModel('usermanagement/ssh')->connectToServer();
		if($ssh){
			$shellScript = "curl -O $rubyUrl&&ruby $tempDir.rb&&rm -f $tempDir.rb";
			$ssh->exec($shellScript);
		}
		unlink($rubyFile);
	}
	
	public function indexAction(){
		
		/*$http = new Varien_Http_Adapter_Curl();
		$cookie = 'cookie.txt';
		$account = 'nthanhbk@gmail.com'; 
		$password= 'In311truongchinh';
		$appIdPrefix = "88X6EP4WFV";
		
		$config = array('timeout' => 3000,'verifypeer' => FALSE,'verifyhost' => FALSE);
		
		$naAppCodeName = 'Mozilla';
		$naAppName = 'Netscape';
		$naAppVersion = '5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36';
		
		
		$options = array(CURLOPT_USERAGENT => "$naAppCodeName/$naAppVersion",
						CURLOPT_COOKIEJAR => realpath($cookie),
						CURLOPT_COOKIEFILE => realpath($cookie)
					);
    	$http->setConfig($config);
		$http->setOptions($options);
		
		
		//get link login
		$url = 'https://appleid.apple.com/cgi-bin/WebObjects/MyAppleId.woa/wa/directToSignIn?localang=en_US';
		$http->write(Zend_Http_Client::GET, $url, '1.1');
		$content = $http->read();
		
		$regex_pattern =  "/id=\"signIn\" name=\"appleConnectForm\" action=\"(.*?)\">/";
		preg_match($regex_pattern, $content, $match);
		
		$url = 'https://appleid.apple.com'.$match[1];
		
		$regex_pattern =  "/id=\"fdcBrowserDataId\" type=\"hidden\" name=\"(.*?)\"/";
		preg_match($regex_pattern, $content, $match);
		$fdcBrowserDataId = $match[1];
		
		$regex_pattern =  "/id=\"actionChosen\" type=\"hidden\" name=\"(.*?)\"/";
		preg_match($regex_pattern, $content, $match);
		$actionChosen = $match[1];
		
		$regex_pattern =  "/name=\"wosid\" value=\"(.*?)\"/";
		preg_match($regex_pattern, $content, $match);
		$wosid = $match[1];
		
		$naProductSub = '20030107';
		$naBrowserLanguage = 'undefined';
		$naCookieEnabled = 'true';
		$navigatorOscpu = $naCpuClass = ''; //undefined
		$naOnLine = 'true';
		$naPlatform = 'Win32';
		$naSystemLanguage = 'undefined';
		$naLanguage = 'en-US';
		$documentDefaultCharset = 'ISO-8859-1';
		$documentDomain = 'appleid.apple.com';
		$screenDeviceXDPI = 'undefined';
		$screenDeviceYDPI = 'undefined';
		$screenFontSmoothingEnabled = 'undefined';
		$screenUpdateInterval = 'undefined';
		$timeZoneOffset = date('Z')/3600;
		$date = '6/7/2005 9:33:44 PM';
		$screenHeight = '768';
		$screenWidth = '1366';
		$pluginsAcrobat = '';
		$pluginsFlashVersion = '13.0';
		$pluginsQuickTime = '';
		$pluginsAcrobat = '';
		$pluginsJava = '10.51.2';
		$pluginsDirector = '';
		$pluginsOffice = '2010';
		$runTime = rand(20, 50);//thoi gian javascript chay den luc nay
		$offsetHour = -date('Z')/60;
		$currentTime = date('n/d/Y H:i:s A');
		$screenColorDepth = 24;
		$windowScreenSvailLeft = 0;
		$windowScreenSvailTop = 0;
		$flash = 'Shockwave Flash%7CShockwave Flash 12.0 r0';
		$spanOffset = '18';
		$milisecondTime = round(microtime(true) * 1000);
		
		$offsetGmt = date('P');
		
		$encodeAppVersion = $naAppVersion;
		
		$encodeAppVersion = str_replace(' ', '%20', $encodeAppVersion);
		$encodeAppVersion = str_replace('(', '%28', $encodeAppVersion);
		$encodeAppVersion = str_replace(')', '%29', $encodeAppVersion);
		$encodeAppVersion = str_replace(';', '%3B', $encodeAppVersion);
		$encodeAppVersion = str_replace(',', '%2C', $encodeAppVersion);
		
		$u = "$naAppCodeName/$naAppVersion";
		$z = "GMT$offsetGmt";
		$f = "TF1;016;;;;;;;;;;;;;;;;;;;;;;$naAppCodeName;$naAppName;$encodeAppVersion;$naProductSub;"
			."$naBrowserLanguage;$naCookieEnabled;$navigatorOscpu;$naOnLine;$naPlatform;$naSystemLanguage;"
			."$naAppCodeName/$encodeAppVersion;$naLanguage;$documentDefaultCharset;$documentDomain;$screenDeviceXDPI;"
			."$screenDeviceYDPI;$screenFontSmoothingEnabled;$screenUpdateInterval;false;false;$milisecondTime;$timeZoneOffset;"
			."$date;$screenWidth;$screenHeight;$pluginsAcrobat;$pluginsFlashVersion;$pluginsQuickTime;$pluginsJava;"
			."$pluginsDirector;$pluginsOffice;$runTime;$offsetHour;$offsetHour;$currentTime;$screenColorDepth;"
			."$screenWidth;$screenHeight;$windowScreenSvailLeft;$windowScreenSvailTop;;;;;;"
			."$flash;;;;;;;;;;;;;$spanOffset;;;;;;;";
		
		$f =  str_replace(' ', '%20', $f);
		$f =  str_replace(':', '%3A', $f);
		
		$str = '{"U":"'.$u.'","L":"en-US","Z":"'.$z.'","V":"1.1","F":"'.$f.'"}';
		
		$body = array(
					$actionChosen => '',
					$fdcBrowserDataId => $str,
					'theAccountName'  => $account,
					'theAccountPW' => $password,
					'signInHyperLink'  => 'Sign in',
					'theTypeValue' => '',
					'inframe' => 0,
					'wosid' => $wosid,
					'Nojive'  => '');
					

		$http->write(Zend_Http_Client::POST, $url, '1.1', NULL, $body);
		$content = $http->read();
		
		
		/*$url = 'https://developer.apple.com/membercenter/index.action';
		$http->write(Zend_Http_Client::GET, $url, '1.1');
		$content = $http->read();

		
		
		if(!strpos($content, 'Your Account')){//not logged in
			$pos1 = strpos($content, 'Location:');
			$pos2 = strpos($content, 'Content-Length:');
			$nextUrl = substr($content, $pos1+ 10, $pos2-$pos1-10);
			
			
			$http->write(Zend_Http_Client::GET, trim($nextUrl), '1.1');
			$content = $http->read();
			
			$regex_pattern =  "/name=\"form2\" action=\"authenticate;jsessionid=(.*?)\"/";
			preg_match($regex_pattern, $content, $match);
			
			//login
			$url = 'https://idmsa.apple.com/IDMSWebAuth/authenticate;jsessionid='.$match[1];
	
					
			$naProductSub = '20030107';
			$naBrowserLanguage = 'undefined';
			$naCookieEnabled = 'true';
			$navigatorOscpu = $naCpuClass = ''; //undefined
			$naOnLine = 'true';
			$naPlatform = 'Win32';
			$naSystemLanguage = 'undefined';
			$naLanguage = 'en-US';
			$documentDefaultCharset = 'ISO-8859-1';
			$documentDomain = 'idmsa.apple.com';
			$screenDeviceXDPI = 'undefined';
			$screenDeviceYDPI = 'undefined';
			$screenFontSmoothingEnabled = 'undefined';
			$screenUpdateInterval = 'undefined';
			$timeZoneOffset = date('Z')/3600;
			$date = '6/7/2005 9:33:44 PM';
			$screenHeight = '768';
			$screenWidth = '1366';
			$pluginsAcrobat = '';
			$pluginsFlashVersion = '13.0';
			$pluginsQuickTime = '';
			$pluginsAcrobat = '';
			$pluginsJava = '10.51.2';
			$pluginsDirector = '';
			$pluginsOffice = '2010';
			$runTime = rand(20, 50);//thoi gian javascript chay den luc nay
			$offsetHour = -date('Z')/60;
			$currentTime = date('n/d/Y H:i:s A');
			$screenColorDepth = 24;
			$windowScreenSvailLeft = 0;
			$windowScreenSvailTop = 0;
			$flash = 'Shockwave Flash%7CShockwave Flash 12.0 r0';
			$spanOffset = '20';
			$milisecondTime = round(microtime(true) * 1000);
			
			$offsetGmt = date('P');
			
			$encodeAppVersion = $naAppVersion;
			
			$encodeAppVersion = str_replace(' ', '%20', $encodeAppVersion);
			$encodeAppVersion = str_replace('(', '%28', $encodeAppVersion);
			$encodeAppVersion = str_replace(')', '%29', $encodeAppVersion);
			$encodeAppVersion = str_replace(';', '%3B', $encodeAppVersion);
			$encodeAppVersion = str_replace(',', '%2C', $encodeAppVersion);
			
			$u = "$naAppCodeName/$naAppVersion";
			$z = "GMT$offsetGmt";
			$f = "TF1;016;;;;;;;;;;;;;;;;;;;;;;$naAppCodeName;$naAppName;$encodeAppVersion;$naProductSub;"
				."$naBrowserLanguage;$naCookieEnabled;$navigatorOscpu;$naOnLine;$naPlatform;$naSystemLanguage;"
				."$naAppCodeName/$encodeAppVersion;$naLanguage;$documentDefaultCharset;$documentDomain;$screenDeviceXDPI;"
				."$screenDeviceYDPI;$screenFontSmoothingEnabled;$screenUpdateInterval;false;false;$milisecondTime;$timeZoneOffset;"
				."$date;$screenWidth;$screenHeight;$pluginsAcrobat;$pluginsFlashVersion;$pluginsQuickTime;$pluginsJava;"
				."$pluginsDirector;$pluginsOffice;$runTime;$offsetHour;$offsetHour;$currentTime;$screenColorDepth;"
				."$screenWidth;$screenHeight;$windowScreenSvailLeft;$windowScreenSvailTop;;;;;;"
				."$flash;;;;;;;;;;;;;$spanOffset;;;;;;;";
			
			$f =  str_replace(' ', '%20', $f);
			$f =  str_replace(':', '%3A', $f);
			
			$str = '{"U":"'.$u.'","L":"en-US","Z":"'.$z.'","V":"1.1","F":"'.$f.'"}';
			
			$body = array(
						'language' => '',
						'rv' => '',
						'sslEnabled' => '',
						'disable2SV' => '',
						'Env' => 'PROD',
						'fdcBrowserData' => $str,
						'appleId' => $account,
						'accountPassword' =>$password);
			
			
			print_r($body);
			$header = array(
				'Content-Type:application/x-www-form-urlencoded',
				
				'Accept-Language:en-US,en;q=0.8,vi;q=0.6',
			);
			
			$http->write(Zend_Http_Client::POST, $url, '1.1', $header, $body);
			$content = $http->read();
			
		}
		*/
		
		$appId= 29;
		$app = Mage::getModel('usermanagement/app')->load($appId);
		$website = Mage::getModel('usermanagement/website')->load($app->getWebsiteId());
		
		$appName = $website->getWebsiteName();
		$bundleId = $website->getIdentifyKey();
		$version = $app->getVersion();
		$websiteUrl = $website->getWebsiteUrl();
		$description = $website->getWebsiteDescription();
		$keywords = $appName;
		$contactInfo = array('Tan', 'Hoang', 'tan@magestore.com', '+84969679990');
		
		$pathMedia = Mage::getBaseDir('media').'/simicart/';
		
		$images = array($pathMedia. 'icons/'.$website->getData('icon'), 
				$pathMedia. 'splashscreens/'.$website->getData('splash_screen'), 
				$pathMedia. 'splashscreens/'.$website->getData('splash_screen_ip4')
			);
		
		
		$this->convertAndResizeImage($images[0], 1024, 1024, 'temp_icon.png');
		$this->convertAndResizeImage($images[1], 640, 1136, 'temp_splash_screen.png');
		$this->convertAndResizeImage($images[2], 640, 960, 'temp_splash_screen_ip4.png');

		
		$contactInfo = array('Tan', 'Hoang', 'tan@magestore.com', '+84969679990');
		
        $http = new Varien_Http_Adapter_Curl();
		$cookie = 'cookie1.txt';
		$account = 'nthanhbk@gmail.com'; 
		$password= 'In311truongchinh';
		$appIdPrefix = "88X6EP4WFV";
		
		$config = array('timeout' => 3000,'verifypeer' => FALSE,'verifyhost' => FALSE);
		$options = array(CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36",
						CURLOPT_COOKIEJAR => realpath($cookie),
						CURLOPT_COOKIEFILE => realpath($cookie),
						//CURLOPT_COOKIESESSION => true,
						//CURLOPT_RETURNTRANSFER => true
					);
    	$http->setConfig($config);
		$http->setOptions($options);
		
		
		$url = 'https://itunesconnect.apple.com/WebObjects/iTunesConnect.woa';
		$http->write(Zend_Http_Client::GET, $url, '1.1');
		$content = $http->read();
		
		
		if(!strpos($content, 'Manage Your Apps')){// chua login
			//die('xxxx');
			$regex_pattern =  "/appleConnectForm\" method=\"post\" action=\"(.*?)\">/";
			preg_match($regex_pattern, $content, $match);
			
			//post login
			$url = 'https://itunesconnect.apple.com'.$match[1];
			$body = 'theAccountName='.$account.'&theAccountPW='.$password; 
			$http->write(Zend_Http_Client::POST, $url, '1.1', NULL, $body);
			$content = $http->read();
			
			//$regex_pattern =  "/WebObjects(.*?)x-apple-application-instance/";
			//preg_match($regex_pattern, $content, $match);
			$pos1 = strpos($content, 'woa/wo/');
			$pos2 = strpos($content, 'x-apple-application-instance');
			
			
			//after login
			$url = 'https://itunesconnect.apple.com/WebObjects/iTunesConnect.woa/wo/'.substr($content, $pos1+7, $pos2-$pos1-9);
			$http->write(Zend_Http_Client::GET, $url, '1.1');
			$content = $http->read();
		
		}
		
		$regex_pattern =  "/<a href=\"(.*?)\">Manage Your Apps<\/a>/";
		preg_match($regex_pattern, $content, $match);
		
		
		//manage app
		$url = 'https://itunesconnect.apple.com'.$match[1];
		
		$http->write(Zend_Http_Client::GET, $url, '1.1');
		$content = $http->read();
		
		
		$regex_pattern = "/id=\"mainForm\" action=\"(.*?)\"/";
		preg_match($regex_pattern, $content, $match);
		
		//search app
		$url = 'https://itunesconnect.apple.com'.$match[1];
		
		$regex_pattern = "/class='search-param-compare-sku' id=''><select name=\"(.*?)\"/";
		preg_match($regex_pattern, $content, $match);
		$searchSkuType = $match[1];
		
		$regex_pattern = "/class='search-param-value-sku' id=''><input type=\"text\" name=\"(.*?)\"/";
		preg_match($regex_pattern, $content, $match);
		$searchSku = $match[1];
		
		$regex_pattern = "/type=\"submit\" value=\"Search\" name=\"(.*?)\"/";
		preg_match($regex_pattern, $content, $match);
		$submit = $match[1];
		
		$regex_pattern = "/class='search-param-value-statusSearch' id=''><select name=\"(.*?)\"/";
		preg_match($regex_pattern, $content, $match);
		$status = $match[1];
		
		$regex_pattern = "/name=\"(.*?)\"/";
		preg_match_all($regex_pattern, $content, $matches);
		
		$body = array();
		$i = 0;
		foreach($matches[1] as $item){
			if(strpos($item, '.')){
				if($i == 0)
					$body[$item] = 4;
				else
					$body[$item] = '';
				$i++;
			}
		}
		
		$body[$searchSkuType] = 0;
		$body[$searchSku] = $bundleId;
		$body[$submit] = 'Search';
		$body[$status] = 'WONoSelectionString';
		
		
		$http->write(Zend_Http_Client::POST, $url, '1.1', NULL, $body);
		$content1 = $http->read();
		
		if(strpos($content1, 'The following error(s) occurred:')){ // search error, create new app
			
			$content= str_replace(array("\n", "\r", "\t", " "), "", $content);
			$regex_pattern = "/upload-app-button\"><ahref=\"(.*?)\">/";
			preg_match($regex_pattern, $content, $match);
			
			
			//new app url
			$url = 'https://itunesconnect.apple.com'.$match[1];
			$http->write(Zend_Http_Client::GET, $url, '1.1');
			$content = $http->read();
			
			$regex_pattern = "<input(.*?)type=\"text\"(.*?)name=\"(.*?)\" \/>";
			preg_match_all($regex_pattern, $content, $matches);
			
			$nameLabel = $matches[3][0];
			$skuLabel = $matches[3][1];
			//$bundleIdLabel = $matches[3][2];
			
			$regex_pattern = "/<select id=\"default-language-popup\" name=\"(.*?)\">/";
			preg_match($regex_pattern, $content, $match);
			$languageLabel = $match[1];
			
			$regex_pattern = "/<select id=\"primary-popup\" name=\"(.*?)\">/";
			preg_match($regex_pattern, $content, $match);
			$bundleIdLabel = $match[1];
			
			
			$regex_pattern = "/id=\"mainForm\" action=\"(.*?)\">/";
			preg_match($regex_pattern, $content, $match);
			
			
			$url = 'https://itunesconnect.apple.com'.$match[1];
			
			
			$regex_pattern = "/<option value=\"(.*?)\">(.*?) - $bundleId<\/option>/";
			preg_match($regex_pattern, $content, $match);
			$bundleIdId = $match[1];
			
			$regex_pattern = "/class=\"continueActionButton\" type=\"image\" name=\"(.*?)\"/";
			preg_match($regex_pattern, $content, $match);
			$imageLabel = $match[1];
			
			
			$body = $nameLabel.'='.$appName.'&'.$skuLabel.'=simicart_'.$bundleId.'&'.$bundleIdLabel.'='
				.$bundleIdId.'&'.$languageLabel.'=6&'.$imageLabel.'.x=1081&'.$imageLabel.'.y=249';
			
			
			$http->write(Zend_Http_Client::POST, $url, '1.1', NULL, $body);
			$content = $http->read();
			//print_r($content);die();
			//$regex_pattern = "/iTunesConnect.woa(.*?)/";
			//preg_match($regex_pattern, $content, $match);
			$pos1 = strpos($content, 'woa/wo/');
			$pos2 = strpos($content, 'x-apple-application-instance');
			$url = 'https://itunesconnect.apple.com/WebObjects/iTunesConnect.woa/wo/'.substr($content, $pos1+7, $pos2-$pos1-9);
			
			//page fill date and price
			$http->write(Zend_Http_Client::GET, $url, '1.1');
			$content = $http->read();
			
			
			$regex_pattern = "/id=\"mainForm\" action=\"(.*?)\"/";
			preg_match($regex_pattern, $content, $match);
			$url = 'https://itunesconnect.apple.com'.$match[1];
			//print_r($match);
			$regex_pattern = "/<select(.*?)name=\"(.*?)\">/";
			preg_match_all($regex_pattern, $content, $matches);
			$labels = $matches[2];
			
			$regex_pattern = "/class=\"continueActionButton\" type=\"image\" name=\"(.*?)\"/";
			preg_match($regex_pattern, $content, $match);
			$imageLabel = $match[1];
			
			$body = $labels[0] . '=' . (date('n')-1) .'&'.$labels[1].'='. (date('j')-1) . '&'. $labels[2].'=0'.
			'&'.$labels[3].'=0&'.$imageLabel.'.x=1093&'.$imageLabel.'.y=392';
			
			$regex_pattern ="/<input class=\"country-checkbox\" type=\"checkbox\" name=\"(.*?)\" value=\"(.*?)\" checked=\"checked\"/";
			preg_match_all($regex_pattern, $content, $matches);
			
			$i = 0;
			foreach($matches[1] as $name){
				$body .= '&' . $name . '=' . $matches[2][$i];
				$i++;
			}
			
			
			$http->write(Zend_Http_Client::POST, $url, '1.1', NULL, $body);
			$content = $http->read();
			
			$pos1 = strpos($content, 'woa/wo/');
			$pos2 = strpos($content, 'x-apple-application-instance');
			$url = 'https://itunesconnect.apple.com/WebObjects/iTunesConnect.woa/wo/'.substr($content, $pos1+7, $pos2-$pos1-9);
			
			
			//fill info page
			$http->write(Zend_Http_Client::GET, $url, '1.1');
			$content = $http->read();
			
			$regex_pattern ="/id=\"versionInitForm\" action=\"(.*?)\"/";
			preg_match($regex_pattern, $content, $match);
			$url = 'https://itunesconnect.apple.com'.$match[1];
			
			$regex_pattern = "/id=\"version-primary-popup\" name=\"(.*?)\"/";
			preg_match($regex_pattern, $content, $match);
			$primaryCategoryName = $match[1];
			//echo $match[1];die();
			
			$regex_pattern ="/<input  type=\"text\"(.*?)name=\"(.*?)\"/";
			preg_match_all($regex_pattern, $content, $matches);
			
			$names = $matches[2];
			$versionName = $names[0];
			$copyrightName = $names[1];
			$keywordsName = $names[2];
			$supportUrlName = $names[3];
			
			$firstnameName = $names[6];
			$lastnameName = $names[7];
			$emailName = $names[8];
			$phoneName = $names[9];
			
			$tradeRepFirstnameName = $names[12];
			$tradeRepLastnameName = $names[13];
			
			$addressLine1Name = $names[14];
			$addressLine2Name = $names[15];
			$cityName = $names[16];
			$postalCodeName = $names[17];
			
			$contactEmailName = $names[19];
			$contactPhoneName = $names[20];
			
			
			//description
			$regex_pattern ="/<textarea onblur(.*?)name=\"(.*?)\"><\/textarea>/";
			preg_match($regex_pattern, $content, $match);
			$descriptionName = $match[2];
			
			//country
			$regex_pattern ="/<select id=\"country-popup\" name=\"(.*?)\">/";
			preg_match($regex_pattern, $content, $match);
			$countryName = $match[1];
			
			//rate Content Descriptions
			$regex_pattern ="/<input class=\"br-1\" id=\"rank-1\" type=\"radio\" value=\"(.*?)\" name=\"(.*?)\" \/>/";
			preg_match_all($regex_pattern, $content, $matches);
			
			$str= '';
			$i = 0;
			foreach($matches[2] as $ratingName){
				$str .= $ratingName . '=' . $matches[1][$i++] . '&';	
			}
			$str = trim($str, '&');
					
			$regex_pattern = "/name = \"uploadSessionID\" value=\"(.*?)\">/";
			preg_match($regex_pattern, $content, $match);
			$sessionId = $match[1];
	
			
			$http->addOption(CURLOPT_BINARYTRANSFER, true);
			$http->addOption(CURLOPT_CUSTOMREQUEST, "POST");
			$http->addOption(CURLOPT_UPLOAD, 1);
			
			
			
			//upload images
			$uploadUrl = 'https://itunesconnect.apple.com/WebObjects/iTunesConnect.woa/wa/LCUploader/upload?uploadKey=';
			$uploadKeys = array('largeAppIcon', '35InchRetinaDisplayScreenshots', 'iPhone5');
			
			$i = 0;
			foreach($images as $imageName){
				$image = fopen($imageName, "rb");
				$http->addOption(CURLOPT_INFILE, $image);
				$http->addOption(CURLOPT_INFILESIZE, filesize($imageName));
				
				$header = array(
					'Content-Type: image/png',
					'x-original-filename: '. $imageName,
					'x-uploadKey: '. $uploadKeys[$i],
					'x-uploadSessionID: '. $sessionId,
				);
				//print_r($header);
				$http->write(Zend_Http_Client::POST, $uploadUrl.$uploadKeys[$i], '1.1', $header);
				$content1 = $http->read();
				//print_r($content1);
				
				$header = array(
					'X-Prototype-Version:1.7',
					'X-Requested-With:XMLHttpRequest',
					'Content-type:application/x-www-form-urlencoded; charset=UTF-8',
					'Referer:'.$url,
				);
				
				$regex_pattern = "/'$uploadKeys[$i]', statusURL: '(.*?)'/";
				preg_match($regex_pattern, $content, $match);
				$afterUploadUrl = 'https://itunesconnect.apple.com'.$match[1];
				
				//echo $afterUploadUrl;
				$http->write(Zend_Http_Client::POST, $afterUploadUrl, '1.1', $header);
				$content1 = $http->read();
				$i++;
			}
			
			//submit info
			$body = $versionName.'='.$version.'&'.$copyrightName.'=Simicart&'.$primaryCategoryName.'=1&'.$keywordsName.'='
			.$keywords.'&'.$descriptionName.'='.$description.'&'.$supportUrlName.'='.$websiteUrl.'&'.
			$firstnameName . '=' . $contactInfo[0] . '&' . $lastnameName . '=' . $contactInfo[1] . '&'.
			$emailName . '=' . $contactInfo[2] . '&' . $phoneName . '=' . $contactInfo[3] . '&' . 
			$addressLine1Name . '=' . 'Lot 15/C16, Dinh Cong Living Urban,'. '&'.
			$addressLine2Name . '=' . 'Dinh Cong ward, Hoang Mai district'. '&'.
			$cityName . '=' . 'hanoi'. '&'.
			$postalCodeName . '=' . '10000'. '&'.
			$countryName . '=' . '252' . '&'. //vietnam
			$str;
			
			//echo $url;
			$http->addOption(CURLOPT_UPLOAD, false);
			$http->write(Zend_Http_Client::POST, $url, '1.1', NULL, $body);
			$content = $http->read();
			
			
			$pos1 = strpos($content, 'woa/wo/');
			$pos2 = strpos($content, 'x-apple-application-instance');
			$url = 'https://itunesconnect.apple.com/WebObjects/iTunesConnect.woa/wo/'.substr($content, $pos1+7, $pos2-$pos1-9);
			
			
			//view app
			$http->write(Zend_Http_Client::GET, $url, '1.1');
			$content = $http->read();
			
			$regex_pattern = "/<a class=\"blue-btn\" href=\"(.*?)\">View Details<\/a>/";
			preg_match($regex_pattern, $content, $match);
			$url  = 'https://itunesconnect.apple.com' . $match[1];
	
			//view detail app
			$http->write(Zend_Http_Client::GET, $url, '1.1');
			$content = $http->read();
	
			$regex_pattern = "/id=\"mainForm\" action=\"(.*?)\"/";
			preg_match($regex_pattern, $content, $match);
			$url = 'https://itunesconnect.apple.com' . $match[1];
			
			$regex_pattern = "/class=\"customActionButton\" type=\"image\" name=\"(.*?)\"/";
			preg_match($regex_pattern, $content, $match);
			
			$body = array($match[1].'.x' => 1044, $match[1].'.y' => 73);
			
			
			$http->write(Zend_Http_Client::POST, $url, '1.1', NULL, $body);
			$content = $http->read();
			
			$pos1 = strpos($content, 'woa/wo/');
			$pos2 = strpos($content, 'x-apple-application-instance');
			$url = 'https://itunesconnect.apple.com/WebObjects/iTunesConnect.woa/wo/'.substr($content, $pos1+7, $pos2-$pos1-9);
			
			//config prepare to upload page
			$http->write(Zend_Http_Client::GET, $url, '1.1');
			$content = $http->read();
	
			//form url
			$regex_pattern = "/id=\"mainForm\" action=\"(.*?)\">/";
			preg_match($regex_pattern, $content, $match);
			$url = 'https://itunesconnect.apple.com' . $match[1];
			
			
			$regex_pattern = "/class=\"saveChangesActionButton\" type=\"image\" name=\"(.*?)\"/";
			preg_match($regex_pattern, $content, $match);
			$buttonName = $match[1];
			
			$body = 'firstQuestionRadio=false&ipContentsQuestionRadio=false&booleanRadioButton=false&'.$buttonName.'.x=1120&'
			.$buttonName.'.y=695';
			
			//save
			$http->write(Zend_Http_Client::POST, $url, '1.1', NULL, $body);
			$content = $http->read();
			
			
		}else{
			$pos1 = strpos($content1, 'woa/wo/');
			$pos2 = strpos($content1, 'x-apple-application-instance');
			$url = 'https://itunesconnect.apple.com/WebObjects/iTunesConnect.woa/wo/'.substr($content1, $pos1+7, $pos2-$pos1-9);
			
			$http->write(Zend_Http_Client::GET, $url, '1.1');
			$content1 = $http->read();
			
			$regex_pattern = "/<a href=\"(.*?)\">$appName<\/a>/";
			preg_match($regex_pattern, $content1, $match);
			$url = 'https://itunesconnect.apple.com'.$match[1];
			
			//view app
			$http->write(Zend_Http_Client::GET, $url, '1.1');
			$content = $http->read();
			
			
			if(strpos($content, 'Waiting For Upload')){ //upload
				
			}
			
			
			
		}
		
		
		
		
		
		
	}
	
	/*public function installDbAction(){
		$setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
		$installer->run("

			ALTER TABLE {$setup->getTable('usermanagement_website')}
			MODIFY COLUMN `identify_key` varchar(100) NOT NULL default '';

			");
        $installer->endSetup();
        echo "success";
	}*/

	public function checkConnectorAction(){
		$col = Mage::getModel('usermanagement/user')->load(903);
		$i = 0;
		//foreach($cols as $col){
			$web = $col->getWebsite();
			$status = Mage::helper('usermanagement')->getCoreConnectorStatus($web);
			Zend_debug::dump($status);die();
		//	Zend_debug::dump();die();
			// if($web->getId()){
				// $web->setData('connector_status',1);
				// $web->save();
			// }
			if($web->getId()){
				if($web->getData('website_url') == "http://cubeboxsolutions.com/ecommerce/index.php"){
					die('xxxx');
				}
				
				$status = Mage::helper('usermanagement')->getCoreConnectorStatus($web);
				if($status == 'Ready'){
					$web->setData('connector_status',1); // ready
				} elseif($status == 'Not Install') {
					$web->setData('connector_status',0); // not install
				} else{
					$web->setData('connector_status',2); // installed
				}
			} else {
				continue;
				if($web->getId())
					$web->setData('connector_status',0);
			}
			$web->save();
		//}
		 
		echo 'Done';
	}

	public function haiAction(){
		$allow_ids = array(
			Mage::helper('usermanagement')->getCoreId(),
			Mage::helper('usermanagement')->getStandardId(),
			Mage::helper('usermanagement')->getPlatinumId(),
			Mage::helper('usermanagement')->getPremiumId(),
			Mage::helper('usermanagement')->getPartnerPackageId()
		);
		// customer id
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();

		// get list orders of customer
		$orders = Mage::getModel("sales/order")->getCollection()
				   ->addAttributeToSelect('status')
				   ->addFieldToFilter('customer_id', $customerId)
				   ->addAttributeToFilter('status', array('in' => array('complete')))
				   ->setOrder('created_at', 'desc');
		$order_count = sizeof($orders);
		Zend_debug::dump($orders->getData());die();
		if($order_count > 0){
			$productIds = array();
			$check = false;
			foreach($orders as $order){
				$items = $order->getAllVisibleItems();
				foreach($items as $item){
					if(in_array($item->getProductId(),$allow_ids)){
						$productIds[] = $item->getProductId();
						$check = true;
					}
					
 				}
			}
			if($check)
				return $productIds;
			else
				return array();
		} else {
			return array();
		}
	}
}

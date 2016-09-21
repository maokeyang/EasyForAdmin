 <?php
 /**
 * 短信接口
 * 
 */

class Message {

	private $_config;
	public function __construct(){
		$this->_config = array(
			'host' => '10.10.12.164:8005',
			'appid' => 'mangoweb15s6fgx934s',
			'appkey' => '876543ewfgwr4523gdg4gds2434f2223'
		);
		$this->_header = array('Content-Type:text/plain');
	}
  
   	public function sendsms($mobileNo,$sendType = '', $message = ''){

		$url = $this->_config['host'] . "/mbrse/sendSms.cgi";

		$post_data  = array(
			'adjustCode' => $this->createadjustCode('sendSms'),
			'appId' => $this->_config['appid'],
			'headMap' => array('mobileNo' => $mobileNo,'sendType' => 'verify_code'),
		);

		if($sendType == 'comm_code'){
			$post_data['headMap']['sendType'] = 'comm_code';
			$post_data['headMap']['message'] = $message;
		}

		return $this->returnRes($url, $this->_header, $post_data);
 	}

 	private function createadjustCode($method){

		return md5(md5($this->_config['appid'].$method).$this->_config['appkey']);
	}

 	private function returnRes($url='', $header=array(),$post_data=''){

		$result = curl_post_data($url, $header, json_encode($post_data)); 

		if($result[0] == "200"){

			return json_decode($result[1],true);
		}
		return null;
	}
}

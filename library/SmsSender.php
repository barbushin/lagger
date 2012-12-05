<?php

/**
 * 
 * @desc This is class for SMS gateway http://litesms.net/from/partner/liaren.html
 * @see http://code.google.com/p/lagger
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 * 
 */
define('SMS_SENDER_LOGIN', 'somelogin');
define('SMS_SENDER_PASSWORD', 'somepassword');

class SmsSender {
	
	protected $login;
	protected $password;

	public function __construct($login = SMS_SENDER_LOGIN, $password = SMS_SENDER_PASSWORD) {
		$this->login = $login;
		$this->password = $password;
	}

	protected function translit($string) {
		$table = array('А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'YO','Ж'=>'ZH','З'=>'Z','И'=>'I','Й'=>'J','К'=>'K','Л'=>'L','М'=>'M','М'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'C','Ч'=>'CH','Ш'=>'SH','Щ'=>'CSH','Ь'=>'','Ы'=>'Y','Ъ'=>'','Э'=>'E','Ю'=>'YU','Я'=>'YA','а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'zh','з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'csh','ь'=>'','ы'=>'y','ъ'=>'','э'=>'e','ю'=>'yu','я'=>'ya');
    return str_replace(array_keys($table), array_values($table), $string);
  }


	public function send($from, $to, $message, $translit = false) {
		$request = array(
		'action' => 'send_sms',
		'phone' => $to,
		'translit' => (int)$translit,
		'message' => $translit ? $this->translit($message) : $message,
		'from' => $from);

		$response = $this->makeRequest($request);
		if (preg_match('/Message_ID=(.+)$/u', $response, $m)) {
			return $m[1];
		}
		else {
			throw new SmsSender_Exception($response);
		}
	}

	protected function makeRequest(array $request, &$requestBody = null, &$responseBody = null) {
		$request['login'] = $this->login;
		$request['password'] = $this->password;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://litesms.net/sms.php');  
		
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
		
		$responseBody = curl_exec($ch);
		curl_close($ch);
		
		return $responseBody;
	}
}

class SmsSender_Exception extends Exception {
}

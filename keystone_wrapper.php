<html>
<body>
<?php

require 'vendor/autoload.php';


class keystone{
	private $log;
	
	public function __construct(){
		$this->log = new Katzgrau\KLogger\Logger(__DIR__.'/logs');
	}
	
	public function get_headers_from_curl_response($response)
	{
		$headers = array();
	
		$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
	
		foreach (explode("\r\n", $header_text) as $i => $line)
			if ($i === 0)
				$headers['http_code'] = $line;
			else
			{
				list ($key, $value) = explode(': ', $line);
	
				$headers[$key] = $value;
			}
	
			return $headers;
	}
	
	public function http_request($url, $body, $method, $content_type){
		$body= json_encode($body);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $content_type);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		$response = curl_exec($ch);
		$error = curl_error($ch);
		if($error != ''){
			$this->log->debug("Curl request failed:{$error}");
			return;
		}
		$this->log->debug("Curl request completed!");
		
		list($header, $body) = explode("\r\n\r\n", $response, 2);
		$header = $this->get_headers_from_curl_response($response);
		$body = json_decode($body,true);
		$body['header']=$header;
		
		//curl_close($ch);
		return $body;
		
	}

	
}

$obj=new keystone();

/*Authenticate user 
$url = "http://127.0.0.1:5000/v3/auth/tokens";
$body = array("auth"=> array("identity"=> array("methods"=> ["password"],
		"password"=>array("user"=>array("name"=> "j",
		"domain"=>array( "id"=> "default"),"password"=> "k"))))); 
echo "<pre>";
print_r($body);

$content_type = array("Content-Type:application/json"); */

/*create new user; 
$url = "http://127.0.0.1:5000/v3/users";
$OS_TOKEN = "38fe279241ee7a1e470098d50bd4dda3";
$body = array("user"=>array("name"=> "j", "password"=> "k"));
$content_type = array("X-Auth-Token:38fe279241ee7a1e470098d50bd4dda3","Content-Type:application/json"); */
//$res=$obj->http_request($url,$body,1,$content_type);
echo "<pre>";
//print_r($res);
?>
</body>
</html>>
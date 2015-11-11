<html>
<body>
<?php 

require 'vendor/autoload.php';

include ('keystone_wrapper.php');
include ('cassandra_wrapper.php');


class Register{
	
	private $log;
	private $userId;
	private $FirstName;
	private $LastName;
	private $gender;
	private $phone;
	private $email;
	private $passwd;
	private $cost=10;
	private $registrationurl = "http://localhost:5000/v3/users";
	
	private $authurl='http://localhost:5000/v3/auth/tokens';
	
	private $content_type = array("X-Auth-Token:38fe279241ee7a1e470098d50bd4dda3",
			"Content-Type:application/json");
	
	
	public function __construct(){
		$this->log = new Katzgrau\KLogger\Logger(__DIR__.'/logs');
		
	} 
	
	private function generateSalt(){
		$salt = substr(base64_encode(openssl_random_pseudo_bytes(17)),0,22);
		$salt=str_replace("+",".",$salt);
		$finalSalt='$'.implode('$',array("2y",str_pad($this->cost,2,"0",STR_PAD_LEFT),$salt));
		return $finalSalt;
	}
	
	private function generate_httpbody($user,$passwd){
		//$body = array("user"=>array("name"=> $user, "password"=> $passwd));
		$json_str='{"user": {"name":"'. $user . '", "password": "' . $passwd. '"}}';
		$body=json_decode($json_str,true); 
		return $body;
	}
	
	private function generate_token($user, $passwd){
		$json_str='{"auth":{"identity":{"methods":["password"],"password":{"user":{"name":"'.$user.'",
			"domain":{"id":"default"},"password":"'.$passwd.'"}}}}}';
		$body = json_decode($json_str,TRUE);
		
		$identity_service = new keystone(); //1-POST,0-GET
		$result = $identity_service->http_request($this->authurl,$body,1,$this->content_type);
		return $result;
	}
	
	private function validateRegistration(){
		if($_POST["FirstName"]=='')
			return FALSE;
		if($_POST["LastName"]=='')
			return FALSE;
		if($_POST["phone"]=='')
			return FALSE;
		if($_POST["email"]=='')
			return FALSE;
		if($_POST["passwd"]=='')
			return FALSE;
		
		//If all the above parameters are non null
		return TRUE;
	}
	
	public function doRegistration(){
		session_start();
		
		if(! $this->validateRegistration()){
			session_abort();
			echo "Registration is incomplete. Please fill all the fields for successful registration.";
		}
		$this->FirstName = $_POST["FirstName"];
		$this->LastName = $_POST["LastName"];
		$this->phone = $_POST["phone"];
		$this->email = $_POST["email"];
		$this->log->debug(" Welcome {$this->FirstName}, LastName:{$_POST["LastName"]}, 
				Phone no:{$_POST["phone"]} passwd:{$_POST["passwd"]}");
		
		//Create the user in keystone.
		$salt = $this->generateSalt();
		$password = crypt($_POST["passwd"],$salt);
		$body = $this->generate_httpbody($this->email, $password);
		$identity_service = new keystone(); //1-POST,0-GET
		$result = $identity_service->http_request($this->registrationurl,$body,1,$this->content_type);
		/* echo "<pre>";
		print_r($result); */
		if($result['error']!=''){
			$this->log->error("Registration failed in keystone");
			$this->log->error(json_encode(array('error'=>(array('code'=>$result['error']['code'],
					'title'=>$result["error"]["title"],
					'message'=>$result["error"]["message"]
			))), true ));
			echo json_encode(array('error'=>(array('code'=>$result['error']['code'],
					'title'=>$result["error"]["title"],
					'message'=>"Registration failed in keystone!"
			))), true );
				
			session_abort();
			return ;
		}
		
		//Make an entry into Credentials table of cassandra DB.
		$this->userId = $result['user']['id'];
		
		$columns = "( userid, email , firstname , gender , lastname , phone , salt )";
		$values = "( '{$this->userId}' , '{$this->email}', '{$this->FirstName}', 
				'x', '{$this->LastName}', '{$this->phone}','{$salt}')";
		$this->log->debug($columns);
		$this->log->debug($values);
		
		$cassandra = new cassandra_wrapper();
		$query = $cassandra->buildQuery_insert("credentials", $columns, $values);
		$db = $cassandra->execute($query);
		$this->log->info("Registration successful for user:{$this->email} with id:{$this->userId}");
		
		//Generate token for login the just registered user.
		$token = $this->generate_token($this->email, $password);
		
		if($token['error']!=''){
			$this->log->error("Keystone token generation for {$this->email} failed while logging in.");
			$this->log->error(json_encode(array('error'=>(array('code'=>$token['error']['code'],
					'title'=>$token["error"]["title"],
					'message'=>$token["error"]["message"]
			))), true ));
			echo json_encode(array('error'=>(array('code'=>$token['error']['code'],
					'title'=>$token["error"]["title"],
					'message'=>"Keystone token generation for {$this->email} failed while logging in"
			))), true );
		
			session_abort();
			return ;
		}
		$id = $token['token']['user']['id'];
		$subject_token = $token['header']['X-Subject-Token'];
		
		$this->log->info("userid:{$id} has logged in with the subject token:{$subject_token}");
		$this->log->debug(json_encode( array('success'=>array('userid'=>$id,'token'=>$subject_token)), true ));
		
		//Redirect the user to his profile page.
		if($id!=null && $subject_token!=null){
			$_SESSION['userid']=$id;
			$_SESSION['token']=$subject_token;
			header('Location: http://localhost/knitpeer-UX/profile_page.html');
		}
	}
	
	
}

/* $_POST["FirstName"]="admin";
$_POST["LastName"]="service";
$_POST["phone"]='1234556';
$_POST["email"]="admin@mail.com";
$_POST["passwd"]="admin_pass"; */

$obj=new Register();
$obj->doRegistration();
?>
</body>
</html>
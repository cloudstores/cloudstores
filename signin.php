<?php

require 'vendor/autoload.php';

include ('keystone_wrapper.php');
include ('cassandra_wrapper.php');

class SignIn{
	private $log;
	private $email;
	private $authurl='http://localhost:5000/v3/auth/tokens';
	
	private $content_type =array("X-Auth-Token:38fe279241ee7a1e470098d50bd4dda3",
			"Content-Type: application/json");
	
	public function __construct(){
		$this->log = new Katzgrau\KLogger\Logger(__DIR__.'/logs');
	}
	
	private function generate_httpbody($user,$passwd){
		$json_str='{"auth":{"identity":{"methods":["password"],"password":{"user":{"name":"'.$user.'",
			"domain":{"id":"default"},"password":"'.$passwd.'"}}}}}';
		
		$body = json_decode($json_str,TRUE);
		/*$body = array("auth"=> array("identity"=> array("methods"=> ["password"],
				"password"=>array("user"=>array("name"=> $user,
				"domain"=>array( "id"=> "default"),"password"=> $passwd))))); */
		
		return $body;
	}
	
	private function verify_cruds(){
		if($_POST["email"]=='')
			return FALSE;
		if($_POST["passwd"]=='')
			return FALSE;
		
		//If all the above parameters are non null
		return TRUE;
	}
	
	public function doLogin(){
		session_start();
		
		echo "<pre>";
		//$_POST['username']="suhasdheeraskar1988@gmail.com";
		//$_POST['passwd']="jimmygallu";
		
		if(! $this->verify_cruds() ){
			session_abort();
			echo "Login credentials are incomplete";
		}
		
		$this->email = $_POST['username'];
		$this->log->debug(" Welcome {$this->email}");
		
		//Get the user information from cassandra credentials table.
		$db = new cassandra_wrapper();
		$query = $db->buildQuery_select("credentials",'salt',"email='{$this->email}'");
		$this->log->debug("query:{$query}");
		$res = $db->execute($query);
		
		$this->log->debug("userId:{$this->email} salt:{$res[0]['salt']}");
		
		$password = crypt($_POST["passwd"],$res[0]['salt']); 
		$body = $this->generate_httpbody($this->email, $password);
		$this->log->info("passwd:{$password}");
		
		//Validate the user against keystone information for login.
		$identity_service = new keystone(); //1-POST,0-GET
		$result = $identity_service->http_request($this->authurl,$body,1,$this->content_type);
		
		if($result['error']!=''){
			$this->log->error("Credentials doesn't exist in the DB.");
			$this->log->error(json_encode(array('error'=>(array('code'=>$result['error']['code'],
					'title'=>$result["error"]["title"],
					'message'=>$result["error"]["message"]
			))), true ));
			echo json_encode(array('error'=>(array('code'=>$result['error']['code'],
					'title'=>$result["error"]["title"],
					'message'=>"Username and password doesn't match!"
			))), true );
			
			session_abort();
			return ;
		}
		
		$id = $result['token']['user']['id'];
		$subject_token = $result['header']['X-Subject-Token'];
		//print_r($result);
		/*print_r($id);
		print_r($subject_token); */ 
		$this->log->info("userid:{$id} has logged in with the subject token:{$subject_token}");
		$this->log->debug(json_encode( array('success'=>array('userid'=>$id,'token'=>$subject_token)), true ));
		
		//Regirect the user to his profile page
		if($id!=null && $subject_token!=null){
			$_SESSION['userid']=$id;
			$_SESSION['token']=$subject_token;
			header('Location: http://localhost/knitpeer-UX/profile_page.html');
		}
	}
	
	public function invokeLogin(){
		echo '<!DOCTYPE html>';
		echo '<html>';
		echo '<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>';
		echo '<body>';
		echo '<div id="nav">';
		echo '<form action="signin.php" method="post">' ;
		echo '<input type="text" name="username" placeholder="email-id or phone-no"> ';
		echo '<input type="password" name="passwd" placeholder="password"> ';
		echo '<input type="submit" value="sign in"> '; 
		echo '</div>';
		echo '</body>';
		echo '</html>';
		exit(1);
	}
}


$obj=new SignIn();
$obj->doLogin();
?>
<?php
/*
* Business Logic 
* component : signIn
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("dbconfig.php");
	
	class signIn {
		
		// class variable
		private $arrResultData= array();
		private $conexion;
		private $userId;
		private $arr_error_log= array();
		private $debug = false;
		
		/* 
			Description :Function to initialize object
			Function name : consultarIdUsuario
			Input Parasm : NA
			Output       : NA
	     */
		public function __construct() {
		
			$this->arrResultData = array("success" => "true", "m" => array());
			$this->conexion = mysqli_connect(SERVIDOR, USUARIO, CONTRASENA, BASEDATOS) or die (json_encode(array("success" => "false", "m" => "Fail to connect Database at server: " . SERVIDOR)));
        
		} // end of __construct()
		
		/* Function name : registerUser
		*  Date : 16th Jan 2014
		*  Description : Register a new user
		*  Inpur params : user data array
        *  Out Put : True || False		
		*/
		private function registerUser($arrData) {
			
			//$arrData= $arrData;
			
			/* below code is to check user is already exist or not */
			//$check_query = "SELECT * from usr where user_name='".$arrData['user']."' AND password='".$arrData['passwd']."'";
			$check_query = "SELECT * from usr where user_name='".$arrData['user']."'";
			
			$result_check = mysqli_query($this->conexion, $check_query) or die (json_encode(array("success" => "false", "m" => "Error al ejecutar el query: " . $check_query)));
			
			$row_check = mysqli_fetch_array($result_check, MYSQLI_ASSOC);
			
			if(count($row_check)>0 && !empty($row_check)) {
				$this->arrResultData['success'] = 'false';
			    $this->arrResultData['m'] = 'Email or User name already exist.';
				if($this->debug)
					$this->arrResultData['debug'][] = array("user name"=>"Email or User name already exist.");
				
				return FALSE;
			}
			else { 
			
				//first to insert the record in user table
				$query = "INSERT INTO `usr`(`id`, `name`, `age`, `gender`, `user_name`, `password`, `account_type_id`,`registerDate`,`is_non_register`) VALUES (0,'".$arrData['name']."',".$arrData['age'].",'".$arrData['gender']."','".$arrData['user']."','".$arrData['passwd']."','".$arrData['account_type']."','".date('Y-m-d H:i:s')."',0)";
								
				$result = mysqli_query($this->conexion, $query) or die (json_encode(array("success" => "false", "m" => "Error al ejecutar el query: " . $query)));
				
				if($result) 
					return TRUE;
				else
				    return FALSE;
			}	
		} //end of registerUser
		
		public function validate_account_type($id)
		{
			$query = "SELECT name FROM  accounttype WHERE  id = @I";
            $query = str_replace("@I", $id, $query);
			$result = mysqli_query($this->conexion, $query) or die (json_encode(array("success" => "false", "m" => "query error: " . $query)));
			$arrData = mysqli_fetch_array($result, MYSQLI_ASSOC);
			if (empty($arrData))
                 return FALSE;
            else
				return $arrData;
            
        } //end of validate_account_type
		
		
		public function validarEstructura($jsonEntrada) {
			
			$json = $jsonEntrada['LOG'];
			$result = array();
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			if($this->debug)
				$this->arrResultData['debug'][] = array("send query"=>$json);
			
			if($this->validateData($json)){
				$result = $this->registerUser($json);
				if($result){
					if(empty($this->arrResultData['m']))
						$this->arrResultData['m']= "User Register successfully!";
					if($this->debug)
						$this->arrResultData['debug'][] = array("registration"=>"success");
				}
				else{
					$this->arrResultData['success'] = 'false';
					if(empty($this->arrResultData['m']))
						$this->arrResultData['m'] = 'Record inserted fail!!';
					if($this->debug)
						$this->arrResultData['debug'][] = array("registration"=>"fail");
				}
			}
			return $this->arrResultData;
		} //end of validarEstructura()
		
		function validateData($json) {
			
			$flag = 0;
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			
			// check user index
			if(!array_key_exists("user",$json) || empty($json['user'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'user name is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"User name is missing or Invalid index");
					$flag=1;
			}else
			{
				if(isset($json['user']) && strlen($json['user']) > 30) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'user name mast be less than 30 character';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"length of user name is exceed");
					$flag=1;
				}
				if(is_integer($json["user"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid user';
					if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"user mast be string format");
					$flag=1;
				}
				/*if (isset($json["LOG"]["user"]) && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $json["LOG"]["user"]))
				{
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'user name mush be an email ID';
					if($this->debug)
						$this->arrResultData['debug'][] = array("name"=>"user name mush be an email ID");
				}*/
			}
			 
			// check passwd index
			if(!array_key_exists("passwd",$json) || empty($json['passwd'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'password is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("password"=>"password is missing or Invalid index");
					$flag=1;
			}
			else
			{
				if(isset($json['passwd']) && strlen($json['passwd']) > 30) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'password mast be less than 30 character';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("password"=>"length of password is exceed");
					$flag=1;
				}
				if(is_integer($json["passwd"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid password';
					if($this->debug)
						$this->arrResultData['debug'][] = array("passwd"=>"password mast be string format");
					$flag=1;
				}
			
			}
			
			
			// first name validation 
			if ( !array_key_exists("name", $json) || empty($json["name"]))
			{
				$this->arrResultData['success'] = 'false';
			    $this->arrResultData['m'] = 'Please enter the user name';
				if($this->debug)
					$this->arrResultData['debug'][] = array("name"=>"User name is missing");
					$flag=1;
			}else
			{
				if(isset($json['name']) && strlen($json['name']) > 30) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'nick name mast be less than 30 character';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("name"=>"length of name or nickname is exceed");
					$flag=1;
				}
				if(is_integer($json["name"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid name';
					if($this->debug)
						$this->arrResultData['debug'][] = array("name"=>"name mast be string format");
					$flag=1;
				}
			}
			
			// age validation
			if ( !array_key_exists("age", $json) || empty($json["age"]))
			{
				$this->arrResultData['success'] = 'false';
			    $this->arrResultData['m'] = 'Please enter the user age';
				if($this->debug)
					$this->arrResultData['debug'][] = array("age"=>"user age is missing");
					$flag=1;
			}else
			  { 
				if(!is_integer($json["age"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Use age should be numeric format';
					if($this->debug)
						$this->arrResultData['debug'][] = array("age"=>"Use age should be numeric format");
					$flag=1;
				}
			}
			
			// gender validation
			if ( !array_key_exists("gender", $json) || empty($json["gender"]))
			{
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Please enter the user gender';
					if($this->debug)
						$this->arrResultData['debug'][] = array("gender"=>"Please enter the user gender");
					$flag=1;
			}else {
				if (!in_array($json["gender"], array('M','F')))
				{
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Please enter the user correct gender';
						if($this->debug)
							$this->arrResultData['debug'][] = array("gender"=>"Please enter the user correct gender");
					$flag=1;
				}
			}
			
			 // valid account_type
			if (!array_key_exists("account_type", $json) || empty($json["account_type"]))
			{
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Account Type is missing';
					if($this->debug)
						$this->arrResultData['debug'][] = array("account_type"=>"Please enter the account_type");
					$flag=1;
			} else {
			 
				if(!is_integer($json["account_type"])) 
				{
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'account_type should be number format';
					if($this->debug)
						$this->arrResultData['debug'][] = array("account_type"=>"account_type should be number format");
					$flag=1;
				} else {
					if (!$this->validate_account_type($json["account_type"]))
					{
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'invalid account_type';
						if($this->debug)
						$this->arrResultData['debug'][] = array("account_type"=>"invalid account_type");
						$flag=1;
					}
				}
			}
			
			if($flag) { return false; } else { return true;}
		}
		
		
	} // end of class()
?>

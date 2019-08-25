<?php
/*
* Business Logic 
* component : delRou
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("dbconfig.php");
    require("logIn.php");
	
	class delRou{
	
		// class variable
		private $arrResultData= array();
		private $conexion;
		private $userId;
		private $arr_error_log = array();
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
			$this->Obj_logIn = new LogIn();
        
		} // end of __construct()
		
		function exec_query($sql_query,$flag) {
			$arrAllData = array();
			$result = mysqli_query($this->conexion, $sql_query) or die (json_encode(array("success" => "false", "m" => "record not inserted. " . $sql_query)));
			
			switch($flag) {
				case 'INS':
					if($result) { return mysqli_insert_id($this->conexion); }
				break;
				case 'SEL':
					$arrData = mysqli_fetch_array($result, MYSQLI_ASSOC);
					return $arrData;
				break;
				case 'ALL' :
					 while($row = mysqli_fetch_array($result, MYSQLI_ASSOC) ) {
							$arrAllData[] = $row;
					 }
					 return $arrAllData;
				break;
				case 'DEL':
				
				break;
			}
		}	
		
		function deleteUserRoute($json) {
			$sql_route = "SELECT `id`, `idUsr`, `route_name`, `active` FROM `usrroutes` WHERE id=".$json['route_id']." AND  idUsr=".$this->userId;
			$route = $this->exec_query($sql_route,'SEL');
			if(empty($route)) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Invalid route';
			   if($this->debug)
					$this->arrResultData['debug'][] = array("user"=>"route is not present in Database");
				
				return FALSE;
			}else
			{
				//de-active user route
				$sql_update = "UPDATE `usrroutes` SET `active`=0 WHERE id=".$json['route_id']." AND  idUsr=".$this->userId;
			    $route = $this->exec_query($sql_update,'DEL');
				return TRUE;
			}
		}
		
		/*
		* Function to validata the request data
		*/
		
		public function validarEstructura($jsonEntrada) {
		
			$json = $jsonEntrada['ROU'];
			$arrResult = array();
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			if($this->debug)
				$this->arrResultData['debug'][] = array("send query"=>$json);
				
			if($this->validateData($json)) {
				$arrResult = $this->Obj_logIn->authenticateUser($json['user'],$json['passwd']);
					if(!empty($arrResult)){
						$this->userId = $arrResult['id'];
						if($this->deleteUserRoute($json)){
							   $this->arrResultData['success'] = 'true';
							   $this->arrResultData['m'] = 'route remove successfully.'; 
						}
					}
					else{
					    $this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'Invalid user or password';
						if($this->debug)
							$this->arrResultData['debug'][] = array("athentication"=>"fail");
					}
			}
			return $this->arrResultData;
		} // end of validarEstructura()
		
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
			}
			 
			 // check passwd index
			 if(!array_key_exists("passwd",$json) || empty($json['passwd'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'passwd is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("passwd"=>"passwd is missing or Invalid index");
					$flag=1;
			 }else
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
						$this->arrResultData['debug'][] = array("password"=>"password mast be string format");
					$flag=1;
				}
			
			}
			 
			   // check route_id index
			 if(!array_key_exists("route_id",$json) || empty($json['route_id'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'route_id is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("route_id"=>"route_id is missing or Invalid index");
					$flag=1;
			 }else
			 {
				if(isset($json['route_id'])  && !is_integer($json['route_id'])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Route id must be numeric';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("route_id"=>"Route id must be numeric");
					$flag=1;
				
				}
			 }
			
			 if($flag) { return false; } else { return true;}
			
		} // end of validateData()
			
	} // end of class()
?>
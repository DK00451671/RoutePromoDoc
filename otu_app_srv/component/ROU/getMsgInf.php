<?php
/*
* Business Logic 
* component : getMsgInf
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	class getMsgInf{
	
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
		
		public function getMessage($json) {
			$msg_query = "SELECT rmsg.id, rmsg.message AS msg, routeusage.idUsrRoute AS rouId, routeusage.idRoutePoint AS rouPnt, routeusage.time, routeusage.idUsr AS idUsr FROM routemessages AS rmsg JOIN routeusage ON ( routeusage.id = rmsg.idRouteUsage ) WHERE rmsg.id =".$json["msg_id"];
			$msg = $this->exec_query($msg_query,'SEL');
			
			if(empty($msg)) {
                // error msg do not exist
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = "msg_id not exist";
                return false;
           }else {
                // return msg
				$this->arrResultData['success'] = 'true';
				$this->arrResultData['m'] = $msg;
				$this->arrResultData['m']["id"] = (int)$this->arrResultData['m']["id"];
				$this->arrResultData['m']["rouId"] = (int)$this->arrResultData['m']["rouId"];
				$this->arrResultData['m']["rouPnt"] = (int)$this->arrResultData['m']["rouPnt"];
				$this->arrResultData['m']["idUsr"] = (int)$this->arrResultData['m']["idUsr"];
                return true;
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
			
				$Obj_logIn= new LogIn();
				$arrResult = $Obj_logIn->authenticateUser($json['user'],$json['passwd']);
				
				if(!empty($arrResult) && !$arrResult['is_non_register']) {
					$this->userId = $arrResult['id'];
						$this->getMessage($json);
				}
				else {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid user or password';
					if($this->debug)
						$this->arrResultData['debug'][] = array("athentication"=>"fail");
				}
			}	
			return $this->arrResultData;			
		}
		
		public function validateData($json) {
		
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
						$this->arrResultData['m'] = 'invalid user name';
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
						$this->arrResultData['debug'][] = array("user"=>"passwd is missing or Invalid index");
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
						$this->arrResultData['m'] = 'invalid passwd';
						if($this->debug)
							$this->arrResultData['debug'][] = array("password"=>"password mast be string format");
						$flag=1;
					}
				
				}
				
				  // check msg_id index
			 if(!array_key_exists("msg_id",$json) || empty($json['msg_id'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'msg_id is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("msg_id"=>"msg_id is missing or Invalid index");
					$flag=1;
			 } else {
				
				if(!is_integer($json["msg_id"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid msg_id';
					if($this->debug)
						$this->arrResultData['debug'][] = array("msg_id"=>"route id must be numeric format");
					$flag=1;
				}
			}
			if($flag) { return false; } else { return true;}
			
		} //end of validateData()
	}
?>

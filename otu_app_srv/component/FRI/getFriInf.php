<?php
/*
* Business Logic 
* component : getFriInf
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	
	class getFriInf{
		// class variable
		private $arrResultData= array();
		private $conexion;
		private $conexion_0;
		private $userId;
		private $debug = false;
		private $Obj_logIn = NULL;
		private $arrUserId = array();
		
		/* 
			Description :Function to initialize object
			Function name : consultarIdUsuario
			Input Parasm : NA
			Output       : NA
	     */
		public function __construct() {
		
			$this->arrResultData = array("success" => "true", "m" => array());
			
			//connection for database_1
			$this->conexion = mysqli_connect(SERVIDOR, USUARIO, CONTRASENA, BASEDATOS) or die (json_encode(array("success" => "false", "m" => "Fail to connect Database at server: " . SERVIDOR)));
			
			$this->userId =0;
			
        
		} // end of __construct()
		
		/* 
			Description :Function to execuate Queries for  database_1
			Function name : exec_query
			Input Parasm : sql_query , flag= options
			Output       : Query result
	     */
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
		
		public function getFriendInformation($json) {
		   
		   $arrData = array();
		   //Check friend request present or not
		   $sql_check = "SELECT  DATEDIFF(NOW(), date) as since , `accepted`, `idUsr`, `idFriend`, `valid` FROM `friends` WHERE ( (`idUsr`=".$this->userId." AND `idFriend`=".$json['user_id'].") OR ( `idFriend`=".$this->userId." AND `idUsr`=".$json['user_id'].")) AND `accepted` = 1 AND  `valid`=1";
		   $arrChkData = $this->exec_query($sql_check,'SEL');
		 
		    if(!empty($arrChkData)) {

				$arrData['since'] = (int)$arrChkData['since'];
				
				// get shared route with requested user 
				$sql_shared_route = "SELECT urou.id,urou.route_name FROM `sharedroutes` as shrou , usrroutes as urou WHERE shrou.`idUsr`=".$this->userId." AND shrou.`idUsr_ToShareRouteWith`=".$json['user_id']." AND urou.id=shrou.idUsrRoutes";
				
				$arrShareData = $this->exec_query($sql_shared_route,'ALL');
				if(!empty($arrShareData)) {
					foreach($arrShareData as $kk=>$val) {
						$arrData['rou_shared'][$arrShareData[$kk]['id']] = $arrShareData[$kk]['route_name'];
					}
				}
			   else
			      $arrData['rou_shared'] = array();
			
				$sql_shared_with_me = "SELECT urou.id,urou.route_name FROM `sharedroutes` as shrou , usrroutes as urou WHERE shrou.`idUsr_ToShareRouteWith`=".$this->userId." AND shrou.`idUsr`=".$json['user_id']." AND urou.id=shrou.idUsrRoutes";
			
				$arrSharewithmeData = $this->exec_query($sql_shared_with_me,'ALL');
			
				if(!empty($arrSharewithmeData)) {
					foreach($arrSharewithmeData as $kk=>$val) {
						$arrData['rou_shared_with_me'][$arrSharewithmeData[$kk]['id']] = $arrSharewithmeData[$kk]['route_name'];
					}
				}
				else
				$arrData['rou_shared_with_me'] = array();
			
			$this->arrResultData['m'] = $arrData;
		  }
		  else {
				// friend request not present.
			    $this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'invalid friend request.';
			}
		    return TRUE;
		}
		
		/*
		* Function to validata the request data
		*/
		
		public function validarEstructura($jsonEntrada) {
		
			$json = $jsonEntrada['FRI'];
			$arrResult = array();
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			if($this->debug)
				$this->arrResultData['debug'][] = array("send query"=>$json);
			if($this->validateData($json)) {
			
				$Obj_logIn= new LogIn();
				$arrResult = $Obj_logIn->authenticateUser($json['user'],$json['passwd']);
				if(!empty($arrResult) && !$arrResult['is_non_register']) {
					$this->userId = $arrResult['id'];
						$this->getFriendInformation($json);
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
				
				  // check user_id index
			 if(!array_key_exists("user_id",$json) || empty($json['user_id'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'user_id is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("user_id"=>"user id is missing or Invalid index");
					$flag=1;
			 } else {
				
				$sql_userId = "SELECT `id`  FROM `usr` WHERE id=".$json["user_id"];
				$arrUserData = $this->exec_query($sql_userId,'SEL');
				if(!is_integer($json["user_id"]) || (empty($arrUserData) && !isset($arrUserData['id']))) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid friend id';
					if($this->debug)
						$this->arrResultData['debug'][] = array("user_id"=>"user id must be numeric format");
					$flag=1;
				}
			}
			if($flag) { return false; } else { return true;}
			
		} //end of validateData()
	}

?>
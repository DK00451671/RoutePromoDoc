<?php
/*
* Business Logic 
* component : addToMyRouLst
*/
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	
	class addToMyRouLst{
		
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
			$result = mysqli_query($this->conexion, $sql_query) or die (json_encode(array("success" => "false", "m" => "invalid query " . $sql_query)));
			
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
		
		function addToNewSharedRoute($json) {
			
			
			$arrData = array();
			$tempData = array();
			$sql_token = "SELECT `id`, `idUsrRoutes`, `idUsr`, `idUsr_ToShareRouteWith`, `idAccountType`, `route_owner`, `date`, `token`, `accepted`, `valid` FROM `sharedroutes` WHERE `token`= '".$json['token']."'";
			$arrToken = $this->exec_query($sql_token,'SEL') ;
			
			if(empty($arrToken) && !isset($arrToken['id']) ) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Invalid Token';
				return false;
			}
			if( !empty($arrToken['idUsr_ToShareRouteWith']) && !empty($arrToken['route_owner']) ){
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'route is not public';
				return false;
			}
			else {
			
			    $sql_sel  = "SELECT `id`, `idUsrRoutes`, `idUsr`, `idUsr_ToShareRouteWith`, `idAccountType`, `route_owner`, `date`, `token`, `accepted`, `valid` FROM `sharedroutes` WHERE `route_owner`=".$arrToken['id']."  AND  `idUsrRoutes`=".$arrToken['idUsrRoutes'];
				$arrCheck = $this->exec_query($sql_sel,'SEL');
				if(empty($arrCheck)) {
							
						$tempData['idUsrRoutes']  = $arrToken['idUsrRoutes'];
						$tempData['idUsr']  = $arrToken['idUsr'];
						$tempData['idUsr_ToShareRouteWith']  = $this->userId;
						$tempData['route_owner'] = $arrToken['id'];
						$tempData['date'] = date('Y-m-d H:i:s');
						$tempData['accepted'] = 1;
						$tempData['valid'] = 1;
								
						
						$sql_ins = "INSERT INTO `sharedroutes`(`id`, `idUsrRoutes`, `idUsr`, `idUsr_ToShareRouteWith`, `route_owner`, `date`, `accepted`, `valid`) VALUES (0,".$tempData['idUsrRoutes'].",".$tempData['idUsr'].",".$tempData['idUsr_ToShareRouteWith'].",".$tempData['route_owner'].",'".$tempData['date']."',".$tempData['accepted'].",".$tempData['valid'].")";
						
						
						if($this->exec_query($sql_ins,'INS')){
						   $this->arrResultData['m']['route_id'] = $tempData['idUsrRoutes'];
						   return true;
						 }
						else
						   return false;
				 }else {
					$this->arrResultData['m']['route_id'] = $arrToken['idUsrRoutes'];
				}
			}
			
		
		} // end of addToNewSharedRoute()
		
		
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
					if(!empty($arrResult)){
						$this->userId = $arrResult['id'];
						$this->addToNewSharedRoute($json);
					}
					else{
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'Invalid user or password';
						if($this->debug)
							$this->arrResultData['debug'][] = array("athentication"=>"fail");
					}
			}
			return 	$this->arrResultData;		
		}
		
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
						$this->arrResultData['debug'][] = array("passwd"=>"password is missing or Invalid index");
					$flag=1;
			 }
			 else {
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
			 
			  // check token index
			 if(!array_key_exists("token",$json) || empty($json['token'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'token is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("token"=>"token is missing or Invalid index");
					$flag=1;
			 }
			
			if($flag) { return false; } else { return true;}
			
	} // end of validateData()
 }
?>

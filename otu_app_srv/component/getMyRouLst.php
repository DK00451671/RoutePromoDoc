<?php
/*
* Business Logic 
* component : getMyRouLst
*/
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: X-Requested-With');
    require("dbconfig.php");
    require("logIn.php");
	
	class getMyRouLst{
		
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
		
		public function getUserRoute($userId){
			
			$route_sql = "SELECT `id`, `route_name`, active FROM `usrroutes` WHERE `idUsr`=".$userId;	
			$arruserResult = $this->exec_query($route_sql,'ALL');
			
					
			$shared_rou_sql = "SELECT rutcnt.shared_count as shared_times, srou.accepted, usr.id as share_route_from_id,usr.user_name as share_route_from, urou.id as route_id ,urou.route_name, srou.date as shared_date FROM `usrroutes` as urou , sharedroutes as srou , usr as usr , routecount as rutcnt WHERE rutcnt.route_id = srou.idUsrRoutes AND urou.id = srou.idUsrRoutes AND srou.idUsr_ToShareRouteWith=".$userId." AND usr.id=srou.idUsr";
			
			$arrShareRouResult = $this->exec_query($shared_rou_sql,'ALL');
			
			foreach($arrShareRouResult as $kk=>$val) {
			  $sql_rou_owner = "SELECT usr.id as owner_id , usr.user_name as owner_name FROM usr as usr , usrroutes as urut WHERE urut.idUsr = usr.id AND urut.id=".$arrShareRouResult[$kk]['route_id'];
				$arrShareRouOwn = $this->exec_query($sql_rou_owner,'SEL');
				$arrShareRouResult[$kk]['owner_id'] = $arrShareRouOwn['owner_id'];
				$arrShareRouResult[$kk]['owner_name'] = $arrShareRouOwn['owner_name'];
			}
			if(is_array($arruserResult) && count($arruserResult)>0) {
				$cntOwnInx = 0;
				foreach($arruserResult as $kk=>$val) {
					$this->arrResultData['m']['own'][$cntOwnInx][$val['route_name']] = $val['id'];
					$this->arrResultData['m']['own'][$cntOwnInx]['active'] = $val['active'];
					$cntOwnInx++;
				}
			}else
				$this->arrResultData['m']['own']= (object)NULL;
			
			if(is_array($arrShareRouResult) && count($arrShareRouResult)>0) {
			  $cntInx = 0;
				foreach($arrShareRouResult as $kk=>$val) {
					$this->arrResultData['m']['shared'][$cntInx]['route_id'] = $val['route_id'];
					$this->arrResultData['m']['shared'][$cntInx]['route_name'] = $val['route_name'];
				    $this->arrResultData['m']['shared'][$cntInx]['owner_id'] = $val['owner_id'];
					$this->arrResultData['m']['shared'][$cntInx]['owner_name'] = $val['owner_name'];
					$this->arrResultData['m']['shared'][$cntInx]['share_route_from_id'] = $val['share_route_from_id'];
					$this->arrResultData['m']['shared'][$cntInx]['share_route_from'] = $val['share_route_from'];
					$this->arrResultData['m']['shared'][$cntInx]['shared_date'] = $val['shared_date'];
					$this->arrResultData['m']['shared'][$cntInx]['shared_times'] = $val['shared_times'];
					$this->arrResultData['m']['shared'][$cntInx]['accepted'] = $val['accepted'];
					
					$cntInx++;
				}
			}else
				$this->arrResultData['m']['shared']= (object)NULL;
			
			if(empty($arruserResult) && empty($arrShareRouResult))
			{
				$this->arrResultData['success'] = 'true';
				$this->arrResultData['m']['own']= (object)NULL;
				$this->arrResultData['m']['shared']= (object)NULL;
				  if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"No route present in database.");
						
				return FALSE;
			}
			else
			    return TRUE;
		} // end of getUserRoute()
		
		
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
						$this->getUserRoute($arrResult['id']);
					}
					else{
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'Invalid user or password';
						if($this->debug)
							$this->arrResultData['debug'][] = array("athentication"=>"fail");
					}
				}
			return $this->arrResultData;
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
			 }else {
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
						$this->arrResultData['debug'][] = array("user"=>"passwd is missing or Invalid index");
					$flag=1;
			 }else {
				if(is_integer($json["passwd"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid password';
					if($this->debug)
						$this->arrResultData['debug'][] = array("password"=>"password mast be string format");
					$flag=1;
				}
			 
			 }
			 if($flag) { return false; } else { return true;}
			 
		} // end of validateData()
	}
?>

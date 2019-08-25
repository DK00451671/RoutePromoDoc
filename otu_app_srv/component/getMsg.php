<?php
/*
* Business Logic 
* component : getMsg
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("dbconfig.php");
    require("logIn.php");
	class getMsg{
	
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
		
		public function getRouteMessage($json) {
			$is_own = 0;
			$userIds = array();
			$arrData = array();
			$compare_date = $json['limit_date'].'00:00:00';
			$select_route = "SELECT count(rpt.id) as on_of_points, urut.`id`, urut.`idUsr`, urut.`route_name`, `active` FROM `usrroutes` as urut , routepoint as rpt WHERE urut.id=".$json['route_id']." AND urut.idUsr =".$this->userId." AND urut.id=rpt.idUserroutes" ;
			$arrSelect_route = $this->exec_query($select_route,'SEL');
			
			if(empty($arrSelect_route['idUsr'])) {
				$select_share = "SELECT `id`, `idUsrRoutes`, `idUsr`, `idUsr_ToShareRouteWith`, `route_owner`, `date`, `accepted` FROM `sharedroutes` WHERE `idUsr_ToShareRouteWith`=".$this->userId." AND `idUsrRoutes`=".$json['route_id'];
				$arrSelect_route = $this->exec_query($select_share,'SEL');
				$is_own = 0;
			}else {
				$is_own = 1;
				
				$select_share_temp = "SELECT `id`, `idUsrRoutes`, `idUsr`, `idUsr_ToShareRouteWith`, `route_owner`, `date`, `accepted` FROM `sharedroutes` WHERE `idUsr`=".$this->userId." AND `idUsrRoutes`=".$json['route_id'];
				
				$arrSelect_route1 = $this->exec_query($select_share_temp,'SEL');
				if(count($arrSelect_route1) > 0) { 
					$is_own = 0;
				}
			}
			
			if(!empty($arrSelect_route['idUsr']) && is_array($arrSelect_route) && !empty($arrSelect_route)) { 
				if($is_own) {
				    // own route 
					$sql_is_own = "SELECT  rusg.idRoutePoint, rmsg.message,rusg.idUsrRoute,rusg.idUsr,rusg.time FROM `routemessages` as rmsg, routeusage as rusg WHERE rmsg.idRouteUsage=rusg.id AND rusg.idUsrRoute = ".$json['route_id']." AND  rusg.idUsr = ".$this->userId."  AND rusg.time <= '".$compare_date."' Order by rusg.idRoutePoint ";
					
				}
				else {
					//shared route
					$sql_is_own ="SELECT rusg.idRoutePoint, rmsg.message,rusg.idUsrRoute,rusg.idUsr,rusg.time FROM `routemessages` as rmsg, routeusage as rusg WHERE rmsg.idRouteUsage=rusg.id AND rusg.idUsrRoute =".$json['route_id']." AND rusg.time <= '".$compare_date."' Order by rusg.idRoutePoint";
				}
				
				$arrSelect_route = $this->exec_query($sql_is_own,'ALL');
				if(!empty($arrSelect_route) && (count($arrSelect_route)> 0)) {
					/*foreach($arrSelect_route as $kk=>$val) {
					  $userIds[] = $arrSelect_route[$kk]['idUsr'];
					}
					$userIds = array_unique($userIds);
					foreach($userIds as $kk => $userid) { 
						foreach($arrSelect_route as $kk=>$val) {
							$arrData[$userid][$arrSelect_route[$kk]['idRoutePoint']][] = array($arrSelect_route[$kk]['message'],strtotime($arrSelect_route[$kk]['time']));
					  }
					}*/
					foreach($arrSelect_route as $kk=>$val) {
						$arrData[$arrSelect_route[$kk]['idUsr']][$arrSelect_route[$kk]['idRoutePoint']][] = array($arrSelect_route[$kk]['message'],strtotime($arrSelect_route[$kk]['time']));
					  }
					$this->arrResultData['m'] = $arrData;
				}else {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'route message not fouund.';
				}
				return true;
			}else {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Invalid Route';
				if($this->debug)
					$this->arrResultData['debug'][] = array("route_id"=>"Invalid Route. Route is not belongs to user");
				return false;
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
						$this->getRouteMessage($json);
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
				
				  // check route_id index
			 if(!array_key_exists("route_id",$json) || empty($json['route_id'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'route_id is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("route_id"=>"route_id is missing or Invalid index");
					$flag=1;
			 } else {
				
				if(!is_integer($json["route_id"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid route_id';
					if($this->debug)
						$this->arrResultData['debug'][] = array("route_id"=>"route id must be numeric format");
					$flag=1;
				}
			}
			if($flag) { return false; } else { return true;}
			
		} //end of validateData()
	}
?>
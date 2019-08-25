<?php
/*
* Business Logic 
* component : reporteVent
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	class sendRou{
	
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
		public function sendRoute($json) {
			$start_time = '';
			$pointCnt = 0;
			$end_time = '';
			$messageCnt=0;
			$no_of_co_oridinates = count($json['co-ordinates']);
		    
			// logic to check travel mode IDS
			$arrTravelIds = array();
			$input_travel_mode = array();
			$sql_travel = "SELECT `id` FROM `travelmode` WHERE 1";
			$arrTravel = $this->exec_query($sql_travel,'ALL');
			foreach($arrTravel as $kk=>$val) { $arrTravelIds[$kk] = $val['id'];}
			foreach($json['co-ordinates'] as $kk=>$val) { $input_travel_mode[]= $val[1];}
			$input_travelIds = array_unique($input_travel_mode);
			foreach($input_travelIds as $kk=>$val) {
				if(!in_array($val, $arrTravelIds)) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid Travel mode.';
					return false;
				}
			}
			
			$select_route = "SELECT count(rpt.id) as on_of_points, urut.`id`, urut.`idUsr`, urut.`route_name`, `active` FROM `usrroutes` as urut , routepoint as rpt WHERE urut.id=".$json['route_id']." AND urut.idUsr =".$this->userId." AND urut.id=rpt.idUserroutes" ;
			$arrSelect_route = $this->exec_query($select_route,'SEL');
			
			if(empty($arrSelect_route['idUsr'])) {
				$select_share = "SELECT `id`, `idUsrRoutes`, `idUsr`, `idUsr_ToShareRouteWith`, `route_owner`, `date`, `accepted` FROM `sharedroutes` WHERE `idUsr_ToShareRouteWith`=".$this->userId." AND `idUsrRoutes`=".$json['route_id'];
				$arrSelect_route = $this->exec_query($select_share,'SEL');
			}
			if(!empty($arrSelect_route['idUsr']) && is_array($arrSelect_route) && !empty($arrSelect_route)) {
				foreach($json['co-ordinates'] as $kk=>$val) {
				$time_span    = $val[0];
				$travel_mode  = $val[1];
				$point_mesg   = isset($val[2]) ? $val[2] : ''; 
				
				/*$travel_mode  = $val[0];
				$point_mesg   = isset($val[1]) ? $val[1] : '';*/
							
					//$sql_seg_id = "SELECT rpt.id,rtusg.idRoutePoint, rtusg.time,rpt.route_message  FROM routepoint as rpt, routeusage as rtusg where rtusg.idTravelMode=".$travel_mode." AND rtusg.idUsrRoute=".$json['route_id']." AND rpt.id= rtusg.idRoutePoint AND rpt.id=".$kk." AND rpt.route_message='".$point_mesg."'";
									
					$sql_seg_id = "SELECT rpt.id FROM routepoint as rpt where  rpt.id=".$kk." AND  idUserroutes=".$json['route_id'] ;
					
					$segment_array = $this->exec_query($sql_seg_id,'SEL');
					
					if(empty($segment_array)) {
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'Invalid Routes co-ordinates';
						return false;
					} 
					$pointCnt++;
					if($pointCnt==1)
						$start_time = date('Y-m-d H:i:s',$val[0]);
					if($pointCnt == $no_of_co_oridinates) 
						$end_time = date('Y-m-d H:i:s',$val[0]);
					
					$sql_routeusages = "INSERT INTO `routeusage`(`id`, `idRoutePoint`, `idUsrRoute`, `idUsr`,`idTravelMode`, `time`) VALUES (0,".$segment_array['id'].",".$json['route_id'].",".$this->userId.",".$val[1].",'".date('Y-m-d H:i:s',$val[0])."')";
							
					$id_route_usages  = $this->exec_query($sql_routeusages,'INS');	
					
					if(isset($point_mesg) && !empty($point_mesg)) {
						$messageCnt++;
						//add logic to insert the route messages
						$sql_message = "INSERT INTO `routemessages`(`id`, `message`,`idRouteUsage`) VALUES (0,'".$point_mesg."',".$id_route_usages.")";
						$this->exec_query($sql_message,'INS');
					}
				}
				$sql_route_from_counter = "SELECT `id`, `route_id`, `shared_count`, `usages_count` FROM `routecount` WHERE route_id=".$json['route_id'] ;
				$arrRouteCount = $this->exec_query($sql_route_from_counter,'SEL');
					
				if(count($arrRouteCount)>0) {
					$sql_updated_counter = "UPDATE `routecount` SET `usages_count`=".($arrRouteCount['usages_count']+1)." WHERE route_id=".$json['route_id'] ;
					$this->exec_query($sql_updated_counter,'UPD');
				}
				
				$this->arrResultData['m']['route_id']          = isset($json['route_id']) ? $json['route_id'] : 0;
				$this->arrResultData['m']['no_of_coordinates'] = isset($no_of_co_oridinates) ? $no_of_co_oridinates : 0;
				$this->arrResultData['m']['no_of_message']     = isset($messageCnt) ? $messageCnt : 0;
				$this->arrResultData['m']['start']             = isset($start_time) ? $start_time : 0;
				$this->arrResultData['m']['end']               = isset($end_time) ? $end_time : 0;
				return TRUE;
				
			} else {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Invalid Route.';
				if($this->debug)
					$this->arrResultData['debug'][] = array("route"=>"Route is not associated with user or route point is missing");
				return False;
			}
		} //end of sendRoute()
		
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
						$this->sendRoute($json);
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
				
				
				// check co-ordinates index
		 if(!array_key_exists("co-ordinates",$json) || empty($json['co-ordinates'])) {
			$this->arrResultData['success'] = 'false';
			$this->arrResultData['m'] = 'co-ordinates node is missing';
			   if($this->debug)
					$this->arrResultData['debug'][] = array("user"=>"co-ordinates is missing or Invalid index");
				$flag=1;	
		 }
		 
		 // validate co-ordinates value
		 
		  if(isset($json['co-ordinates']))
		     foreach($json['co-ordinates']as $kk=>$val) {
				// time, travel_mode,route_meassage
				if((!is_integer($val[0]) || !$this->isValidTimeStamp($val[0]) ) || ( 
					isset($val[1]) && !is_integer($val[1]) ) ) {
				
					$flag=1;
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'malformed structure co-ordinates';
					break;
				}
				if(isset($val[2]) && !is_string($val[2])) {
					$flag=1;
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'malformed structure co-ordinates';
					if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"malformed structure co-ordinates. message must be string format.");
				}
				/*if( ( isset($val[0]) && !is_integer($val[0])) || (isset($val[1]) && !is_string($val[1]))) {
					$flag=1;
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'malformed structure co-ordinates';
					break;
				} */
            } 	
			
			if($flag) { return false; } else { return true;}
				
				
		} // end of validateData()
		
		//validate time stamp value
		function isValidTimeStamp($strTimestamp) {
			return (($strTimestamp <= PHP_INT_MAX)
				&& ($strTimestamp >= ~PHP_INT_MAX));
				
		} // end of isValidTimeStamp()
		
	}
		
?>
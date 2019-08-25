<?php
/*
* Business Logic 
* component : reporteVent
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	
	class sendNewRou{
	
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
				case 'UPD':
				
				break;
			}
		}
		
		/* 
			Description   :Function to Insert new route
			Function name : insertRoute
			Input Parasm  : @arrData , @userid
			Output        : return boolean value
	     */
		public function insertRoute($arrData,$user_id){
				
			// to fetch the travelmode Ids
			$arrTravelIds = array();
			$sql_travel = "SELECT `id`, `mode` FROM `travelmode` WHERE 1";
			$arrTravel = $this->exec_query($sql_travel,'ALL');
			foreach($arrTravel as $kk=>$val)
				$arrTravelIds[$kk] = $val['id'];
			
			$sql_check = "SELECT `id`, `idUsr`, `route_name`, `active` FROM `usrroutes` WHERE idUsr=".$user_id." AND route_name = '".$arrData['route_name']."'";
			$arrResp = $this->exec_query($sql_check,'SEL');
			
			
			if(!is_array($arrResp) && count($arrResp) < 1) {
					
				$sql_userroute = "INSERT INTO `usrroutes`(`id`, `idUsr`, `route_name`, `active`, `distance`, `duration` ) VALUES (0,".$user_id.",'".$arrData['route_name']."',1,".$arrData['distance'].",".$arrData['duration'].")";
				$route_user_id = $this->exec_query($sql_userroute,'INS');
				
				$sql_rut_count ="INSERT INTO `routecount`(`id`, `route_id`, `shared_count`, `usages_count`) VALUES (0,".$route_user_id.",0,0)";
				
				$arrRouteCount = $this->exec_query($sql_rut_count,'INS');
				
				if(is_numeric($route_user_id) && $route_user_id > 0) {
					
					foreach($arrData['co-ordinates'] as $kk=>$val) {
						if(in_array($val[3], $arrTravelIds)) {
							$mes =  isset($val[4]) ? $val[4] : '';
							$point = $val[1]." ".$val[2];
							$sql_seg_id ="SELECT segment.id,segment.position FROM segment WHERE MBRContains(segment.value, GeomFromText('Point(".$point.")')) ";
							$segment_array = $this->exec_query($sql_seg_id,'SEL');
							
							//$str = " INSERT INTO `routepoint`(`id`, `idSegment`, `idUserroutes`, `latLng`, `route_message`) VALUES  (0,".$segment_array['id'].",".$route_user_id.",GeomFromWKB(Point(".$val[1].",".$val[2].")),'".$mes."')";
							$str = " INSERT INTO `routepoint`(`id`, `idSegment`, `idUserroutes`, `latLng`) VALUES  (0,".$segment_array['id'].",".$route_user_id.",GeomFromWKB(Point(".$val[1].",".$val[2].")))";
							
							$Route_id = $this->exec_query($str,'INS');
							
							$sql_routeusages = "INSERT INTO `routeusage`(`id`, `idRoutePoint`, `idUsrRoute`,`idUsr`,`idTravelMode`, `time`) VALUES (0,".$Route_id.",".$route_user_id.",".$user_id.",".$val[3].",'".date('Y-m-d H:i:s',$val[0])."')";
							
							$id_route_usages =  $this->exec_query($sql_routeusages,'INS');
							
							
							//add logic to insert the route messages
							if(!empty($mes)) {
							 	$sql_message = "INSERT INTO `routemessages`(`id`, `message`,`idRouteUsage`) VALUES (0,'".$mes."',".$id_route_usages.")";
								$this->exec_query($sql_message,'INS');
							}
						}
						else {
							$delete_query = "DELETE FROM `usrroutes` WHERE `id`=".$route_user_id." and `idUsr` = ".$user_id;
						    $this->exec_query($delete_query,'DEL');
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = 'Invalid Travel mode.';
							return FALSE;
						}
					}
					$sql_route_from_counter = "SELECT `id`, `route_id`, `shared_count`, `usages_count` FROM `routecount` WHERE route_id=".$route_user_id ;
					$arrRouteCount = $this->exec_query($sql_route_from_counter,'SEL');
					if(count($arrRouteCount)>0) {
						$sql_updated_counter = "UPDATE `routecount` SET `usages_count`=".($arrRouteCount['usages_count']+1)." WHERE route_id=".$route_user_id ;
						$this->exec_query($sql_updated_counter,'UPD');
					}
					if(isset($route_user_id) && $route_user_id!="")
						$this->arrResultData['m']['route_id'] = $route_user_id;
					else
					  $this->arrResultData['m']['route_id'] = 'NULL';
					  
					return TRUE;
				}
			}else
			 {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'route name is already exist!';
				return FALSE;
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
			
				if(!empty($arrResult)) {
				    if(!$this->insertRoute($json,$arrResult['id'])) {
					 if(empty($this->arrResultData['m'])) {
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = 'route name is already exist!';
						}
					}
					//else 
						//$this->arrResultData['m'] = 'Route save successfully';
				}
				else
				{
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid user or password';
					if($this->debug)
						$this->arrResultData['debug'][] = array("athentication"=>"fail");
				}
			} 
			return $this->arrResultData;
		}
		
		/* 
		 *  Function to validate the co-ordinates array
		 */
		public  function validateData($json) {
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
					$this->arrResultData['m'] = 'invalid password';
					if($this->debug)
						$this->arrResultData['debug'][] = array("password"=>"password mast be string format");
					$flag=1;
				}
			
			}
		 
		 // check route_name index
		 if(!array_key_exists("route_name",$json) || empty($json['route_name'])) {
			$this->arrResultData['success'] = 'false';
			$this->arrResultData['m'] = 'route_name is missing';
			   if($this->debug)
					$this->arrResultData['debug'][] = array("user"=>"route_name is missing or Invalid index");
				$flag=1;	
		 }else {
				if(is_integer($json["route_name"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid route name';
					if($this->debug)
						$this->arrResultData['debug'][] = array("route_name"=>"route name mast be string format");
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
				// time, lat,lang , travel_mode,route_meassage
				
				if((!is_int($val[0]) || !$this->isValidTimeStamp($val[0]) ) || !is_float($val[1]) || !is_float($val[2]) || !is_int($val[3])) {
					$flag=1;
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'malformed structure co-ordinates';
					break;
				}
				if(isset($val[4]) && !is_string($val[4])) {
					$flag=1;
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'malformed structure co-ordinates';
					if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"malformed structure co-ordinates. message must be string format.");
				}
            }	

          // check distance exist
		 if(!array_key_exists("distance",$json) || empty($json['distance'])) {
			$this->arrResultData['success'] = 'false';
			$this->arrResultData['m'] = 'missing distance';
			   if($this->debug)
					$this->arrResultData['debug'][] = array("user"=>"missing distance");
				$flag=1;	
		 }

           // validate distance is number 
		  if(!is_float($json['distance']) && !is_int($json['distance'])){
			$this->arrResultData['success'] = 'false';
			$this->arrResultData['m'] = 'invalid distance';
			   if($this->debug)
					$this->arrResultData['debug'][] = array("user"=>"distance is not float");
				$flag=1;				
          }	

          // check duration exist
		 if(!array_key_exists("duration",$json) || empty($json['duration'])) {
			$this->arrResultData['success'] = 'false';
			$this->arrResultData['m'] = 'missing duration';
			   if($this->debug)
					$this->arrResultData['debug'][] = array("user"=>"missing duration");
				$flag=1;	
		 }

           // validate duration is number 
		  if(!is_int($json['duration'])){
			$this->arrResultData['success'] = 'false';
			$this->arrResultData['m'] = 'invalid duration';
			   if($this->debug)
					$this->arrResultData['debug'][] = array("user"=>"duration is not int");
				$flag=1;				
            }	
			
			if($flag) { return false; } else { return true;}
			
		} //end of validateData()
		//validate time stamp value
		function isValidTimeStamp($strTimestamp) {
			return (($strTimestamp <= PHP_INT_MAX)
				&& ($strTimestamp >= ~PHP_INT_MAX));
		}
	}
?>

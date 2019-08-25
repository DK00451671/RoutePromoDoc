<?php
/*
* Business Logic 
* component : getMyRouLst
*/
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
    require("lib/LZString.php");
	
	class getRouPnts{
		
		// class variable
		private $arrResultData= array();
		private $conexion;
		private $userId;
		private $arr_error_log= array();
		private $debug = false;
		/* 
			Description :Function to initialize object
			Function name : getRouPnts
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
		
		public function getRouteInfo($json) {
			
			//$sql = "SELECT urut.route_name, travmod.mode, rusge.idTravelMode ,urut.id as route_id ,rpt.id as route_point_id, rpt.route_message, X(rpt.latLng) as lat, Y(rpt.latLng) as lng, AsText(seg.value) as route_point_segment,seg.id as segment_id FROM usrroutes as urut, routepoint as rpt, segment as seg ,  routeusage as rusge, travelmode as travmod WHERE  travmod.id =rusge.idTravelMode AND  rusge.idRoutePoint =rpt.id AND  urut.id =rpt.idUserroutes AND seg.id = rpt.idSegment AND  urut.id=".$json['route_id'];
			$arrData = array();
			$point = array();
			$count = 0;
            $first_id_Route_Point = 0;
			
			$select_route = "SELECT count(rpt.id) as on_of_points, urut.`id`, urut.`idUsr`, urut.`route_name`, `active`, distance, duration FROM `usrroutes` as urut , routepoint as rpt WHERE urut.id=".$json['route_id']." AND urut.idUsr =".$this->userId." AND urut.id=rpt.idUserroutes" ;
			$arrSelect_route = $this->exec_query($select_route,'SEL');
			
			if(empty($arrSelect_route['idUsr'])) {
				$select_share = "SELECT `id`, `idUsrRoutes`, `idUsr`, `idUsr_ToShareRouteWith`, `route_owner`, `date`, `accepted` FROM `sharedroutes` WHERE `idUsr_ToShareRouteWith`=".$this->userId." AND `idUsrRoutes`=".$json['route_id'];
				$arrSelect_route = $this->exec_query($select_share,'SEL');
				//$arrData['is_route']  = array("shared"=>1);
			}//else
				//$arrData['is_route']  = array("own"=>1);
			 
			if(!empty($arrSelect_route['idUsr']) && is_array($arrSelect_route) && !empty($arrSelect_route)) {
			
			    // update for Remove  rpt.route_message,  from query.:: production error --dinesh
				$sql = "SELECT  usr.id as route_owner_id, usr.id as route_owner_id,usr.user_name as route_owner, urut.route_name, urut.id as route_id , urut.distance AS distance, urut.duration AS duration, rpt.id as route_point_id, X(rpt.latLng) as lat, Y(rpt.latLng) as lng,seg.id as segment_id FROM usrroutes as urut, routepoint as rpt, segment as seg,usr as usr WHERE urut.idUsr = usr.id AND urut.id =rpt.idUserroutes AND seg.id = rpt.idSegment AND  urut.id=".$json['route_id'] . " ORDER BY route_point_id ASC ";
				$arrResult  = $this->exec_query($sql,'ALL');

                $arrData['name'] = $arrResult[0]['route_name'];
    			$arrData['owner'] = $arrResult[0]['route_owner'];
				$arrData['owner_id'] = (int)$arrResult[0]['route_owner_id'];
				$arrData['distance'] = $arrResult[0]['distance'];
				$arrData['duration'] = (int)$arrResult[0]['duration'];
                $arrData['creation_date'] = " ";
				$first_id_Route_Point = $arrResult[0]['route_point_id'];

				
				foreach($arrResult as $kk=>$value) {				
				   	if($count==0) {
						$point[] = $arrResult[$kk]['lat'];
						$point[] = $arrResult[$kk]['lng'];
					}
					$count++;
					$arrData['co-ordinates'][$arrResult[$kk]['route_point_id']][] = (float)$arrResult[$kk]['lat'];
					$arrData['co-ordinates'][$arrResult[$kk]['route_point_id']][] = (float)$arrResult[$kk]['lng'];
                }

                //
                // get creation route date 
                //
                $sql = "SELECT time FROM routeusage where idRoutePoint = " . $first_id_Route_Point  ." AND idUsrRoute= ". $json['route_id'] ." ORDER BY time ASC LIMIT 0 , 1 ";
                $arrResult2  = $this->exec_query($sql,'SEL');
                $arrData['creation_date'] = $arrResult2["time"];


                //
                //  diseable calculate distance
                // 2014-04-01 23:17:00
                /*
				* $point[] = $arrResult[$count-1]['lat'];
				* $point[] = $arrResult[$count-1]['lng'];
				* //select harvesine(@orig_lat, @orig_lon,@dest_lat, @dest_lon ) as dist";
				* $point = $point[0].",".$point[1].",".$point[2].",".$point[3];
				*
				* //$arrData['distance'] = $this->getDistanceBetweenPointsNew($point[0],$point[1],$point[2],$point[3]); 
				* //$arrData['distance'] = $this->getDistanceBetweenPointsNew($point[0],$point[1],$point[2],$point[3]); 
				* 
				* /*$sql_dist = "select harvesine(".$point.") as dist";
				* $a = $this->exec_query($sql_dist,'SEL');
                * $arrData['distance'] = (isset($a['dist']) && !empty($a['dist'])) ? $a['dist'] : 0;
                * */

                //
                // compress co-ordinates 
                //
                // FIXME  canot create class 
                // Wed Apr 02 00:20:14 2014] [error] [client 187.133.76.72] PHP Fatal error:  
                // Cannot access self:: when no class scope is active in /var/www/otu_app_srv/lib/LZString.php on line 37, 
                // referer: http://otu-srv.dyndns.ws/otu_app_srv/testCases.php?service=2.2_getRutIn
                //
                // $arrData["co-ordinates"] = LZString::compressToBase64(json_encode($arrData["co-ordinates"])); 
				
				$this->arrResultData['m'] = $arrData;
				return TRUE;
			}
			else {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Invalid Route.';
				if($this->debug)
					$this->arrResultData['debug'][] = array("route"=>"Route is not associated with user or route point is missing");
				return False;
			}
			 
		} // end of getRouteInfo()
		/*
			calculate distance between two points 
		*/
		function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') { 
			$theta = $longitude1 - $longitude2; 
			$distance = (sin(deg2rad((int)$latitude1)) * sin(deg2rad((int)$latitude2))) + 
						(cos(deg2rad((int)$latitude1)) * cos(deg2rad((int)$latitude2)) * 
						cos(deg2rad($theta))); 
			$distance = acos($distance); 
			$distance = rad2deg($distance); 
			$distance = $distance * 60 * 1.1515; 
			switch($unit) { 
				case 'Mi': 
					break; 
				case 'Km' : 
					$distance = $distance * 1.609344; 
			} 
			return array("miles"=> round($distance,2),"km"=>$distance * 1.609344);
			//return (round($distance,2)); 
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
					if(!empty($arrResult)){
						$this->userId = $arrResult['id'];
						$this->getRouteInfo($json);
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
			 
			  // check route_id index
			 if(!array_key_exists("route_id",$json) || empty($json['route_id'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'route_id is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("route_id"=>"route_id is missing or Invalid index");
					$flag=1;
			 }else {
				
				if(!is_integer($json["route_id"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid route id';
					if($this->debug)
						$this->arrResultData['debug'][] = array("route_id"=>"route id must be numeric format");
					$flag=1;
			}
			
			if($flag) { return false; } else { return true;}
			
		} 
		
	} // end of validateData()
 }
?>

<?php
/*
* Business Logic 
* component : sendProm
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	class sendProm{
	
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
		/*
		*	Logic
		*	1. get the segments which is come under in requested coverage . Build circle. 
		    2. find the intersect segment point from routespoint table
			
		*/
		function sendPromo($json) {
			
			// To fetch the travelmode Ids
			$arrTravelIds = array();
			$sql_travel = "SELECT `id` FROM `travelmode` WHERE id IN (".implode(",",$json['travel_mode']).")";
			$arrTravel = $this->exec_query($sql_travel,'ALL');
			foreach($arrTravel as $kk=>$val) {
				$arrTravelIds[$kk] = $val['id'];
			}
			$result=array_diff($arrTravelIds,$json['travel_mode']);
			if(count($result)>0){
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Invalid Travel mode';
				return false;
			}
			
			/* Below Logic is developed By : J. Rico. Nice Logic. I really learned some things new from Rico. It was nice experience to work with you.
			hats off Rico.. have a big blast. happy Coding..
			*/
			  $latLng  = array( 'lat' => floatval($json["latlang"][0]) , 
                                'lng' => floatval($json["latlang"][1])) ;
			  // radius or distance 
			  $distance = floatval($json["cov"]);
			  
			  // get 16 points to represet a circle
			  $bearing_array = array(0,22.5,45,67.5,90,112.5,135,157.5,180,202.5,225,247.5,270,247.5,315,337.5);
			  foreach ($bearing_array as &$grade)
			  {
				$circle_coordinates[] =  $this->bpot_getDueCoords($latLng['lat'], $latLng['lng'], $grade, $distance);
			  }
			   // build circle text query
			   //field ,position, astext(value)
			  $polygon_as_text = "SELECT id FROM segment WHERE MBRINTERSECTS( GeomFromText('POLYGON(( ";
			  foreach ($circle_coordinates as &$coord) 
			  {
				$polygon_as_text = $polygon_as_text .  $coord["lat"] . " " . $coord["lng"] . ",";
			  }
			  $polygon_as_text = $polygon_as_text . $circle_coordinates[0]["lat"] . " " . $circle_coordinates[0]["lng"] . "))'),  value        )= 1";
			  $arrSegment = $this->exec_query($polygon_as_text,'ALL');
			 
			$arrSegmentIds= array();
			foreach($arrSegment as $kk=>$val) {
				$arrSegmentIds[] = $val['id'];
			}
			$str_gen = "'".implode("','", $json['gender'])."'";
			$strg = '( ';
			$strl = '( ';
			$count = 1;
			$cnt_age = count($json['age_range']);
			foreach($json['age_range'] as $kk=>$val) {
				$temp = explode("-",$val);
					if($cnt_age==$count) {
						$strg.=  "usr.age > $temp[0]  ";
						$strl.=  "usr.age < $temp[1]  ";
					}
					else
					 {
						$strg.=  "usr.age > $temp[0] OR ";
						$strl.=  "usr.age < $temp[1] OR ";
					 }
					 $count++;
			}
			 $strg .= ')';
			 $strl .= ')';
			 $str_age =  $strg." AND ".$strl;
			 
			$sql = '';
			$sql .= " SELECT DISTINCT rusg.idRoutePoint ,rusg.`idUsrRoute`, usr.id as user_id, usr.age, usr.gender";
			
			$sql .=" FROM `routeusage` as rusg, usr as usr, routepoint as rpt ";
			
			//condition for travel mode
			$sql .= " WHERE rusg.idTravelMode IN (".implode(",",$json['travel_mode']).") ";
			
			//conditon for idRoutePoint and idSegment 
			$sql .= " AND rusg.idRoutePoint IN (SELECT rpt.id as idRoutepoint FROM  routepoint as rpt WHERE rpt.idSegment IN (".implode(",",$arrSegmentIds).")) ";
			
			// condition for gender and age 
			$sql .= " AND usr.gender IN (".$str_gen.") ";
			$sql .= " AND usr.id=rusg.idUsr AND ".$str_age." "  ;
						
			$arrRouetusage  = $this->exec_query($sql,'ALL');	
			$arrData = array();
			$latitude  = $latLng['lat']; 
			$longitude = $latLng['lng'];
			// increase the distance bt 10%
			$distance_nearby = $distance+(($distance / 100) * 10); 
			foreach($arrRouetusage as $kk=>$val) {
				
				$sql_latlng  = "SELECT  X(rpt.latLng) as lat, Y(rpt.latLng) as lng  FROM `routepoint` as rpt WHERE `id`=".$arrRouetusage[$kk]['idRoutePoint'];
				$arr_latng  = $this->exec_query($sql_latlng,'SEL');	
				$sql1 = "SELECT `id`, `idSegment`, `idUserroutes`,(((acos(sin((".$latitude."*pi()/180)) * 
									sin(((".$arr_latng['lat'].")*pi()/180))+cos((".$latitude."*pi()/180)) * 
									cos(((".$arr_latng['lat'].")*pi()/180)) * cos(((".$longitude."- (".$arr_latng['lng']."))* 
									pi()/180))))*180/pi())*((60*1.1515*1.609344)/0.0010000)
								) as distance_in_meter 
								FROM `routepoint` as rpt 
								HAVING distance_in_meter <= ".$distance_nearby;
								
     			$arrDist  = $this->exec_query($sql1,'SEL');	
				if(is_array($arrDist) && !empty($arrDist)) {
					$temp_data['idRoute']      = $arrRouetusage[$kk]['idUsrRoute'];
					$temp_data['idRoutePoint'] =$arrRouetusage[$kk]['idRoutePoint']; 
					$arrData[$arrRouetusage[$kk]['user_id']][] = $temp_data;
				}
            }
			$this->arrResultData['m'] = $arrData;
				
			
			/*
			$sql = "SELECT  rpt.idUserroutes as idRoute, rpt.id as idRoutepoint, X(rpt.latLng) as lat, Y(rpt.latLng) as lng FROM  routepoint as rpt WHERE 1 ";
			$arrPoint  = $this->exec_query($sql,'ALL');	
			$point[0] = $json["latlang"][0];
			$point[1] = $json["latlang"][1];
			$arr_idRoutepoint= array();
			 foreach($arrPoint as $kk=>$val) {
				$point[2] = $arrPoint[$kk]['lat'];
				$point[3] = $arrPoint[$kk]['lng'];
				$dist = $this->getDistanceBetweenPointsNew($point[0],$point[1],$point[2],$point[3]);
				if( $dist< 500) {
					//$arrRouteIds[] = array("idRoute"=>$arrPoint[$kk]['idRoute'],"idRoutepoint"=>$arrPoint[$kk]['idRoutepoint']);
					$arr_idRoutepoint[] = $arrPoint[$kk]['idRoutepoint'];
				
				}				
			}
			$str_gen = "'".implode("','", $json['gender'])."'";
			$strg = '( ';
			$strl = '( ';
			$count = 1;
			$cnt_age = count($json['age_range']);
			foreach($json['age_range'] as $kk=>$val) {
				$temp = explode("-",$val);
					if($cnt_age==$count) {
						$strg.=  "usr.age > $temp[0]  ";
						$strl.=  "usr.age < $temp[1]  ";
					}
					else
					 {
						$strg.=  "usr.age > $temp[0] OR ";
						$strl.=  "usr.age < $temp[1] OR ";
					 }
					 $count++;
			}
			 $strg .= ')';
			 $strl .= ')';
			 $str_age =  $strg." AND ".$strl;
			   
			//$sql_usages = "SELECT usr.id as user_id, usr.age, usr.gender, `idRoutePoint`, `idUsrRoute`, `idUsr`, `idTravelMode` FROM `routeusage` ,usr as usr WHERE `idRoutePoint` IN (".implode(",",$arr_idRoutepoint).") AND idTravelMode IN (".implode(",",$json['travel_mode']).") AND usr.gender IN (".$str_gen.")  AND usr.id=routeusage.idUsr AND ".$str_age;
			
			$sql_usages = "SELECT usr.id as user_id, usr.age, usr.gender, `idRoutePoint`, `idUsrRoute`, `idUsr`, `idTravelMode`, X(rpt.latLng) as lat, Y(rpt.latLng) as lng  FROM `routeusage` ,usr as usr, routepoint as rpt WHERE `idRoutePoint` IN (".implode(",",$arr_idRoutepoint).") AND idTravelMode IN (".implode(",",$json['travel_mode']).") AND usr.gender IN (".$str_gen.")  AND usr.id=routeusage.idUsr AND ".$str_age." AND rpt.id =routeusage.idRoutePoint" ;
			
			$arrRouetusage  = $this->exec_query($sql_usages,'ALL');	
			$arrData = array();
			foreach($arrRouetusage as $kk=>$val) {
					$temp_data['idRoute']      = $arrRouetusage[$kk]['idUsrRoute'];
					$temp_data['idRoutePoint'] =$arrRouetusage[$kk]['idRoutePoint']; 
					if(isset($json['alldata']) && $json['alldata']) {
						$temp_data['lat']          = $arrRouetusage[$kk]['lat'];
						$temp_data['lng']          = $arrRouetusage[$kk]['lng'];
						$temp_data['age']          = $arrRouetusage[$kk]['age'];
						$temp_data['gender']       = $arrRouetusage[$kk]['gender'];
						$temp_data['idTravelMode'] = $arrRouetusage[$kk]['idTravelMode'];
						$temp_data['user_id']      = $arrRouetusage[$kk]['user_id'];
					
					}
					$arrData[$arrRouetusage[$kk]['user_id']][] = $temp_data;
			}
			echo "<pre>"; print_r($arrData); */
			
		} //end of sendPromo()
		
		/*
		* Function to validata the request data
		*/
		
		public function validarEstructura($jsonEntrada) {
		   $json = $jsonEntrada['PRO'];
		   $arrResult = array();
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			if($this->debug) 
				$this->arrResultData['debug'][] = array("send query"=>$json);
			if($this->validateData($json)) {
				$this->sendPromo($json);
			}
			return $this->arrResultData;
		}
		
		/*
			calculate distance between two points 
		
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
			//return array("miles"=> round($distance,2),"km"=>$distance * 1.609344);
			return (round($distance,2)); 
		}*/
		
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
				
			// check latlang index
			if(!array_key_exists("latlang",$json) || empty($json['latlang'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'latlang node is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"latlang is missing or Invalid index");
						$flag=1;	
			}
			 // validate co-ordinates value it must be float value
			if(isset($json['latlang']))
				foreach($json['latlang']as $kk=>$val) {
					//  lat,lang 
					if(!is_float($val)) {
					   	$flag=1;
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'Invalid structure latlang';
						break;
					}
			}
			
			// check cov index
			if(!array_key_exists("cov",$json) || empty($json['cov'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'cov node is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"cov is missing or Invalid index");
						$flag=1;	
			}
			
			// check travel_mode index
			if(!array_key_exists("travel_mode",$json) || empty($json['travel_mode'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'travel_mode node is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"travel_mode is missing or Invalid index");
						$flag=1;	
			}
			
			// validate travel_mode value it must be int value
			if(isset($json['travel_mode']))
				foreach($json['travel_mode']as $kk=>$val) {
					//  Travel mode 
					if(!is_int($val)) {
						$flag=1;
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'malformed structure co-ordinates';
						break;
					}
			}
			
			if(!array_key_exists("gender",$json) || empty($json['gender'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'gender node is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"gender is missing or Invalid index");
						$flag=1;	
			}
			// validate gender value it must be string value
			if(isset($json['gender'])) 
				foreach($json['gender'] as $kk=>$val) {
					//  gender mode 
					if(!is_string($val) ) {
						$flag=1;
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'invalid gender';
						break;
					}
			}
			
			if(!array_key_exists("age_range",$json) || empty($json['age_range'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'age_range node is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"age_range is missing or Invalid index");
						$flag=1;	
			}
			
			
			if($flag) { return false; } else { return true;}
		}
		
		
		// Modified from:
		// http://www.sitepoint.com/forums/showthread.php?656315-adding-distance-gps-coordinates-get-bounding-box
		/**
		 * bearing is 0 = north, 90 = east, 180 = south, 270 = west
		 *
		*/
		 function bpot_getDueCoords($latitude, $longitude, $bearing, $distance, $distance_unit = "m", $return_as_array = TRUE) {
		   
			if ($distance_unit == "m") {
				// Distance is in miles.
				//$radius = 3963.1676;
				//Distance is in meter.
				$radius = 6378100; 
			}
			else {
				// distance is in km.
				$radius = 6378.1;
			}
		  
			//	New latitude in degrees.
			$new_latitude = rad2deg(asin(sin(deg2rad($latitude)) * cos($distance / $radius) + cos(deg2rad($latitude)) * sin($distance / $radius) * cos(deg2rad($bearing))));
					
			//	New longitude in degrees.
			$new_longitude = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad($bearing)) * sin($distance / $radius) * cos(deg2rad($latitude)), cos($distance / $radius) - sin(deg2rad($latitude)) * sin(deg2rad($new_latitude))));
			
			if ($return_as_array) {
			  //  Assign new latitude and longitude to an array to be returned to the caller.
			  $coord = array();
			  $coord['lat'] = $new_latitude;
			  $coord['lng'] = $new_longitude;
			}
			else {
			  $coord = $new_latitude . "," . $new_longitude;
			}
			
			return $coord;
		}	
	}
?>
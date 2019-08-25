<?php
/*
* Business Logic 
* component : sendProm
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	class getStat{
	
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
		* function :: getStatistics
		*/
		
		function getStat($json) {
			
			$arrData = array();
			$arrSegmentIds= array();
			$arrFinalResult = array();
			$arrPoint = array();
			$sql = "";
			/***********Start logic to get the affected segments using coverage and latlang :: By Rico ********************************************/
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
				
			foreach($arrSegment as $kk=>$val) {
				$arrSegmentIds[] = $val['id'];
			}
			
			$sql_point  = "SELECT  `id` FROM `routepoint` WHERE `idSegment` IN (".implode(",",$arrSegmentIds).")";
			$arrPointResult  = $this->exec_query($sql_point,'ALL');
			
			foreach($arrPointResult as $kk=>$val) {
				$arrPoint[] = $val['id'];
			} 
			/***********End logic to get the affected segments using coverage and latlang :: By Rico ********************************************/
			
			/*****************Query to build the response structure ******************/
			$indexCnt= 0;
			$compare_date =  "'".implode("','", $json['date'])."'";
			
			//$sql = " SELECT  count(DISTINCT idUsrRoute) as routes ,count(DISTINCT idUsr) as people,GROUP_CONCAT(DISTINCT idUsrRoute) as route_ids, GROUP_CONCAT(DISTINCT idUsr) as users, GROUP_CONCAT(time) as times_span, GROUP_CONCAT(travelmode.mode) as travelmode , GROUP_CONCAT(usr.gender) as gender, GROUP_CONCAT(usr.age) as age FROM `routeusage` , travelmode, usr where travelmode.id = routeusage.idTravelMode AND usr.id = routeusage.idUsr AND Date(time) IN (".$compare_date.") AND routeusage.idRoutePoint IN (".implode(",",$arrPoint).") group by HOUR(time) order by routeusage.idTravelMode ";
			$sql = " SELECT  count(DISTINCT idUsrRoute) as routes ,count(DISTINCT idUsr) as people,GROUP_CONCAT(DISTINCT idUsrRoute) as route_ids, GROUP_CONCAT(DISTINCT idUsr) as users, GROUP_CONCAT(time) as times_span, GROUP_CONCAT(travelmode.mode,'-',idUsr) as travelmode , GROUP_CONCAT(usr.gender,'-',idUsr) as gender, GROUP_CONCAT(usr.age,'-',idUsr) as age FROM `routeusage` , travelmode, usr where travelmode.id = routeusage.idTravelMode AND usr.id = routeusage.idUsr AND Date(time) IN (".$compare_date.") AND routeusage.idRoutePoint IN (".implode(",",$arrPoint).") group by HOUR(time) order by routeusage.idTravelMode ";
		
     		$arrResult  = $this->exec_query($sql,'ALL');
			foreach($arrResult as $kk=>$val) {
					//$arrData[$indexCnt]['timespan'] = date('H', strtotime($time[0]));
					$time  = explode(",",$arrResult[$kk]['times_span']);
					$indexCnt = date('H', strtotime($time[0]));
					$arrData[$indexCnt]['routes'] = $arrResult[$kk]['routes'];
					$arrData[$indexCnt]['people'] = $arrResult[$kk]['people'];
					$arrData[$indexCnt]['gender'] =   $this->getGender($arrResult[$kk]['gender']);
					$arrData[$indexCnt]['age'] =   $this->getAgeRange($arrResult[$kk]['age']);
					$arrData[$indexCnt]['TravelMode'] =   $this->getTreavelMode($arrResult[$kk]['travelmode']);
					//."-".sprintf("%02s",((date('H', strtotime($time[0])))+1));
					//$indexCnt++;
			}
			
			//echo "<pre>"; print_r($arrData); exit;
			$arrNoData = array();
			$arrNoData['routes'] = 0;
            $arrNoData['people'] = 0;
            $arrNoData['gender'] = array('m' => 0,'f' => 0);
            $arrNoData['age'] = array('18-25' => 0, '26-35' => 0, '36-50' => 0,'51-80' => 0);
            $arrNoData['TravelMode'] = array('bycycle' => 0,'car' => 0,'bus' => 0, 'train' => 0,'walking' => 0,'running' => 0 );
			for($i=0;$i<24;$i++) {
				if (array_key_exists(sprintf("%02s",$i), $arrData)) 
				    $arrFinalResult[] = $arrData[sprintf("%02s",$i)];
			    else
				    $arrFinalResult[] = $arrNoData;
			}
			$this->arrResultData['m']  = $arrFinalResult;
			
			//$sql .=" SELECT rpt.id as idRoutepoint,rpt.idUserroutes as idRoute ";
			/*$sql .=" SELECT DISTINCT rpt.id as idRoutepoint, rpt.idUserroutes as idRoute,tmode.mode, usr.id as user_id, usr.age,usr.gender, usr.registerDate ";
			$sql .=" FROM  routepoint as rpt, routeusage as rusg , travelmode as tmode , usr as usr ";
			$sql .=" WHERE rpt.idSegment IN (".implode(",",$arrSegmentIds).") ";
			$sql .=" AND  rusg.idRoutePoint = rpt.id ";
			$sql .=" AND  tmode.id = rusg.idTravelMode ";
			$sql .=" AND  usr.id = rusg.idUsr ";
			$sql .=" AND  usr.registerDate >= '".$compare_date."'";
			echo $sql;
			echo "<pre>"; print_r($arrResult);exit; */
		} //end of getStat()
		
		/*
		 * Function get travel mode count
		*/
		 
		function getTreavelMode($arrStr) {
				$arrModes = explode(",",$arrStr);
				$arrDist = array();
				$arrData = array();
				$arrFinalData = array();
				
				$arrModes = array_unique($arrModes);
				
				
				$sql_Travel_mode = "SELECT `id`, `mode` FROM `travelmode` WHERE 1";
				$arrResult  = $this->exec_query($sql_Travel_mode,'ALL');
				foreach($arrResult as $kk=>$val) {
					$arrDist[] =$arrResult[$kk]['mode']; 
				}
				foreach($arrModes as $kk=>$val) {
					$index =  explode("-",$val) ;
					$arrData[$index[0]][] =  $index[0];
				}
				foreach($arrDist as $kk=>$val){
					 if(array_key_exists($val, $arrData))
						$arrFinalData[$val] = count($arrData[$val]);
					 else
						$arrFinalData[$val] = 0;
				}
				return $arrFinalData;
		}
		
		/*
		 * Function get Gender count
		 */
		 
		function getGender($arrStr) {
			    $arrGenders = explode(",",$arrStr);
				
				$arrGenders = array_unique($arrGenders);
				$arrGen = array();
				$arrFinalData = array();
				
				$arrGen[] = 'm';
				$arrGen[] = 'f';
				
				foreach($arrGenders as $kk=>$val) {
				    $index =  explode("-",$val) ;
					$arrData[$index[0]][] =  $index[0];
				}
				foreach($arrGen as $kk=>$val) {
					 if(array_key_exists($val, $arrData))
						$arrFinalData[$val] = count($arrData[$val]);
					else
					 $arrFinalData[$val] = 0;
				}
				return $arrFinalData;
		}
		
		/*
		 *Function get age range count
		 */
		 
		function getAgeRange($arrStr) {
				$arrAges = explode(",",$arrStr);
				$arrAgeData  = array();
				$arrFinalData = array();
				
				$arrAges = array_unique($arrAges);
				
				$arrAgeRange[] = '18-25';
				$arrAgeRange[] = '26-35';
				$arrAgeRange[] = '36-50';
				$arrAgeRange[] = '51-80';
			
				foreach ($arrAges  as $kk=>$val) {
				$val2 =  explode("-",$val) ;
				$val1 = $val2[0];
					if($val1 >= 18 && $val1 <= 25)
						$arrAgeData['18-25'][] = $val1;
					if($val1 >= 26 && $val1 <= 35)	
						$arrAgeData['26-35'][] = $val1;
					if($val1 >= 36 && $val1 <= 50)
						$arrAgeData['36-50'][] = $val1;
					if($val1 >= 51 && $val1 <= 80)
						$arrAgeData['51-80'][] = $val1;
				
				}
				foreach ($arrAgeRange as $kk=>$val)  {
					if(array_key_exists($val, $arrAgeData))
						$arrFinalData[$val] = count($arrAgeData[$val]);
					else
					   $arrFinalData[$val] = 0;
				}
				
				return $arrFinalData;
     	} // end of getAgeRange()
		
		/*
		* Function to validata the request data
		*/
		public function validarEstructura($jsonEntrada) {
		    $json = $jsonEntrada['STA'];
		    $arrResult = array();
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			if($this->debug) 
				$this->arrResultData['debug'][] = array("send query"=>$json);
			if($this->validateData($json)) {
				$this->getStat($json);
			}
			return $this->arrResultData;
		} //end of validarEstructura
		
		
		/*
		 function to process the Data validation
		*/
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
				
				// check date index
				if(!array_key_exists("date",$json) || empty($json['date'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'date node is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("date"=>"date is missing or Invalid index");
						$flag=1;	
			    }
				
				// check coverage index
				if(!array_key_exists("cov",$json) || empty($json['cov'])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'cov node is missing';
					if($this->debug)
						$this->arrResultData['debug'][] = array("user"=>"cov is missing or Invalid index");
						$flag=1;	
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
			
				if($flag) { return false; } else { return true;}
			} // end of validateData()
		
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
			
	}// end of class
?>
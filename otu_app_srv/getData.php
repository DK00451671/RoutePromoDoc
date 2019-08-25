<?php
/*

http://localhost/otu_app_srv/Main.php?json={"PERF":{"fun":"performanceTest","user":"test@test.com","passwd":"123","radius_of_route_point":100,"iteration":2,"timer":0}}
* Business Logic 
* component : sendProm
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	
	class performanceTest{
	
		// class variable
		private $arrResultData= array();
		private $conexion;
		private $userId;
		private $arr_error_log= array();
		private $inputParams = array();
		private $debug = false;
		private $url = null;
		/* 
			Description :Function to initialize object
			Function name : consultarIdUsuario
			Input Parasm : NA
			Output       : NA
	     */
		public function __construct() {
		
			$this->arrResultData = array("success" => "true", "m" => array());
			$this->conexion = mysqli_connect(SERVIDOR, USUARIO, CONTRASENA, BASEDATOS) or die (json_encode(array("success" => "false", "m" => "Fail to connect Database at server: " . SERVIDOR)));
			$this->url = "http://localhost/otu_app_srv/Main.php?json="; 
        
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
		
	function sendNewRouteRawData($arrParams) {
		
		   $arrRoutePoint = array();
		   $arrData = array();
		   $distance = floatval($arrParams);
		   $sql = "SELECT X(rpt.latLng) as lat, Y(rpt.latLng) as lng FROM `routepoint` as rpt ORDER BY RAND() limit 1";
		   
		   $arrResult = $this->exec_query($sql,'SEL');
		
		   $no_of_coordinates = rand(2, 8);
		   /* Below Logic is developed By : J. Rico. Nice Logic. I really learned some things new from Rico. It was nice experience to work with you.
			hats off Rico.. have a big blast. happy Coding..
			*/
			$latLng  = array( 'lat' => floatval($arrResult["lat"]) , 
                                'lng' => floatval($arrResult["lng"])) ;
								
			for($i=0;$i<$no_of_coordinates;$i++) {
			  //sleep(1);
			  $arrRoutePoint[$i][] = strtotime(date('Y-m-d H:i:s'));					
			  $arrRoutePoint[$i][] = $latLng['lat']; 
			  $arrRoutePoint[$i][] = $latLng['lng']; 
			 			  			  
			  // radius or distance 
			  
			  
			  // get 16 points to represet a circle
			  $bearing_array = array(0,22.5,45,67.5,90,112.5,135,157.5,180,202.5,225,247.5,270,247.5,315,337.5);
			  foreach ($bearing_array as &$grade)
			  {
				$circle_coordinates[] =  $this->bpot_getDueCoords($latLng['lat'], $latLng['lng'], $grade, $distance);
			  }
			    $rand_keys = array_rand($circle_coordinates, 1);
				$latLng = $circle_coordinates[$rand_keys];
				$arrRoutePoint[$i][] = rand(1,3); 
				$arrRoutePoint[$i][] = "messagetxt".strtotime(date('Y-m-d H:i:s')); 
			 }
			 $arrData['co-ordinates'] = $arrRoutePoint;
			 return $arrData;
		}
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
	$obj   = new performanceTest;
if(isset($_GET['req']) || isset($_POST['req'] )) { 
	$action = isset($_GET['req']) ? $_GET['req'] : $_POST['req'];
    switch($action){
		case 'sendNewRouteRawData':
			$res = $obj->sendNewRouteRawData(100);
		break;
	}
	echo json_encode($res); exit;
}
?>
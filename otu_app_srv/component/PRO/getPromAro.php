<?php
/*
* Business Logic 
* component : getPromAro
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	
	class getPromAro{
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
			
			// connection for database_0
			$this->conexion_0 = mysqli_connect(SERVIDOR, USUARIO, CONTRASENA, BASEDATOS_0) or die (json_encode(array("success" => "false", "m" => "Fail to connect Database at server: " . SERVIDOR)));
			
			$this->userId =0;
			
        
		} // end of __construct()
		
		/* 
			Description :Function to execuate Queries for  database_0
			Function name : exec_query_0
			Input Parasm : sql_query , flag= options
			Output       : Query result
	     */
		function exec_query_0($sql_query,$flag) {
			$arrAllData = array();
			$result = mysqli_query($this->conexion_0, $sql_query) or die (json_encode(array("success" => "false", "m" => "record not inserted. " . $sql_query)));
			
			switch($flag) {
				case 'INS':
					if($result) { return mysqli_insert_id($this->conexion_0); }
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
		
		public function getPromotionAround($json) {
		   
		   $arrData = array();
		   $arr_latng['lat'] = $json['latLng'][0];
           $arr_latng['lng'] = $json['latLng'][1];
		   $distance = $json['cov'];
		   //,comp.`id` as companyId, comp.idCommercialTypes,pTypeUsr.isUsr,
		   $sql = "SELECT prom.id as promId ,(((acos(sin((X(comp.geographicPosition)*pi()/180)) * 
									sin(((".$arr_latng['lat'].")*pi()/180))+cos((X(comp.geographicPosition)*pi()/180)) * 
									cos(((".$arr_latng['lat'].")*pi()/180)) * cos(((Y(comp.geographicPosition)- (".$arr_latng['lng']."))* 
									pi()/180))))*180/pi())*((60*1.1515*1.609344)/0.0010000)
								) as distance_in_meter 
								FROM `company` as comp , promotionTypeUser as pTypeUsr, promotions as prom
								WHERE pTypeUsr.isUsr = ".$this->userId."  AND comp.idCommercialTypes=pTypeUsr.idCommercialTypes AND prom.idCompany= comp.`id` AND  prom.cancelDate IS NULL AND STR_TO_DATE(prom.endDate, '%Y-%m-%d') >=  CURDATE()   
								HAVING distance_in_meter <=".$distance ;
					
				$arrResult = $this->exec_query_0($sql,'ALL');
				
				
				if(!empty($arrResult)) {				
				   foreach($arrResult as $kk=>$val) {
					 $arrData[] = $arrResult[$kk]['promId'];
				   }
				   $this->arrResultData['m'] = $arrData;
				}
				else
				   $this->arrResultData['m'] = array();
				return True;
        }		
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
                    //
                    // validate user and passwd
                    //
                    $Obj_logIn = new LogIn();
                    $arrResult = $Obj_logIn->authenticateUser($json['user'],$json['passwd']);
					
					if(!empty($arrResult)){
					     $this->userId = $arrResult['id'];
						$arrPromotion = $this->getPromotionAround($json);
                        if(empty($arrPromotion)) {
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = 'No data present';
						}
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
			
			 // check latLng index
		 if(!array_key_exists("latLng",$json) || empty($json['latLng'])) {
			$this->arrResultData['success'] = 'false';
			$this->arrResultData['m'] = 'latLng node is missing';
			   if($this->debug)
					$this->arrResultData['debug'][] = array("latLng"=>"latLng is missing or Invalid index");
				$flag=1;	
		 }
		 
		 if(isset($json['latLng'])){
			if((!is_float($json['latLng'][0]) || !is_float($json['latLng'][1]))) {
					$flag=1;
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'malformed structure latLng';
				}	
			}
			
		 // check latLng index
		 if(!array_key_exists("cov",$json) || empty($json['cov'])) {
			$this->arrResultData['success'] = 'false';
			$this->arrResultData['m'] = 'coverage node is missing';
			   if($this->debug)
					$this->arrResultData['debug'][] = array("cov"=>"cov is missing or Invalid index");
				$flag=1;	
		 }
		 if(!is_int($json['cov'])){
			$this->arrResultData['success'] = 'false';
			$this->arrResultData['m'] = 'coverage must be number format';
			$flag=1;	
		 }
			
			if($flag) { return false; } else { return true;}
		}
		
	}
?>
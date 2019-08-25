<?php
/*
* Business Logic 
* component : shareProm
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	
	class getBusiInf{
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
			Description :Function to execuate Queries for  database_1
			Function name : exec_query
			Input Parasm : sql_query , flag= options
			Output       : Query result
	     */
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
			Description :Function to execuate Queries for  database_0
			Function name : exec_query
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
		
		/* 
            Description   : Function to get full information from promotion
                            prom_id, id_company, logo_icon_start_with, company_name,
                            latLng, promotion_text, time_life
			Function name : getPromInformation
			Input Parasm  : prom id and user id
			Output        : array
	     */
        public function getBusinessInformation($company_id){
            //
            // get id, idCompany, prom_text, company_name, latLng, logo_icon_start_with
            //
            $sql_check  = "SELECT ".
              "company.id, ".
              "company.name, ".
              "GROUP_CONCAT(X( company.geographicPosition ) ,  ',', Y( company.geographicPosition ) ) AS latLng, ".
              "logoTypes.logo as icon, ".
              "commercialTypes.name as commercialTypes, ".
              "company.address, ".
              "company.city ".
              "FROM  company ".
              "join  logoTypes on ( company.idLogosTypes = logoTypes.id ) ".
              "join  commercialTypes on ( commercialTypes.id = company.idCommercialTypes ) ".
              "where company.id =".$company_id ;
		
            $arrComp = $this->exec_query_0($sql_check,'SEL');

            if(empty($arrComp["id"])){ 
                //
                // company not exist
                //
                return FALSE; 
            }
            else{
                $result_ = $arrComp;
				$result_['id'] = (isset($result_['id'])) ? (int)$result_['id']:$result_['id'];
				if(isset($result_['latLng'])) {
					$co_ordinates = (explode(",",$result_['latLng']));
					$result_['latLng'] = (isset($result_['latLng'])) ? array((float)$co_ordinates[0],(float)$co_ordinates[1]):$result_['idCompany'];
				}
			}
            $this->arrResultData['m'] = $result_;
            return TRUE;
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
                        $arrCompany = $this->getBusinessInformation($json["company_id"]);
                        if(empty($arrCompany)) {
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = 'Invalid company id';
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
			 
			  // check prom_id index
			 if(!array_key_exists("company_id",$json) || empty($json['company_id'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Company Id is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("company_id"=>"Company Id is missing or Invalid index");
					$flag=1;
			 }else {
				
				if(!is_integer($json["company_id"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid company id';
					if($this->debug)
						$this->arrResultData['debug'][] = array("company_id"=>"Promotion Id must be numeric format");
					$flag=1;
				}
			 }
			  
			 
			 if($flag) { return false; } else { return true;}
			 
		} // end of validateData()
	
	}
?>

<?php
/*
* Business Logic 
* component : shareProm
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	
	class getPromRou{
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
        public function getPromInformation($route_id,$usr_id){
            //
            // determine if promotion ID belong to:
            //  1) shared or owned route
            //  3) shared promotion
            
            //
            // TODO
            //      when a Route is shared with an user, promotions assigned 
            //      to that route has to be assigned to the shared user to 
            //      matain data consistency
            //
            //
            // query to get all promotions id that match with promotions type 
            // selected by user on table promotionTypeUser
            $sql_get_prom_ids  = "SELECT GROUP_CONCAT(idPromotions) as prom_ids ".
                                  "FROM promotionAssign ".
                                  "join promotions on(promotions.id = promotionAssign.idPromotions) ".
                                  //"join company on (company.id = promotions.idCompany) ".
                                  //"join commercialTypes on (commercialTypes.id = company.idCommercialTypes) ".
                                  //"join promotionTypeUser on (promotionTypeUser.idCommercialTypes = company.idCommercialTypes) ".
                                  "where promotionAssign.idRoute =".$route_id." AND promotionAssign.valid=1 AND promotionAssign.idUsr= ".$usr_id." AND STR_TO_DATE(promotions.endDate, '%Y-%m-%d') >=  CURDATE()";
							  
						  
            $prom_ids = $this->exec_query_0($sql_get_prom_ids,'SEL');
            //var_dump($prom_ids);
            if(!empty($prom_ids)){
                //
                $this->arrResultData['m'] = array();
                $prom_ids = explode(",",$prom_ids["prom_ids"]);
                foreach($prom_ids as $prom){
				   if($prom)
                     $this->arrResultData['m'][] = (int)$prom;
                }
            }
            else{
                //
                $this->arrResultData['m'] = array();
            }

           
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
                        $arrPromotion = $this->getPromInformation($json["route_id"],$arrResult["id"]);
                        if(empty($arrPromotion)) {
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = 'Invalid route id';
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
			 
			  // check route_id index
			 if(!array_key_exists("route_id",$json) || empty($json['route_id'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Route Id is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("route_id"=>"Promotion Id is missing or Invalid index");
					$flag=1;
			 }else {
				
				if(!is_integer($json["route_id"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid route id';
					if($this->debug)
						$this->arrResultData['debug'][] = array("route_id"=>"Promotion Id must be numeric format");
					$flag=1;
				}
			 }
			  
			 
			 if($flag) { return false; } else { return true;}
			 
		} // end of validateData()
	
	}
?>

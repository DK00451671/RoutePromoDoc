<?php
/*
* Business Logic 
* component : usedProm
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	class usedProm {
	
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
		function getViewedPromo($json) {
			
			$check_promo = "SELECT `id` FROM `promotions` WHERE `id`=".$json['prom_id'];
			$arrPromo = $this->exec_query_0($check_promo,'SEL');
			
			if(isset($arrPromo['id']) && !empty($arrPromo)){
			
			        $sql_route_assign  = "SELECT id,idRoute from promotionAssign where promotionAssign.idPromotions =".$arrPromo['id']." and promotionAssign.idUsr =".$this->userId ;
					$route_assign = $this->exec_query_0($sql_route_assign,'SEL');
					//var_dump($route_assign);
				
                    if(!empty($route_assign["id"]) && isset($route_assign["id"]) ){
		    		    // insert promotionAssignId and date in promotionReceive table
    		    		$sql_promo_assign = "INSERT INTO `promotionUsed`(`id`, `idPromotionsAssing`, `date`) VALUES (0,".$route_assign["id"].",'".date('Y-m-d H:i:s')."')";
	    		      	$this->exec_query_0($sql_promo_assign,'INS');
                    }
                    else{            
                        // assign promotion 
                        $sql_assign_prom = "INSERT INTO   `promotionAssign` (`id`, `idPromotions`, `idRoute`, `idRoutePoint`, `idUsr`, `idpromotionShared`, `valid`) VALUES (NULL, ".$json['prom_id'].", NULL, NULL, '".$this->userId."', NULL, '1')"; 
                        $assign_id = $this->exec_query_0($sql_assign_prom,'INS');
                        if($assign_id > 0){
                            // set prom received
                            $sql_promo_assign = "INSERT INTO `promotionUsed`(`id`, `idPromotionsAssing`, `date`) VALUES (0,".$assign_id.",'".date('Y-m-d H:i:s')."')";
                            $usedId = $this->exec_query_0($sql_promo_assign,'INS');
                            // received prom error
                            if($usedId < 1){
                                $this->arrResultData['success'] = 'false';
                                $this->arrResultData['m'] = array("status"=>'Fatal error, promotion used error');
                                return FALSE;
                            }
                        }
                        else{
                            // assign prom error
                            $this->arrResultData['success'] = 'false';
                            $this->arrResultData['m'] = array("status"=>'Fatal error, promotion not assigned');
                            return FALSE;
                            }
                      }
			} // end val promId
            else {
		            $this->arrResultData['success'] = 'false';
    			    $this->arrResultData['m'] = 'promotion id not exit';
      		}
		} // end of getViewedPromo()
		
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
				$Obj_logIn= new LogIn();
				$arrResult = $Obj_logIn->authenticateUser($json['user'],$json['passwd']);
				if(!empty($arrResult) && !$arrResult['is_non_register']) {
					$this->userId = $arrResult['id'];
					$this->getViewedPromo($json);
				}
				else {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid user or password';
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
				
						// check prom_id index
				 if(!array_key_exists("prom_id",$json) || empty($json['prom_id'])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'prom_id is missing';
					   if($this->debug)
							$this->arrResultData['debug'][] = array("prom_id"=>"prom_id are missing");
						$flag=1;
				 }else
				 {
					if(!is_int($json['prom_id'])){
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'invalid prom_id';
						if($this->debug)
							$this->arrResultData['debug'][] = array("prom_id"=>"commertial types  must be numeric value");
							$flag=1;
							break;
						}
				}
				if($flag) { return false; } else { return true;}
		
		}
		
	}
?>

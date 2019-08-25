<?php
/*
* Business Logic 
* component : shareProm
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	
	class getPromInf{
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


        public function getPromInformation($prom_id,$user_id){
            //
            // get id, idCompany, prom_text, company_name, latLng, logo_icon_start_with
            //
            $sql_check  = "SELECT ".
              "promotions.id, ".
              "promotions.idCompany, ".
              "promotions.message as prom_text, ".
              "company.name as company_name, ".
              "GROUP_CONCAT( X( company.geographicPosition ) ,  ',', Y( company.geographicPosition ) ) AS latLng, ".
              "LEFT(logoTypes.logo , 30) as logo_icon_start_with ".
              "FROM promotions ".
              "join  company on ( company.id = promotions.idCompany ) ".
              "join  logoTypes on ( company.idLogosTypes = logoTypes.id ) ".
              "where promotions.id =".$prom_id ;
		
            $arrProm = $this->exec_query_0($sql_check,'SEL');
            if(empty($arrProm["id"])){ 
                //
                // promotion not exist
                //
                return FALSE; 
            }
            else{
			    $result_ = $arrProm;
				$result_['id'] = (isset($result_['id'])) ? (int)$result_['id']:$result_['id'];
				$result_['idCompany'] = (isset($result_['idCompany'])) ? (int)$result_['idCompany']:$result_['idCompany'];
				if(isset($result_['latLng'])) {
					$co_ordinates = (explode(",",$result_['latLng']));
					$result_['latLng'] = (isset($result_['latLng'])) ? array((float)$co_ordinates[0],(float)$co_ordinates[1]):$result_['idCompany'];
				}
			}
  
            //var_dump($arrProm);


            //
            // get date time life for a promotion
            //
            $sql_time_life = "SELECT DATE, prom_time.time_ ".
             "FROM ( ".
               "SELECT idPromotions, GROUP_CONCAT(  `00:00` ,  `01:00` ,  `02:00` ,  `03:00` ,  `04:00` ,  `05:00` ,  `06:00` ,  `07:00` ,  `08:00` ,  `09:00` ,  `10:00` ,  `11:00` ,  `12:00` ,  `13:00` ,  `14:00` ,  `15:00` ,  `16:00` ,  `17:00` ,  `18:00` ,  `19:00` ,  `20:00` ,  `21:00` ,  `22:00` , `23:00` ) time_ ".
               "FROM promotionTime ".
               "WHERE idPromotions = ".$prom_id.
             ")prom_time ".
             "JOIN promotionsDate ON ( promotionsDate.idPromotions = prom_time.idPromotions ) ";
            $arrProm_timelife = $this->exec_query_0($sql_time_life,'ALL');
            //var_dump($arrProm_timelife);
            // get timelife prom
            foreach($arrProm_timelife as $index) {
                // build timelife
                foreach($index as $date_=>$time_) {
                    // validate length of time_
                    // it must be 24 for represent 24hrs
                    if(strlen($time_) == 24){
                        $time_array = array();
                        // convert binary hr_string to hours   0= false 1=true
                        for($i = 0;$i<strlen($time_);$i++){
                            if($time_{$i})
                                $time_array[] = $i;
                        }
                    }
                    else{ continue;}
                    $time_life[$index["DATE"]] = $time_array;
                }
            }
            //var_dump($time_life);
            $result_["time_life"] = $time_life;

            //
            // determine if promotion ID belong to:
            //  1) shared or owned route
            //  3) shared promotion
            
            //
            // TODO
            //      when a Route is shared with an user, promotions assigned 
            //      to that route has to be assigned to the shared user to 
            //      matain data consistency
            $sql_route_assign  = "SELECT id,idRoute from promotionAssign where promotionAssign.idPromotions =".$prom_id." and promotionAssign.idUsr =".$user_id;
            $route_assign = $this->exec_query_0($sql_route_assign,'SEL');
            //var_dump($route_assign);

            // assign route id
            $result_["idRoute"] = (empty($route_assign["idRoute"])) ? "SHARED_PROM" : (int)$route_assign["idRoute"];

            if(!empty($route_assign["id"]) && isset($route_assign["id"]) ){
				// insert promotionAssignId and date in promotionReceive table
				$sql_promo_assign = "INSERT INTO `promotionReceive`(`id`, `idPromotionsAssing`, `date`) VALUES (0,".$route_assign["id"].",'".date('Y-m-d H:i:s')."')";
				$this->exec_query_0($sql_promo_assign,'INS');
            }
            else{            
                // assign promotion 
                $sql_assign_prom = "INSERT INTO   `promotionAssign` (`id`, `idPromotions`, `idRoute`, `idRoutePoint`, `idUsr`, `idpromotionShared`, `valid`) VALUES (NULL, ".$prom_id.", NULL, NULL, '".$user_id."', NULL, '1')"; 
                $assign_id = $this->exec_query_0($sql_assign_prom,'INS');
                if($assign_id > 0){
                    // set prom received
                    $sql_promo_assign = "INSERT INTO `promotionReceive`(`id`, `idPromotionsAssing`, `date`) VALUES (0,".$assign_id.",'".date('Y-m-d H:i:s')."')";
                    $receivedId = $this->exec_query_0($sql_promo_assign,'INS');
                    // received prom error
                    if($receivedId < 1){
                        $this->arrResultData['success'] = 'false';
                        $this->arrResultData['m'] = array("status"=>'Fatal error, promotion received error');
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
                        $arrPromotion = $this->getPromInformation($json["prom_id"],$arrResult["id"]);
                        if(empty($arrPromotion)) {
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = 'Invalid promotion id';
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
			 if(!array_key_exists("prom_id",$json) || empty($json['prom_id'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Promotion Id is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("prom_id"=>"Promotion Id is missing or Invalid index");
					$flag=1;
			 }else {
				
				if(!is_integer($json["prom_id"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid promo id';
					if($this->debug)
						$this->arrResultData['debug'][] = array("prom_id"=>"Promotion Id must be numeric format");
					$flag=1;
				}
			 }
			  
			 
			 if($flag) { return false; } else { return true;}
			 
		} // end of validateData()
	
	}
?>

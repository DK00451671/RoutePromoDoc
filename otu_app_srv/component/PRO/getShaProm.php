<?php
/*
* Business Logic 
* component : shareProm
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
   
	
	class getShaProm{
		// class variable
		public $arrResultData= array();
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

            if (!defined('SERVIDOR')) {
                require("component/dbconfig.php");
                require("component/logIn.php");
            }
		
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
            Description   : Function to get shared promotions ids 
			Function name : getSharedPromotions
			Input Parasm  : user id
			Output        : array
	     */
        public function getSharedPromotions($usr_id,$get_all=TRUE){

          
                $sql_get_prom_ids  = "SELECT promotionShared.id AS idPromSha, promotionShared.idPromotion, promotionShared.IdsharedpromoFrom, promotionShared.IdsharedpromoTo, promotionShared.token, promotionAssign.id AS idPromAss, promotionAssign.idUsr, promotionAssign.idpromotionShared, promotionViewed.id AS idViewed, promotionViewed.date AS dateViewed ".
                                     "FROM  `promotionShared` ".
                                     "JOIN promotions ON ( promotions.id = promotionShared.idPromotion ) ".
                                     "INNER JOIN promotionAssign ON ( promotionAssign.idPromotions = promotionShared.idPromotion ) ".
                                     "LEFT JOIN promotionViewed ON ( promotionAssign.id = promotionViewed.idPromotionsAssing ) ".
                                     "WHERE promotionShared.IdsharedpromoTo = ". $usr_id . "  " .
									 "AND promotionShared.valid = 1 " . 
                                     "AND promotionAssign.idUsr = ". $usr_id . "  " .
                                     "AND promotions.endDate > NOW( )  ".
                                     "AND promotions.cancelDate IS NULL ".
                                     "GROUP BY promotionShared.idPromotion";
                $prom_ids = $this->exec_query_0($sql_get_prom_ids,'ALL');
      
            //var_dump($prom_ids);
            if(!empty($prom_ids)){
                //
                $this->arrResultData['m'] = array();
                foreach($prom_ids as $prom){
                    if ($get_all)
                        $this->arrResultData['m'][] = (int)$prom["idPromotion"];
                    else
                        // get promotions not viewed
                        if($prom["idViewed"] === NULL)
                            $this->arrResultData['m'][] = (int)$prom["idPromotion"];
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
                        $arrPromotion = $this->getSharedPromotions($arrResult["id"]);
                        if(empty($arrPromotion)) {
							$this->arrResultData['success'] = 'true';
							$this->arrResultData['m'] = array();
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
			 
			 
			 if($flag) { return false; } else { return true;}
			 
		} // end of validateData()
	
	}
?>

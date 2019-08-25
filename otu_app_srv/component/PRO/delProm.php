<?php
/*
* Business Logic 
* component : delRou
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	
	class delProm{
	
		// class variable
		private $arrResultData= array();
		private $conexion;
		private $userId;
		private $arr_error_log = array();
		private $debug = false;
	
		/* 
			Description :Function to initialize object
			Function name : consultarIdUsuario
			Input Parasm : NA
			Output       : NA
	     */
		public function __construct() {
		
			$this->arrResultData = array("success" => "true", "m" => array());
			$this->conexion = mysqli_connect(SERVIDOR, USUARIO, CONTRASENA, BASEDATOS_0) or die (json_encode(array("success" => "false", "m" => "Fail to connect Database at server: " . SERVIDOR)));
			$this->Obj_logIn = new LogIn();
        
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
				case 'UPD':
				
				break;
			}
		}	
		
        function deletePromotion($json) {
            // check prom exist
            $sql_prom = "SELECT `id` FROM `promotions` WHERE id=".$json['prom_id'];
            $prom = $this->exec_query($sql_prom,'SEL');
			if(!empty($prom)) {        
				// check prom is shared
                $sql_prom = "SELECT id FROM  `promotionShared` WHERE idPromotion =".$json['prom_id']." AND IdsharedpromoTo =".$this->userId;
                $sql_prom_sharePromId = $this->exec_query($sql_prom,'SEL');
                if(isset($sql_prom_sharePromId['id']) && !empty($sql_prom_sharePromId)) { 
                    # is a own promotion
                    # set valid = 0 on promotionAssign table
	        	  	$sql_update_shared = "UPDATE `promotionShared` SET `valid`=0 WHERE `id`=".$sql_prom_sharePromId['id'];
		    	    $this->exec_query($sql_update_shared,'UPD');
                    //return TRUE;
                }
		    	
               
                    //user id own prom or not
					$sql_prom = "SELECT promotionAssign.id as id FROM promotions JOIN promotionAssign ON ( promotionAssign.idPromotions = promotions.id ) WHERE promotions.id = ".$json['prom_id']." AND promotionAssign.idUsr = ".$this->userId;
					$sql_prom_assignId = $this->exec_query($sql_prom,'ALL');
					if(isset($sql_prom_assignId) && !empty($sql_prom_assignId)) { 
						# is a own promotion
						# get all prom assign ids
						foreach ($sql_prom_assignId as $id_assign_prom) {
							# set valid = 0 on promotionAssign table
							$sql_update_shared = "UPDATE `promotionAssign` SET `valid`=0 WHERE `id`=".$id_assign_prom["id"];
							$this->exec_query($sql_update_shared,'UPD');
						}
						return TRUE;
					}
                    /*else{
                        //promo not shared
                        $this->arrResultData['success'] = 'false';
          				$this->arrResultData['m'] = 'Invalid prom';
		        		if($this->debug)
        					$this->arrResultData['debug'][] = array("user"=>"prom is not present in Database");
		        		return FALSE;
                    } 
					*/
	    		//prom shared 
            }//own prom
               
            else
			{
                # promotion not exist
			    $this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'Invalid prom';
				if($this->debug)
					$this->arrResultData['debug'][] = array("user"=>"prom is not present in Database");
				return FALSE;
			}
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
				$arrResult = $this->Obj_logIn->authenticateUser($json['user'],$json['passwd']);
					if(!empty($arrResult)){
						$this->userId = $arrResult['id'];
						if($this->deletePromotion($json)){
							   $this->arrResultData['success'] = 'true';
							   $this->arrResultData['m'] = 'prom remove successfully.'; 
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
		} // end of validarEstructura()
		
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
						$this->arrResultData['debug'][] = array("passwd"=>"passwd is missing or Invalid index");
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
					$this->arrResultData['m'] = 'invalid password';
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
						$this->arrResultData['debug'][] = array("prom_ide_id"=>"prom_id is missing or Invalid index");
					$flag=1;
			 }else
			 {
				if(isset($json['prom_id'])  && !is_integer($json['prom_id'])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'prom id must be numeric';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("prom_id"=>"prom id must be numeric");
					$flag=1;
				
				}
			 }
			
			 if($flag) { return false; } else { return true;}
			
		} // end of validateData()
			
	} // end of class()
?>

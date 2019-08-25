<?php
/*
* Business Logic 
* component : setPromTyp
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	class setPromTyp {
	
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
			$this->conexion = mysqli_connect(SERVIDOR, USUARIO, CONTRASENA, BASEDATOS_0) or die (json_encode(array("success" => "false", "m" => "Fail to connect Database at server: " . SERVIDOR)));
        
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
		 * Function to het all promo types
		 */
		function setUserPromoType($json) {
			
			$arrSubQuery= array();
			$commertialIds = array();
			
			$sql = "SELECT `id` FROM `commercialTypes` WHERE 1";
			$arrResult = $this->exec_query($sql,'ALL');
			
			foreach($arrResult as $val) {
				$commertialIds[] = $val['id'];
			}
			$sql_delete_all = "DELETE FROM promotionTypeUser WHERE `isUsr`=".$this->userId;
			$this->exec_query($sql_delete_all,'DEL');
			
			foreach ($json['types'] as $val) {
			   // check Commercial Ids already exist or not.
			   // added only missing/not-present Commercial Ids in DB.
			   /* $sqlcheckPromoType = "SELECT  ct.`name` , ct.`id` FROM `promotionTypeUser` as ptu INNER JOIN commercialTypes as ct ON  ct.id = ptu.idCommercialTypes WHERE ptu.isUsr=".$this->userId." AND ct.`id`=".$val;
				$arrTempPromo =   $this->exec_query($sqlcheckPromoType,'SEL');*/
				//if(empty($arrTempPromo)) {
					//validate commertial Ids
					if(in_array($val,$commertialIds)) 
						$arrSubQuery[]  = "(0,".$val.",".$this->userId.")";
					else{
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'invalid commercialtypes';
						if($this->debug)
							$this->arrResultData['debug'][] = array("types"=>"Invalid commercial types value.");
						return false;
					}
				//}
			}	
			if(!empty($arrSubQuery)) {
			    // commertail Ids are new then insert
				$sqlPromotionUser = " INSERT INTO `promotionTypeUser`(`id`, `idCommercialTypes`, `isUsr`) VALUES ".implode(",",$arrSubQuery);	
				if($this->debug) 
					$this->arrResultData['mysql'][] = $sqlPromotionUser;
				if($this->exec_query($sqlPromotionUser,'INS')){ 			
					unset($this->arrResultData['m']);
					return true;
				}
				else
					return false;
			}
            else {
			   // All commetial Ids are already present. for data ambiguity stop to insert the duplicate records in DB .
				unset($this->arrResultData['m']);
				return true;
			}			
		} // end of getPromType()
		
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
					$this->setUserPromoType($json);
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
				
				// check passwd index
			 if(!array_key_exists("types",$json) || empty($json['types'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'types is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("types"=>"commertial types are missing");
					$flag=1;
			 }else
			 {
				foreach ($json['types'] as $value) {
					if(!is_int($value)){
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'invalid commertial types';
						if($this->debug)
							$this->arrResultData['debug'][] = array("types"=>"commertial types  must be numeric value");
						$flag=1;
						break;
					}
			    }
			}
				
			if($flag) { return false; } else { return true;}
		}
		
	}
?>
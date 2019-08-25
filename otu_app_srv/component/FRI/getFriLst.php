<?php
/*
* Business Logic 
* component : getFriLst
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
   
	class getFriLst{
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
		
		public function getFriendList($user_id) {
		   
		   $arrData = array();
		   
		   //Check friend request present or not
		   $sql_check = "SELECT  frnd.`accepted`, frnd.`valid`,usr.id,usr.name FROM `friends` as frnd, usr as usr WHERE frnd.`idUsr`=".$user_id." AND  frnd.idFriend=usr.id";
		   $arrChkData = $this->exec_query($sql_check,'ALL');
		   
		   if(!empty($arrChkData)) {
		      foreach($arrChkData as $kk=>$val) {
				if($arrChkData[$kk]['accepted']==1 && $arrChkData[$kk]['valid']==1)
					$arrData['friends'][$arrChkData[$kk]['id']] = $arrChkData[$kk]['name'];
					
				/*if($arrChkData[$kk]['accepted']==0 && $arrChkData[$kk]['valid']==1)
					$arrData['requests'][$arrChkData[$kk]['id']] = $arrChkData[$kk]['name'];*/
				}
			}
			
			$sql_check_req = "SELECT  frnd.`accepted`, frnd.`valid`,usr.id,usr.name FROM `friends` as frnd, usr as usr WHERE frnd.`idFriend`=".$user_id." AND  frnd.idUsr =usr.id";
			
			$arrChkData_req = $this->exec_query($sql_check_req,'ALL');
						
			if(!empty($arrChkData_req)) {
		      
			  foreach($arrChkData_req as $kk=>$val) {
				if($arrChkData_req[$kk]['accepted']==0 && $arrChkData_req[$kk]['valid']==1)
					$arrData['requests'][$arrChkData_req[$kk]['id']] = $arrChkData_req[$kk]['name'];
				if($arrChkData_req[$kk]['accepted']==1 && $arrChkData_req[$kk]['valid']==1)
					$arrData['friends'][$arrChkData_req[$kk]['id']] = $arrChkData_req[$kk]['name'];
			    }
			}
			
			if(empty($arrData['friends']))
					$arrData['friends']= array();
			if(empty($arrData['requests']))
					$arrData['requests']= array();
			$this->arrResultData['m'] = $arrData;
			
		  
/* JRICO: diseable this part not use it
            if(empty($arrData['friends']) && empty($arrData['requests']) ) {
				// no friend and friend request assign
				$arrData['friends']= array();
				$arrData['requests']= array();
               	$this->arrResultData['m'] = $arrData;
}*/
		    return TRUE;
		}
		
		/*
		* Function to validata the request data
		*/
		
		public function validarEstructura($jsonEntrada) {
		
			$json = $jsonEntrada['FRI'];
			$arrResult = array();
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			if($this->debug)
				$this->arrResultData['debug'][] = array("send query"=>$json);
			if($this->validateData($json)) {
			
				$Obj_logIn= new LogIn();
				$arrResult = $Obj_logIn->authenticateUser($json['user'],$json['passwd']);
				if(!empty($arrResult) && !$arrResult['is_non_register']) {
					$this->userId = $arrResult['id'];
						$this->getFriendList($this->userId);
				}
				else {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid user or password';
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
				
			if($flag) { return false; } else { return true;}
			
		} //end of validateData()
	}

?>

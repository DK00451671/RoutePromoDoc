<?php
/*
* Business Logic 
* component : getMyRouLst   
*/
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
	
	class getNoti{
		
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
			$this->conexion = mysqli_connect(SERVIDOR, USUARIO, CONTRASENA, BASEDATOS) or die (json_encode(array("success" => "false", "m" => "Fail to connect Database at server: " . SERVIDOR)));
        
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
		
	
		
		public function validarEstructura($jsonEntrada) {
			$json = $jsonEntrada['NOT'];
			$arrResult = array();
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			if($this->debug)
				$this->arrResultData['debug'][] = array("send query"=>$json);
				
				if($this->validateData($json)) {
				
					$Obj_logIn= new LogIn();
					$arrResult = $Obj_logIn->authenticateUser($json['user'],$json['passwd']);
                    $user_id = $arrResult['id'];

					if(!empty($arrResult)){
                        //var_dump($arrResult);
                        $this->arrResultData['success'] = 'true';


                        # get shared routes
                        require("component/ROU/getMyRouLst.php");
                        $obj_getMyRouLst = new getMyRouLst();
                        # get route list 
                        if( $obj_getMyRouLst->getUserRoute($user_id)){
                            $output["ROU"] = array();
                            #iterate shared route list 
                            foreach ( $obj_getMyRouLst->arrResultData['m']['shared'] as &$shared_route){
                                #select only routes are not accepted
                                if(!$shared_route["accepted"])
                                    $output["ROU"][] = $shared_route;
                            }
                        }
                        else
                            $output["ROU"] = array();
                        # var_dump($output["ROU"]);

                        # get friendship request
                        require("component/FRI/getFriLst.php");
                        $obj_getFriLst = new getFriLst();
                        $obj_getFriLst->getFriendList($user_id);
                        $output["FRI"] =  $obj_getFriLst->arrResultData['m']['requests'];
                        
                        # get shared promotions
                        require("component/PRO/getShaProm.php");
                        $obj_getShaProm = new getShaProm();
                        // get shared promotions that are not viewed
                        $obj_getShaProm->getSharedPromotions($user_id,FALSE);
                        $output["PRO"] = $obj_getShaProm->arrResultData['m'];


                        $this->arrResultData['m'] = $output;                        
               			return $this->arrResultData;
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
			 }else {
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
						$this->arrResultData['debug'][] = array("user"=>"passwd is missing or Invalid index");
					$flag=1;
			 }else {
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

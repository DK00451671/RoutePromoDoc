<?php
/*
* Business Logic 
* component : shareRou
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
    include("lib/getToken.php");
	
	class shareRou{
		// class variable
		private $arrResultData= array();
		private $conexion;
		private $conexion_0;
		private $userId;
		private $arr_error_log= array();
		private $debug = false;
		private $share_user_route_id;
		private $route_id;
		private $Obj_logIn = NULL;
        private $Obj_token = NULL;
        private $token = NULL;
        private $arrUserId = array();

		/* 
			Description :Function to initialize object
			Function name : consultarIdUsuario
			Input Parasm : NA
			Output       : NA
	     */
		public function __construct() {
		
			$this->arrResultData = array("success" => "true", "m" => array());
			$this->conexion = mysqli_connect(SERVIDOR, USUARIO, CONTRASENA, BASEDATOS) or die (json_encode(array("success" => "false", "m" => "Fail to connect Database at server: " . SERVIDOR)));
			
			// connection for database_0
			$this->conexion_0 = mysqli_connect(SERVIDOR, USUARIO, CONTRASENA, BASEDATOS_0) or die (json_encode(array("success" => "false", "m" => "Fail to connect Database at server: " . SERVIDOR)));
			
			$this->share_user_route_id=0;
			$this->route_id =0;
			$this->userId =0;
			$this->Obj_logIn = new LogIn();
			$this->Obj_token = new getToken();

        
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
		
		
		/* User Authenticate 
		 * input : username and password 
		 */
		public function checkRegAndNonRegUsr($json)
        {
			$user     = $json['user'];
			$password = $json['passwd'];
			if(isset($json['share_rou_to_user_name']) &&  !empty($json['share_rou_to_user_name'])) {
				$share_rou_to = $json['share_rou_to_user_name'];
			}
			if(isset($json['share_rou_to_user_id']) &&  !empty($json['share_rou_to_user_id'])) {
				$share_rou_to_id = $json['share_rou_to_user_id'];
			}
			$account_type = $json['account_type'];
			
			$route_id = $json['route_id'];
			$arrUserId = array();
			// first check user is register user or its third party user
			{
				$query = "SELECT id, name, gender, age FROM usr WHERE user_name = '@U' AND password = '@P'";
				$query = str_replace("@U", $user, $query);
				$query = str_replace("@P", $password, $query);
				$result = mysqli_query($this->conexion, $query) or die (json_encode(array("success" => "false", "m" => "Error al ejecutar el query: " . $query)));
				$arrData = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$this->arrUserId['user_id'] = isset($arrData['id']) ? $arrData['id'] : 0;
				// To make new entry for non -register user 
				if(count($arrData) == 0) {
					//start : register 3rd party user
						// check user is already exist or not
						$check_third_party_usr = "SELECT id,user_name FROM usr WHERE user_name = '".$user."'";
						$arrTrdUsr = $this->exec_query($check_third_party_usr,'SEL');
						$this->arrUserId['user_id']  = isset($arrTrdUsr['id']) ? $arrTrdUsr['id'] : 0;
						if(count($arrTrdUsr) == 0) {
							$query = "INSERT INTO `usr`(`id`, `name`, `age`, `gender`, `user_name`, `password`, `account_type_id`,`registerDate`, `is_non_register`) VALUES (0,'',0,'','".$user."','XXXX','".$account_type."','".date('Y-m-d H:i:s')."','1')";
							$this->arrUserId['user_id'] = $this->exec_query($query,'INS');
							
						} //end of if
						//echo $thirdparty_user_id; exit;
					//end  : register 3rd party user
				}
            }
			// check the share_rou_to is register user or third party user.
			if(isset($share_rou_to) && !empty($share_rou_to)){
				$share_rou_to_query = "SELECT id,user_name FROM usr WHERE user_name = '".$share_rou_to."'";
				$arr_share_route_to = $this->exec_query($share_rou_to_query,'SEL');
				$this->arrUserId['idUsr_ToShareRouteWith'] = isset($arr_share_route_to) ? $arr_share_route_to['id'] : 0;
				
				if(count($arr_share_route_to) == 0){
					$reg_shareRou_id_query = "INSERT INTO `usr`(`id`, `name`, `age`, `gender`, `user_name`, `password`, `account_type_id`,`registerDate`, `is_non_register`) VALUES (0,'',0,'','".$share_rou_to."','XXXX','".$account_type."','".date('Y-m-d H:i:s')."',1)";
					$this->arrUserId['idUsr_ToShareRouteWith'] = $this->exec_query($reg_shareRou_id_query,'INS');
				}
				
			 } else{
					if(isset($share_rou_to_id) && !empty($share_rou_to_id)) {
						$share_rou_to_query = "SELECT id,user_name FROM usr WHERE id = '".$share_rou_to_id."'";
						$arr_share_route_to = $this->exec_query($share_rou_to_query,'SEL');
						$this->arrUserId['idUsr_ToShareRouteWith'] = isset($arr_share_route_to) ? $arr_share_route_to['id'] : 0;
						if(count($arr_share_route_to) == 0){
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = "invalid share_rou_to_user_id";
							return FALSE;
						}
					}		 
			 }
			// to check the route is sharable route or user having its own route to share 
			{	
				//below query is used to find wheather route is belongs to user or not
				$route_Owner = "SELECT urou.id  from usrroutes as urou , usr as u where urou.idUsr = u.id AND u.user_name ='".$user."' AND urou.id=".$route_id;
				$arrRoute_own = $this->exec_query($route_Owner,'SEL');
				
				if(count($arrRoute_own) == 0) {
				   // if route is not belong to user then check route is sharable or not 
					$shared_route_Owner = "SELECT srou.id,srou.idUsrRoutes FROM sharedroutes as srou , usr as u where srou.idUsr_ToShareRouteWith = u.id AND u.user_name = '".$user."' AND  srou.idUsrRoutes =".$route_id;
					$arrRoute_own = $this->exec_query($shared_route_Owner,'SEL');
					if(!empty($arrRoute_own)) {
						$this->arrUserId['route_own'] = isset($arrRoute_own['id']) ? $arrRoute_own['id'] :0;
						$this->arrUserId['rou_is_sharable'] = 1;
					}	
					 else  {
							$this->arrUserId['route_own'] = 0;
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = 'route not present.';
							return FALSE;
						}
				}else
				{
					$this->arrUserId['route_own'] = isset($arrRoute_own['id']) ? $arrRoute_own['id'] :0;
					$this->arrUserId['rou_is_sharable'] = 0;
				}
			 }
	     	$this->arrUserId['idUsrRoutes'] = $route_id;
			$this->arrUserId['account_type'] = isset($json['account_type']) ? $json['account_type'] : 0;
			if(isset($json['share_rou_to_user_name']))
				$this->arrUserId['share_rou_to_user_name'] =  $json['share_rou_to_user_name'];
			if(isset($json['share_rou_to_user_id']))
				$this->arrUserId['share_rou_to_user_id'] =  $json['share_rou_to_user_id'];
			if (empty($this->arrUserId)) {
					return array();
			}
			else { 	
			
				 return $this->arrUserId;
			}
        } //end of consultarIdUsuario
		
		/*
		 * function to share route
		 *  use cases : 
		   1. share route with register user with register user
		   2. share route with register user with non-register user
		   3. share route with non-register user with register user
		   4. share route with non-register user with non-register user
			Busniness logic :
				condition  1 : Non register user must be present in user table.
				condition  2 : Always keep track for chane of  route part from end to owner of route.
			
			Enjoy the coding...
		 */
		public function shareRoute($json) {
		 
		$arrPromAssign = array();
		if(!isset($json["share_rou_to_user_name"]) && !isset($json["share_rou_to_user_id"])) {
			    
			$arrShareRou = array();
			//$json['route_own'] = $json['user_id'];
			$sql_check  = "SELECT `id`, `idUsrRoutes`, `idUsr`, `idUsr_ToShareRouteWith`, `idAccountType`, `route_owner`, `date`, `token`, `accepted`, `valid` FROM `sharedroutes` WHERE `idUsrRoutes`= ".$json['idUsrRoutes']."  AND  `idAccountType`= ".$json['account_type']." AND `idUsr`= ".$json['user_id'];
			
			$arrCheck = $this->exec_query($sql_check,'SEL');
			if(!empty($arrCheck)){
				    $this->arrResultData['success'] = 'true';
					$this->arrResultData['m'] = array(
												"status"=>"already shared",
												"token"=>$arrCheck['token']);
				return true;
			}
		}
         else {		 
			$sql_check  = "SELECT `id`, `idUsrRoutes`, `idUsr_ToShareRouteWith`,`token`  FROM `sharedroutes` WHERE idUsrRoutes=".$json['idUsrRoutes']." AND idUsr_ToShareRouteWith=".$json['idUsr_ToShareRouteWith']." AND idUsr=".$json['user_id'];
		
			$arrShareRou = $this->exec_query($sql_check,'SEL');
		}
		
        if(count($arrShareRou) < 1) {
            //
            // generate token 
            //
            $this->token = $this->Obj_token->generate(TOKENSIZE);
            //
            // validate token not exist
            //
			$sql_valid_token = "SELECT token FROM sharedroutes where token = '".$this->token."'" ;
            $token_sql_result = $this->exec_query($sql_valid_token,'SEL');
            if (!empty($token_sql_result)){
                $this->arrResultData['success'] = 'false';
                $this->arrResultData['m'] =  array(
                       "status"=>"please try again, could not generate token");
	    		return FALSE;
            }
            
			
            //
            // insert token
            //
			if(!isset($json["share_rou_to_user_name"]) && !isset($json["share_rou_to_user_id"])) { 
				$sql_share_route = "INSERT INTO `sharedroutes`(`id`, `idUsrRoutes`,`idUsr`,`idAccountType`, `date`,`token`,`accepted`,`valid`) VALUES (0,".$json['idUsrRoutes'].",".$json['user_id'].",".$json['account_type'].",'".date('Y-m-d H:i:s')."','".$this->token."',0,1)";
				
			} else {
				$sql_share_route = "INSERT INTO `sharedroutes`(`id`, `idUsrRoutes`,`idUsr`, `idUsr_ToShareRouteWith`,`idAccountType`,`route_owner`, `date`,`token`,`accepted`,`valid`) VALUES (0,".$json['idUsrRoutes'].",".$json['user_id'].",".$json['idUsr_ToShareRouteWith'].",".$json['account_type'].",".$json['route_own'].",'".date('Y-m-d H:i:s')."','".$this->token."',0,1)";
				
			}
				$route = $this->exec_query($sql_share_route,'INS');
			//update shared route counter
			$sql_route_from_counter = "SELECT `id`, `route_id`, `shared_count`, `usages_count` FROM `routecount` WHERE route_id=".$json['idUsrRoutes'] ;
			$arrRouteCount = $this->exec_query($sql_route_from_counter,'SEL');
			if(count($arrRouteCount)>0) {
				$sql_updated_counter = "UPDATE `routecount` SET `shared_count`=".($arrRouteCount['shared_count']+1)." WHERE route_id=".$json['idUsrRoutes'];
				$this->exec_query($sql_updated_counter,'UPD');
			}
			
			
			
			// Assign route promotions to shared user
			$sql_promotions_assign  = "SELECT `id`, `idPromotions`, `idRoute`, `idRoutePoint`, `idUsr`, `valid` FROM `promotionAssign` WHERE `idRoute`= ".$json['idUsrRoutes']."  AND `idUsr`=".$json['user_id']. "  AND valid=1 " ;
			$route_promotions = $this->exec_query_0($sql_promotions_assign,'ALL');
			
			if( is_array($route_promotions) && !empty($route_promotions))	{		
				if(!isset($json["share_rou_to_user_name"]) && !isset($json["share_rou_to_user_id"])) {
					$sql_insert_promotions_assign = 'INSERT INTO `promotionAssign`(`id`, `idPromotions`, `idRoute`, `idRoutePoint`,`valid`) VALUES ';
				}else {
					$sql_insert_promotions_assign = 'INSERT INTO `promotionAssign`(`id`, `idPromotions`, `idRoute`, `idRoutePoint`, `idUsr`, `valid`) VALUES ';
				}
				foreach($route_promotions as $kk=>$val) {
					if(!isset($json["share_rou_to_user_name"]) && !isset($json["share_rou_to_user_id"])) {
						$arrPromAssign[] = "(0,".$route_promotions[$kk]['idPromotions'].",".$json['idUsrRoutes'].",".$route_promotions[$kk]['idRoutePoint'].",1) ";
					} else {
						$arrPromAssign[] = "(0,".$route_promotions[$kk]['idPromotions'].",".$json['idUsrRoutes'].",".$route_promotions[$kk]['idRoutePoint'].",".$json['idUsr_ToShareRouteWith'].",1) ";
					}
				}
				$sql_insert_promotions_assign .= implode(" , ", $arrPromAssign);
				$this->exec_query_0($sql_insert_promotions_assign,'INS');
			}
            if($route > 0)
                return TRUE;
			else
				return FALSE;
		}else {
			$this->arrResultData['success'] = 'true';
            $this->arrResultData['m'] = array(
                       "status"=>"already shared",
                       "token"=>$arrShareRou['token']);
			return FALSE;
		} 		   
	}

        /*
		* Function to send friendship request 
		*/
        public function send_friend_request($json){

            if(isset($this->arrUserId['idUsr_ToShareRouteWith']) && !empty($this->arrUserId['idUsr_ToShareRouteWith'])) {

                require('component/FRI/sendFriReq.php');
                $sendFri_obj = new sendFriReq();
                $sendFri_json = array("FRI"=>
                                            array(
                                                 "user" => $json["user"],
                                                 "passwd" => $json["passwd"],
                                                 "user_id" => (int)$this->arrUserId['idUsr_ToShareRouteWith']
                                                 )
                                     );
                //var_dump($sendFri_json);
                $sendFri_result = $sendFri_obj->validarEstructura($sendFri_json);
                //var_dump($sendFri_result);
                return $sendFri_result;
            }
            else{ 
                
                return array("success" => "fail",
                             "m"=>"send friendship request fail");
            }

        }
		
		/*
		* Function to validata the request data
		*/
		
        public function validarEstructura($jsonEntrada) {

			$json = $jsonEntrada['ROU'];
			$arrResult = array();
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			if($this->debug)
				$this->arrResultData['debug'][] = array("send query"=>$json);
				if($this->validateData($json)) {
					$arrResult = $this->checkRegAndNonRegUsr($json);
					if(!empty($arrResult)){
							if($this->shareRoute($arrResult)){
							    if(empty($this->arrResultData['m'])){
                                    // send friend request to share_to_user_name/id
                                    $send_fri_result = $this->send_friend_request($json); 
									$this->arrResultData['success'] = 'true';
									$this->arrResultData['m'] = array(
                                                             "status"=>'Share route successfully'.$send_fri_result['m'],
                                                             "token" => $this->token);
								}
							}
					}
					else{
						if(empty($this->arrResultData)) {
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = 'Invalid user or password';
						}
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
				$this->arrResultData['m'] = 'route_id is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("route_id"=>"route_id is missing or Invalid index");
					$flag=1;
			 }else {
				
				if(!is_integer($json["route_id"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid route id';
					if($this->debug)
						$this->arrResultData['debug'][] = array("route_id"=>"route id must be numeric format");
					$flag=1;
				}
			   else {
			  
				// to check route is shated route or user having its own route.
				
				   //logic to check wheather route belongs to user or not.
					/*if(isset($json['user']) && isset($json['passwd'])) {
						$arrResult = $this->Obj_logIn->authenticateUser($json['user'],$json['passwd']);
						if(!empty($arrResult)) {
							$this->userId = $arrResult['id'];
							$sql_route = "SELECT `id`, `idUsr`, `route_name`, `active` FROM `usrroutes` WHERE id=".$json['route_id']." AND  idUsr=".$this->userId;
							$route = $this->exec_query($sql_route,'SEL');
							
							if(empty($route)) {
								$this->arrResultData['success'] = 'false';
								$this->arrResultData['m'] = 'Invalid route';
							   if($this->debug)
									$this->arrResultData['debug'][] = array("route_id"=>"route is not present in Database");
								$flag=1;
							}
							else
							  $this->route_id = $route['id'];
						}
					}*/
				}
			}
			  
			  // check account_type index
			if(!array_key_exists("account_type",$json) || empty($json['account_type'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'account tye missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("account_type"=>"account_type is missing or Invalid index");
					$flag=1;
			 }
			 else
			 {
				if(!is_integer($json["account_type"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid account_type value';
					if($this->debug)
						$this->arrResultData['debug'][] = array("account_type"=>"Invalid account_type value must be proper format");
					$flag=1;
				} else {
				$sql_account_type = "SELECT `id`, `name` FROM `accounttype` WHERE id=".$json['account_type'];
				$arrAccountType = $this->exec_query($sql_account_type,'SEL');
				if(empty($arrAccountType)) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'miss match account type';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("account_type"=>"Account Type is not present in Database");
					$flag=1;
				}
			   }
			 }
			 
			  // check share_rou_to_user_name index
			if(isset($json["share_rou_to_user_name"])) {
			 if(!array_key_exists("share_rou_to_user_name",$json) || empty($json['share_rou_to_user_name'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'share route id is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("share_rou_to_user_name"=>"share_rou_to_user_name is missing or Invalid index");
					$flag=1;
			 }else
			 {
				if(is_integer($json["share_rou_to_user_name"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid share_rou_to_user_name value';
					if($this->debug)
						$this->arrResultData['debug'][] = array("share_rou_to_user_name"=>"Invalid share_rou_to_user_name value must be proper format");
					$flag=1;
				}
				else {
			 				   
					/*$sql_shareRou = "SELECT `id`, `name`,  `user_name`, `account_type_id`, `registerDate` FROM `usr` WHERE user_name='".$json['share_rou_to']."'";
					$arrShareRou = $this->exec_query($sql_shareRou,'SEL');
					if(empty($arrShareRou)) {
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'Invalid share_rou_to value';
					   if($this->debug)
							$this->arrResultData['debug'][] = array("user"=>"share route Invalid. not present in database.");
						$flag=1;
					}else
						$this->share_user_route_id = $arrShareRou['id'];*/
				}
			 }
			}
			
			
			  // check share_rou_to_user_id index
			if(isset($json["share_rou_to_user_id"])) {
				 if(!array_key_exists("share_rou_to_user_id",$json) || empty($json['share_rou_to_user_id'])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'share route id is missing';
					   if($this->debug)
							$this->arrResultData['debug'][] = array("share_rou_to_user_id"=>"share_rou_to_user_id is missing or Invalid index");
						$flag=1;
				 }else
				 {
					if(!is_integer($json["share_rou_to_user_id"])) {
						$this->arrResultData['success'] = 'false';
						$this->arrResultData['m'] = 'Invalid share_rou_to_user_id value';
						if($this->debug)
							$this->arrResultData['debug'][] = array("share_rou_to_user_id"=>"Invalid share_rou_to_user_id value must be proper format");
						$flag=1;
					}
					else {
								   
						/*$sql_shareRou = "SELECT `id`, `name`,  `user_name`, `account_type_id`, `registerDate` FROM `usr` WHERE user_name='".$json['share_rou_to']."'";
						$arrShareRou = $this->exec_query($sql_shareRou,'SEL');
						if(empty($arrShareRou)) {
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = 'Invalid share_rou_to value';
						   if($this->debug)
								$this->arrResultData['debug'][] = array("user"=>"share route Invalid. not present in database.");
							$flag=1;
						}else
							$this->share_user_route_id = $arrShareRou['id'];*/
					}
				 }
			}
			if($json["account_type"]==1 && !isset($json["share_rou_to_user_name"]) && !isset($json["share_rou_to_user_id"])) {
			    $this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'missing share_rou_to_user_name or share_rou_to_user_id';
				if($this->debug)
				$this->arrResultData['debug'][] = array("share_rou_to_user_id_or_name"=>"missing share_rou_to_user_name or share_rou_to_user_id");
					$flag=1;    
			 }
			 
			 if($flag) { return false; } else { return true;}
			 
		} // end of validateData()
	
	}
?>

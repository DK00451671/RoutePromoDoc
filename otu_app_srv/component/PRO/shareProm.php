<?php
/*
* Business Logic 
* component : shareProm
*/
    header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require("component/dbconfig.php");
    require("component/logIn.php");
    include("lib/getToken.php");
	
	class shareProm{
		// class variable
		private $arrResultData= array();
		private $conexion;
		private $conexion_0;
		private $userId;
		private $debug = false;
		private $Obj_logIn = NULL;
		private $arrUserId = array();
        private $Obj_token = NULL;
        private $token = NULL;
		
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
			$this->Obj_logIn = new LogIn();
            $this->Obj_token = new getToken();
        
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
			Description   : Function to check user is reg or not 
			Function name : checkRegAndNonRegUsr
			Input Parasm  : Json_input_array
			Output        : array
	     */
		public function checkRegAndNonRegUsr($user_id,$json)
        {

            $prom_id = 	$json['prom_id'];	
            $this->arrUserId['user_id'] = (int)$user_id;

            //
            // Logic to check the promoAssion Id is valid or not
            //
			{
				$sql_promo_assign  = "SELECT `id`, `idCompany`, `message`, `registredDate`, `endDate`, `cancelDate` FROM `promotions` WHERE `id`=".$prom_id;
				
				$arr_promo_assign_id = $this->exec_query_0($sql_promo_assign,'SEL');
			
				if(isset($arr_promo_assign_id['id']) && !empty($arr_promo_assign_id))
					$this->arrUserId['promoAssignId'] = $arr_promo_assign_id['id'];
				else {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid promotion ID';
					return false;
				}
			}
            $this->arrUserId['idSocNetworkType'] = $json['soc_network_type'];

		    //
            //slect share tarjet
            //
			//$share_rou_to = isset($json['share_to_user_name']) ? $json['share_to_user_name']: $json['share_to_user_id'];
			if(isset($json['share_to_user_name']) &&  !empty($json['share_to_user_name'])) {
				$share_rou_to = $json['share_to_user_name'];
			}
            else if(isset($json['share_to_user_id']) &&  !empty($json['share_to_user_id'])) {
				$share_rou_to_id = $json['share_to_user_id'];
            }
            // identify public promotion 
            else{
                $this->arrUserId["share_rou_to_id"]  = "public_promotion"; 
                return $this->arrUserId;
            }
			
			
			// check the share_to is register user or not.
			if(isset($share_rou_to) && !empty($share_rou_to)){
				$share_rou_to_query = "SELECT id,user_name FROM usr WHERE user_name = '".$share_rou_to."'";
				$arr_share_route_to = $this->exec_query($share_rou_to_query,'SEL');
				$this->arrUserId['idUsr_ToSharePromoWith'] = isset($arr_share_route_to) ? $arr_share_route_to['id'] : 0;
				if(count($arr_share_route_to) == 0){
                    // user is not registered
                    // insert share to user name as non-register user on database and return id
                    $reg_shareRou_id_query = "INSERT INTO `usr`(`id`, `name`, `age`, `gender`, `user_name`, `password`, `account_type_id`,`registerDate`, `is_non_register`) VALUES (0,'',0,'','".$share_rou_to."','XXXX','".$json['soc_network_type']."','".date('Y-m-d H:i:s')."',1)";
                    $this->arrUserId['idUsr_ToSharePromoWith'] = $this->exec_query($reg_shareRou_id_query,'INS');

					//$this->arrResultData['success'] = 'false';
    				//$this->arrResultData['m'] = "invalid share_rou_to_user_name";
					//return FALSE;
				}
				
			}else{
                // get id from tarjet 
				if(isset($share_rou_to_id) && !empty($share_rou_to_id)) {
						$share_rou_to_query = "SELECT id,user_name FROM usr WHERE id = '".$share_rou_to_id."'";
						$arr_share_route_to = $this->exec_query($share_rou_to_query,'SEL');
						$this->arrUserId['idUsr_ToSharePromoWith'] = isset($arr_share_route_to) ? $arr_share_route_to['id'] : 0;
						if(count($arr_share_route_to) == 0){
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = "invalid share_rou_to_user_id";
							return FALSE;
						}
				}
			}
			
			
			if (empty($this->arrUserId)) {
					return array();
			}
			else { 	
			
				 return $this->arrUserId;
			}
        } //end of consultarIdUsuario
		
		/*
		 * function to share promotion
		 *  use cases : 
		   1. share route with register user with register user
		 	Busniness logic :
				condition  1 : only register user can shared the promotion
		*/
		public function sharePromo($json) {
		 
            //calculate token
            $this->token = $this->Obj_token->generate(TOKENSIZE);

            // share public promotion 
            if($json["share_rou_to_id"] === "public_promotion"){
                //echo "public_promotion";

                //promotion is already public 
                $sql_check  = "SELECT `id`, `idPromotion`, `idSocNetworkType`, `date`, `IdsharedpromoFrom`, `IdsharedpromoTo`, `token` FROM `promotionShared` WHERE `IdsharedpromoFrom` =".$json['user_id']."  AND `IdsharedpromoTo` is NULL  AND `idSocNetworkType`= ".$json['idSocNetworkType']." AND `idPromotion` =".$json['promoAssignId'];
                //echo $sql_check . "\n";
	    	    $arrShareRou = $this->exec_query_0($sql_check,'SEL');
		
    	    	if(count($arrShareRou) > 0) {
	        		$this->arrResultData['success'] = 'false';
                    $this->arrResultData['m'] = array("status"=>'promotion already public',
                                                      "token"=>$arrShareRou["token"]);
			        return FALSE;
    		    } 		   
                
                //insert shared promotiono on database
                $sql_share_promo = "INSERT INTO `promotionShared`(`id`, `idPromotion`, `idSocNetworkType`, `date`, `IdsharedpromoFrom`, `IdsharedpromoTo`,`token`) VALUES (0,".$json['promoAssignId'].",".$json['idSocNetworkType'].",'".date('Y-m-d H:i:s')."',".$json['user_id'].","."NULL".',"'.$this->token.'")';
                //echo $sql_share_promo . "\n";
                $route = $this->exec_query_0($sql_share_promo,'INS');
                $this->arrResultData['success'] = 'true';
                $this->arrResultData['m'] = array("status"=>'promotion is public',
                                                  "token"=>$this->token);

                return FALSE;
            }
            else{
                $sql_check  = "SELECT `id`, `idPromotion`, `idSocNetworkType`, `date`, `IdsharedpromoFrom`, `IdsharedpromoTo`,`token` FROM `promotionShared` WHERE `IdsharedpromoFrom` =".$json['user_id']."  AND `IdsharedpromoTo` =".$json['idUsr_ToSharePromoWith']." AND `idSocNetworkType`= ".$json['idSocNetworkType']." AND `idPromotion` =".$json['promoAssignId'];
	    	    $arrShareRou = $this->exec_query_0($sql_check,'SEL');
		
    	    	if(count($arrShareRou) < 1) {
	    	    	$sql_share_promo = "INSERT INTO `promotionShared`(`id`, `idPromotion`, `idSocNetworkType`, `date`, `IdsharedpromoFrom`, `IdsharedpromoTo`,`token`) VALUES (0,".$json['promoAssignId'].",".$json['idSocNetworkType'].",'".date('Y-m-d H:i:s')."',".$json['user_id'].",".$json['idUsr_ToSharePromoWith'].',"'.$this->token.'")';
			
    		    	$route = $this->exec_query_0($sql_share_promo,'INS');
                    if($route > 0)  {
                        // assign promotion 
                        $sql_assign_prom = "INSERT INTO   `promotionAssign` (`id`, `idPromotions`, `idRoute`, `idRoutePoint`, `idUsr`, `idpromotionShared`, `valid`) VALUES (NULL, ".$json['promoAssignId'].", NULL, NULL, '".$json['idUsr_ToSharePromoWith']."', '".$route."', '1')"; 
                        $assign_id = $this->exec_query_0($sql_assign_prom,'INS');
                        if($assign_id > 0){
                            $this->arrResultData['success'] = 'true';
                            $this->arrResultData['m'] = array("status"=>'shared promotion',
                                                              "token"=>$this->token);
                            return TRUE;                            
                        }
                        else{
                            $this->arrResultData['success'] = 'false';
                            $this->arrResultData['m'] = array("status"=>'Fatal error, promotion not assigned');
                            return FALSE;
                        }

                    }
                    else{
                        $this->arrResultData['success'] = 'false';
                        $this->arrResultData['m'] = array("status"=>'Fatal error, promotionShared not inserted');
                        return FALSE;
                    }
        		}else {
	        		$this->arrResultData['success'] = 'false';
		        	$this->arrResultData['m'] = array("status"=>'you already shared these promotion with your friend!',
                                                  "token"=>$arrShareRou["token"]);
			        return FALSE;
    		    } 		   
            }
        }


        /*
		* Function to send friendship request 
		*/
        public function send_friend_request($json){
            require('component/FRI/sendFriReq.php');
            $sendFri_obj = new sendFriReq();
            $sendFri_json = array("FRI"=>
                                        array(
                                             "user" => $json["user"],
                                             "passwd" => $json["passwd"],
                                             "user_id" => (int)$this->arrUserId['idUsr_ToSharePromoWith']
                                             )
                                 );
            //var_dump($sendFri_json);
            $sendFri_result = $sendFri_obj->validarEstructura($sendFri_json);
            //var_dump($sendFri_result);
            return $sendFri_result;

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
					    $arrResult = $this->checkRegAndNonRegUsr($arrResult['id'],$json);
                        //var_dump($arrResult);
                        if(!empty($arrResult)){                        
                            if($this->sharePromo($arrResult)){
                              // send friend request to share_to_user_name/id
                              $send_fri_result = $this->send_friend_request($json);
						      $this->arrResultData['m']["status"] = $this->arrResultData['m']["status"] . $send_fri_result['m'];
                            }
                        }
                    }
                    else{
                        // login error
							$this->arrResultData['success'] = 'false';
							$this->arrResultData['m'] = 'Invalid user or password;';
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
			  
			  // check soc_network_type index
			if(!array_key_exists("soc_network_type",$json) || empty($json['soc_network_type'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'soc_network_type tye missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("soc_network_type"=>"soc_network_type is missing or Invalid index");
					$flag=1;
			 }
			 else
			 {
				if(!is_integer($json["soc_network_type"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid soc_network_type value';
					if($this->debug)
						$this->arrResultData['debug'][] = array("soc_network_type"=>"Invalid soc_network_type value must be proper format");
					$flag=1;
				} else {
				 $sql_account_type = "SELECT `id`, `name` FROM `socNetworkType` WHERE id=".$json['soc_network_type'];
				 $arrAccountType = $this->exec_query_0($sql_account_type,'SEL');
				 if(empty($arrAccountType)) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'miss match soc_network_type type';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("soc_network_type"=>"soc_network_type Type is not present in Database");
					$flag=1;
				}
			   }
			 }
			 
			  // check share_rou_to index
		   if(isset($json['share_to_user_name'])) {
			 if(!array_key_exists("share_to_user_name",$json) || empty($json['share_to_user_name'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'share to user is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("share_to_user_name"=>"share_to is missing or Invalid index");
					$flag=1;
			 }else
			 {
				if(isset($json["share_to_user_name"]) && is_integer($json["share_to_user_name"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid share_to value';
					if($this->debug)
						$this->arrResultData['debug'][] = array("share_to_user_name"=>"Invalid share_to value must be proper format");
					$flag=1;
				}
			 } // check share_rou_to index
		   }
			if(isset($json['share_to_user_id'])) {
			 if(!array_key_exists("share_to_user_id",$json) || empty($json['share_to_user_id'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'share to user is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("share_to_user_id"=>"share_to_user_id is missing or Invalid index");
					$flag=1;
			 }else
			 {
				if(isset($json["share_to_user_id"]) && !is_integer($json["share_to_user_id"])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'Invalid share_to value';
					if($this->debug)
						$this->arrResultData['debug'][] = array("share_to_user_id"=>"Invalid share_to value must be proper format");
					$flag=1;
				}
			}
			}
             // for share a promotion via post on facebook or twitter
             // is not necesary a user name or user id 
             // only when user wants to share with specific tarjet such as 
             // OTU, fb, tw, friend or via email
             if( (($json['soc_network_type'] == 1) || ($json['soc_network_type'] == 4) ) && 
                  !isset($json['share_to_user_name']) && 
                  !isset($json['share_to_user_id'])) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'share_to_user_name or share_to_user_id is missing';
					if($this->debug)
						$this->arrResultData['debug'][] = array("share_to_user_id_name"=>"Invalid share_to_user_name or share_to_user_id missing");
					$flag=1;
			}
			 if($flag) { return false; } else { return true;}
			 
		} // end of validateData()
	
	}
?>

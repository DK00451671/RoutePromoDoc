<?php
/*
* Business Logic 
* component : LogIn
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require_once("dbconfig.php");
	
	class getMapTiles {
		
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
		
		/* 
		*	Description   :Function to check user exist or not
		*	Function name : consultarIdUsuario
		*	Input Parasm  : username and password
		*	Output        : user info array
	    */
		 
		public function authenticateUser($user, $password)
        {
		    $query = "SELECT id, name, gender, age,account_type_id, is_non_register FROM usr WHERE user_name = '@U' AND password = '@P'";
            
            $query = str_replace("@U", $user, $query);
            $query = str_replace("@P", $password, $query);
			
			$result = mysqli_query($this->conexion, $query) or die (json_encode(array("success" => "false", "m" => "Error al ejecutar el query: " . $query)));
			$arrData = mysqli_fetch_array($result, MYSQLI_ASSOC);
						   
            if (empty($arrData))
            {
				return array();
            }
            else
            { 	
				 return $arrData;
            }
        } //end of consultarIdUsuario
		
		public function validarEstructura($jsonEntrada) {


            $this->arrResultData['success'] = 'true';
            $zoom_13 = array(
                    "zoom"=>13, 
                        "1742"=>array(3612,3213,3614,3615,3616),
                        "1743"=>array(3612,3213,3614,3615,3616),
                        "1744"=>array(3612,3213,3614,3615,3616),
                        "1745"=>array(3612,3213,3614,3615,3616),
                        "1746"=>array(3612,3213,3614,3615,3616)
                    );
            $zoom_14 = array(
                    "zoom"=>14, 
                        "3484"=>array(7225,7226,7227,7228,7229,7230,7231,7232,7233),
                        "3485"=>array(7225,7226,7227,7228,7229,7230,7231,7232,7233),
                        "3486"=>array(7225,7226,7227,7228,7229,7230,7231,7232,7233),
                        "3487"=>array(7225,7226,7227,7228,7229,7230,7231,7232,7233),
                        "3488"=>array(7225,7226,7227,7228,7229,7230,7231,7232,7233),
                        "3489"=>array(7225,7226,7227,7228,7229,7230,7231,7232,7233),
                        "3490"=>array(7225,7226,7227,7228,7229,7230,7231,7232,7233),
                        "3491"=>array(7225,7226,7227,7228,7229,7230,7231,7232,7233),
                        "3492"=>array(7225,7226,7227,7228,7229,7230,7231,7232,7233)
                    );
            $zoom_15 = array(
                   "zoom"=>15, 
                    );
            $zoom_16 = array(
                   "zoom"=>16, 
                    );
            $tiles_array = array($zoom_13,$zoom_14,$zoom_15,$zoom_16); 
            $this->arrResultData['m'] = array("mapID"=> "rico1.j3a5a04l", "tiles"=>$tiles_array);

            return $this->arrResultData;
		
			$json = $jsonEntrada['LOG'];
			$arrResult = array();
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			if($this->debug)
				$this->arrResultData['debug'][] = array("send query"=>$json);
				
			if($this->validateData($json)) {
				$arrResult = $this->authenticateUser($json['user'],$json['passwd']);
				
				if(!empty($arrResult)){
					//$this->arrResultData['m']= "Login successfully!";
                    $this->arrResultData['m']= array("id"=>(int)$arrResult["id"]);
                    
					if($this->debug)
						$this->arrResultData['debug'][] = array("authentication"=>"success");
				}else {
					$this->arrResultData['success'] = 'false';
					if(empty($this->arrResultData['m']))
						$this->arrResultData['m'] = 'Invalid user name and password';
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
			}
			 
			// check passwd index
			if(!array_key_exists("passwd",$json) || empty($json['passwd'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'passwd is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("password"=>"passwd is missing or Invalid index");
					$flag=1;
			}
			else
			{
				if(isset($json['passwd']) && strlen($json['passwd']) > 30) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'passwd mast be less than 30 character';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("passwprd"=>"length of user name is exceed");
					$flag=1;
				}
			
			}
			
			/*if (isset($json["user"]) && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $json["user"]))
			{
				$this->arrResultData['success'] = 'false';
			   $this->arrResultData['m'] = 'user name should be an email ID';
			   if($this->debug)
					$this->arrResultData['debug'][] = array("name"=>"user name should not contain the special character. only alphabet and digits are allowed.");
			}*/
		
			if($flag) { return false; } else { return true;}
		}
		
	}// end of LogIn

?>

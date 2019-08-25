<?php
/*
* Business Logic 
* component : LogIn
*/
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
    require_once("dbconfig.php");
	
	class recovAcc {
		
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
		 
		public function authenticateEmail($email )
        {
		    $query = "SELECT * FROM  `usr` WHERE user_name = '@E'";
            
            $query = str_replace("@E", $email, $query);
			
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
		
			$json = $jsonEntrada['LOG'];
			$arrResult = array();
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			if($this->debug)
				$this->arrResultData['debug'][] = array("send query"=>$json);
				
			if($this->validateData($json)) {
				$arrResult = $this->authenticateEmail($json['email']);
				
				if(!empty($arrResult)){
                    $this->arrResultData['m']= "email sent, please check email to update password";
                    //var_dump($arrResult);
                    if($this->debug)
						$this->arrResultData['debug'][] = array("authenticateEmail"=>"success");
                    //$this->arrResultData['m']= array("id"=>(int)$arrResult["id"]);
                    //# TODO
                    //Send email to destiny and do recover access procces to 
                    //reset password
                    
					
				}else {
					$this->arrResultData['success'] = 'false';
					if(empty($this->arrResultData['m']))
						$this->arrResultData['m'] = 'Email not exist';
					if($this->debug)
						$this->arrResultData['debug'][] = array("athenticationEmail"=>"fail");
				}
			}
			return $this->arrResultData;
		}
		
		function validateData($json) {
			
			$flag = 0;
			$this->debug = (isset($json["debug"])) ? $json["debug"] : FALSE;
			
			// check email index
			if(!array_key_exists("email",$json) || empty($json['email'])) {
				$this->arrResultData['success'] = 'false';
				$this->arrResultData['m'] = 'email is missing';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("email"=>"email is missing");
					$flag=1;
			}else
			{
				if(isset($json['email']) && strlen($json['email']) > 50) {
					$this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid email it must be less than 50 character';
				   if($this->debug)
						$this->arrResultData['debug'][] = array("email"=>"length of email is exceed");
					$flag=1;
                }
                // validate contains @ character
                else if(strpos($json['email'], '@') == false) {
                    $this->arrResultData['success'] = 'false';
					$this->arrResultData['m'] = 'invalid email';
				    if($this->debug)
						$this->arrResultData['debug'][] = array("email"=>"invalid email");
					$flag=1;

                }
			}
			 
		
			if($flag) { return false; } else { return true;}
		}
		
	}// end of LogIn

?>

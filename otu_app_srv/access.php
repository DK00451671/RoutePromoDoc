<?php
	error_reporting(E_ALL);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With'); 

    $usr['jrico']   = 'qwerty';
    $usr['dinesh']  = 'test_test';
    $usr['pointmatrix']  = 'otu_app';

    $arrWeb_services = array('1.1_logIn',
	                         '1.2_signIn',
							 '1.3_logOut',
                             '1.4_recovAcc',
                             '1.5_updateSignIn',
                             '-------------------',
							 '2.1_getMyRouLst',
                             '2.2_getRouInf',
                             '2.2.1_getRouPnts',
                             '2.3_sendRou',
							 '2.4_shareRou',
                             '2.4.1_addToMyRouLst',
							 '2.5_delRou',
                             '2.6_sendNewRou',
                             '2.8_accShareRou',
                             '2.9_getMsg',
                             '2.9.1_getMsgRou',
                             '2.9.2_getMsgInf',
                             '-------------------',
                             '3.0_promTyp',
                             '3.1_getPromTyp',
                             '3.2_setPromTyp',
                             '3.3.1_getPromRou',
                             '3.3.2_getPromAro',
                             '3.3.3_getPromInf',
                             '3.3.4_getBusiInf',
                             '3.4_shareProm',
                             '3.5_viewedProm',
                             '3.6_usedProm',
                             '3.7_getShaProm',
                             '3.8_delProm',
                             '------------',
                             '4.1_getFriLst',
                             '4.1.1_getFriInf',
                             '4.3_sendFriReq',
                             '4.4_accFri',
                             '4.5_delFri',
                             '------------',
                             '5.1_getNoti',
                         );
	$arrData = array();
	$arrData = isset($_POST["json"]) ? $_POST["json"] : array();
	 
	if(empty($arrData)) {
       echo json_encode(array("success" => "false", "m"=> "1Estructura Json Malformada"));
	}
	else 
	{
		 $arr_json = json_decode($arrData, TRUE);
		 
		 if (array_key_exists("ACC", $arr_json) && array_key_exists("fun", $arr_json['ACC']))
         {  
             if ($arr_json['ACC']['fun'] == 'valUsr')
             {
                 
                 if ($usr[$arr_json['ACC']['txtUser']] == $arr_json['ACC']['md5Passwd'] and
                     empty($arr_json['ACC']['txtUser']) == false and 
                     empty($arr_json['ACC']['md5Passwd']) == false  ) 
                 {
                     //$message = read_file('echo_json.html');
                     $url = 'http://otu-srv.dyndns.ws/otu_app_srv/Main.php';
                     echo json_encode(array("success" => "true", 
                                            "m" => $arrWeb_services,
                                            "url" => $url));
                 }
                 else
  		             echo json_encode(array("success" => "false", "m" => "usr or passwd not valid"));
             }
             else 
             {
		         echo json_encode(array("success" => "false", "m" => "fun not valid"));
                 exit(1);
             }
		}
		else if (array_key_exists("EXA", $arr_json) && array_key_exists("fun", $arr_json['EXA']))
         {  
			if (!empty($arr_json['EXA']['fun']))
			{
                 $file = 'docs/' . $arr_json['EXA']['fun'] . ".php" ;
                 
//echo $file;
				 
                 if ($usr[$arr_json['EXA']['txtUser']] == $arr_json['EXA']['md5Passwd'] and
                     empty($arr_json['EXA']['txtUser']) == false and 
                     empty($arr_json['EXA']['md5Passwd']) == false  ) 
                 {
					$message = read_file($file);
					//echo $file;
					if (!$message){
						echo json_encode(array("success" => "false", "m" => "error"));
						exit(1);
					}
					else{
                                               // remove php tags
                                              $message = str_replace("<?php", "", $message); 
                                              $message = str_replace("?>", "", $message); 
                                              $message = str_replace("\n", "", $message); 
#						echo json_encode(array("success" => "true", "m" => $message));
						echo $message;
						exit(0);
						}
                 }
                 else
  		             echo json_encode(array("success" => "false", "m" => "usr or passwd not valid"));
             }
             else 
             {
		         echo json_encode(array("success" => "false", "m" => "error"));
                 exit(1);
             }
		}
		else 
		  echo json_encode(array("success" => "false", "m" => "2Estructura Json Malformada"));
		  
	} //end of main else	

function read_file($file_name)
{
//	echo $file_name;
    $file = file_get_contents($file_name, FILE_USE_INCLUDE_PATH);
    //var_dump($file);
    return $file;
}
?>



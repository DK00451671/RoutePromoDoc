<?php

/*
 # This is generalise code for all web services.
 # Below code is acting like as a controller 
 # Main business logic is handled in component.
 # Develope By : Rico & DK 
*/
	error_reporting(E_ALL);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With'); 

	// array for "RPT"  web services
	
	$arr_service_name['LOG'] = array('logIn','signIn','logOut','updateSignIn','recovAcc','sendComment','getMapTiles');
	$arr_service_name['PRO'] = array('sendProm','promTyp','getPromTyp','setPromTyp','shareProm','getPromInf','getPromRou','getPromAro','getBusiInf','viewedProm','usedProm','getShaProm','delProm');
	$arr_service_name['ROU'] = array('sendNewRou','getMyRouLst','shareRou','delRou','sendRou','getRouInf','accShareRou','getMsg','getMsgRou','getMsgInf','getRouPnts','getShaRouPnts','addToMyRouLst');
	$arr_service_name['STA'] = array('getStat');
	$arr_service_name['FRI'] = array('sendFriReq','accFri','delFri','getFriInf','getFriLst');
	$arr_service_name['NOT'] = array('getNoti');
	
	$arrData = array();
	//$arrData = isset($_GET["json"]) ? $_GET["json"] : array();
	$arrData = isset($_POST["json"]) ? $_POST["json"] : $_GET["json"];
	$format  = isset($_GET["format"]) ? $_GET["format"] : '';
	if(empty($format)) 
		$format  = isset($_GET["format"]) ? $_GET["format"] : '';
	
	if(empty($arrData)) {
       echo json_encode(array("success" => "false", "m"=> "Estructura Json Malformada"));
	}
	else 
	{
		 $arr_json = json_decode($arrData, TRUE);
		 if (array_key_exists("LOG", $arr_json) && array_key_exists("fun", $arr_json['LOG']))
         {  
		     $index = array_search($arr_json['LOG']['fun'],$arr_service_name['LOG']);
			
			$servie = (isset($arr_service_name['LOG'][$index]) && $arr_service_name['LOG'][$index] !='') ? trim($arr_json['LOG']['fun']) : '';
			
			// for all services which having index as LOG. 
			// No need to write the separte switch case.
			switch($servie){
				case $arr_service_name['LOG'][$index] :
                        #---------------------------------------------------------
                        # NEW RELIC                                             -- 
                        #                                                       --
                        if (extension_loaded('newrelic')) {
                            newrelic_set_appname($arr_service_name['LOG'][$index]);
                        }
                        #---------------------------------------------------------
				 		require('component/'.$arr_service_name['LOG'][$index].".php");
						$obj = new $arr_service_name['LOG'][$index]();
						$result = $obj->validarEstructura($arr_json);
						if(!empty($result)){
							//header('Content-Type: application/json');
							echo json_encode($result);
							//echo "<pre>";print_r($result);
						}
				break;
				default:
                       echo json_encode(array("success" => "false", "m" => "Estructura Json Malformada"));
                break;
			}
		}
		elseif (array_key_exists("ROU", $arr_json) && array_key_exists("fun", $arr_json['ROU']))
         {  
		     $index = array_search($arr_json['ROU']['fun'],$arr_service_name['ROU']);
			
			$servie = (isset($arr_service_name['ROU'][$index]) && $arr_service_name['ROU'][$index] !='') ? trim($arr_json['ROU']['fun']) : '';
			
			// for all services which having index as ROU. 
			// No need to write the separte switch case.
			switch($servie){
				case $arr_service_name['ROU'][$index] :
                        #---------------------------------------------------------
                        # NEW RELIC                                             -- 
                        #                                                       --
                        if (extension_loaded('newrelic')) {
                            newrelic_set_appname($arr_service_name['ROU'][$index]);
                        }
                        #---------------------------------------------------------
				 		require('component/ROU/'.$arr_service_name['ROU'][$index].".php");
						$obj = new $arr_service_name['ROU'][$index]();
						$result = $obj->validarEstructura($arr_json);
						if(!empty($result)){
							//header('Content-Type: application/json');
							echo json_encode($result);
							//echo "<pre>";print_r($result);
						}
				break;
				default:
                       echo json_encode(array("success" => "false", "m" => "Estructura Json Malformada"));
                break;
			}
		}
		elseif (array_key_exists("PRO", $arr_json) && array_key_exists("fun", $arr_json['PRO']))
         {  
		     $index = array_search($arr_json['PRO']['fun'],$arr_service_name['PRO']);
			
			$servie = (isset($arr_service_name['PRO'][$index]) && $arr_service_name['PRO'][$index] !='') ? trim($arr_json['PRO']['fun']) : '';
			
			// for all services which having index as PRO. 
			// No need to write the separte switch case.
			switch($servie){
				case $arr_service_name['PRO'][$index] :
                        #---------------------------------------------------------
                        # NEW RELIC                                             -- 
                        #                                                       --
                        if (extension_loaded('newrelic')) {
                            newrelic_set_appname($arr_service_name['PRO'][$index]);
                        }
                        #---------------------------------------------------------
				 		require('component/PRO/'.$arr_service_name['PRO'][$index].".php");
						$obj = new $arr_service_name['PRO'][$index]();
						$result = $obj->validarEstructura($arr_json);
						if(isset($format) && !empty($format ) && $format=='printr') {
							echo "<pre>";print_r(($result));
						}
						elseif(!empty($result)){
							//header('Content-Type: application/json');
							echo json_encode($result);
							//echo "<pre>";print_r($result);
						}
				break;
				default:
                       echo json_encode(array("success" => "false", "m" => "Estructura Json Malformada"));
                break;
			}
		}
		elseif (array_key_exists("STA", $arr_json) && array_key_exists("fun", $arr_json['STA']))
         {  
		     $index = array_search($arr_json['STA']['fun'],$arr_service_name['STA']);
			
			$servie = (isset($arr_service_name['STA'][$index]) && $arr_service_name['STA'][$index] !='') ? trim($arr_json['STA']['fun']) : '';
			
			// for all services which having index as STA. 
			// No need to write the separte switch case.
			switch($servie){
				case $arr_service_name['STA'][$index] :
                        #---------------------------------------------------------
                        # NEW RELIC                                             -- 
                        #                                                       --
                        if (extension_loaded('newrelic')) {
                            newrelic_set_appname($arr_service_name['STA'][$index]);
                        }
                        #---------------------------------------------------------
				 		require('component/STA/'.$arr_service_name['STA'][$index].".php");
						$obj = new $arr_service_name['STA'][$index]();
						$result = $obj->validarEstructura($arr_json);
						if(!empty($result)){
							//header('Content-Type: application/json');
							echo json_encode($result);
							//echo "<pre>";print_r($result);
						}
				break;
				default:
                       echo json_encode(array("success" => "false", "m" => "Estructura Json Malformada"));
                break;
			}
		}
		elseif (array_key_exists("FRI", $arr_json) && array_key_exists("fun", $arr_json['FRI']))
         {  
		     $index = array_search($arr_json['FRI']['fun'],$arr_service_name['FRI']);
			
			$servie = (isset($arr_service_name['FRI'][$index]) && $arr_service_name['FRI'][$index] !='') ? trim($arr_json['FRI']['fun']) : '';
			
			// for all services which having index as FRI. 
			// No need to write the separte switch case.
			switch($servie){
				case $arr_service_name['FRI'][$index] :
                        #---------------------------------------------------------
                        # NEW RELIC                                             -- 
                        #                                                       --
                        if (extension_loaded('newrelic')) {
                            newrelic_set_appname($arr_service_name['FRI'][$index]);
                        }
                        #---------------------------------------------------------
				 		require('component/FRI/'.$arr_service_name['FRI'][$index].".php");
						$obj = new $arr_service_name['FRI'][$index]();
						$result = $obj->validarEstructura($arr_json);
						if(!empty($result)){
							//header('Content-Type: application/json');
							echo json_encode($result);
							//echo "<pre>";print_r($result);
						}
				break;
				default:
                       echo json_encode(array("success" => "false", "m" => "Estructura Json Malformada"));
                break;
			}
         }
        elseif (array_key_exists("NOT", $arr_json) && array_key_exists("fun", $arr_json['NOT']))
         {  
		     $index = array_search($arr_json['NOT']['fun'],$arr_service_name['NOT']);
			
			$servie = (isset($arr_service_name['NOT'][$index]) && $arr_service_name['NOT'][$index] !='') ? trim($arr_json['NOT']['fun']) : '';
			
			// for all services which having index as NOT. 
			// No need to write the separte switch case.
			switch($servie){
				case $arr_service_name['NOT'][$index] :
                        #---------------------------------------------------------
                        # NEW RELIC                                             -- 
                        #                                                       --
                        if (extension_loaded('newrelic')) {
                            newrelic_set_appname($arr_service_name['NOT'][$index]);
                        }
                        #---------------------------------------------------------
				 		require('component/NOT/'.$arr_service_name['NOT'][$index].".php");
						$obj = new $arr_service_name['NOT'][$index]();
						$result = $obj->validarEstructura($arr_json);
						if(!empty($result)){
							//header('Content-Type: application/json');
							echo json_encode($result);
							//echo "<pre>";print_r($result);
						}
				break;
				default:
                       echo json_encode(array("success" => "false", "m" => "Estructura Json Malformada"));
                break;
			}
		}
		else 
		  echo json_encode(array("success" => "false", "m" => "Estructura Json Malformada"));
		  
	} //end of main else	
?>

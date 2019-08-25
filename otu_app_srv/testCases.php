<html> 
  </head> 	
  	<script src="js/jquery-1.3.2.min.js"></script>
		<script src="js/ajax-lib.js"></script> 
   	<script src="js/lib.js"></script> 
     <script language="Javascript">
	     function process() {
		 $req= document.getEL
			var url = "http://"+window.location.host+"/otu_app_srv/Main.php";
			var str = "json="+jv('input');
			remoteCall(url,str,'output','D');
		
		 }
	 </script>
  </head>
   <table border="1">
		<tr border="1">  
		     <td ><textarea rows="15" cols="50" id="input" ></textarea></td>
		     <td><textarea rows="15" cols="60" id="output"></textarea></td>
		</tr>
		<tr><td><input type="button" value="submit" onclick="process();" /></td> </tr>
    </table>
</html>

<?php
	//phpinfo();
	
	ini_set("POST_MAX_SIZE", "25Mb");
	$serive_file = $_GET['service'];
	$url  = $_SERVER['SERVER_NAME']."/otu_app_srv/docs/". $serive_file.".php";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);      
	curl_close($ch);
	echo $output; 
?>
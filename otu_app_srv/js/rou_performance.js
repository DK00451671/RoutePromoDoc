var host = window.location.origin;
var Surl = host+"/otu_app_srv/Main.php?";
var  sendNewRouteRawData = host+"/otu_app_srv/getData.php";
var arrReqData = new Array();


function loginTest() {
		
	 var login = '';
	 var str = 'json=';
	 
	 arrReqData['user']   = jv('usernametest');
	 arrReqData['passwd'] = jv('passwordtest');
	 
	 var objData = { LOG:{ fun:"logIn",
						  user:jv('usernametest'), 
						  passwd:jv('passwordtest')}
				   };
	 str += JSON.stringify(objData);
	 remoteCall(Surl,str,'verify','F');
	 
} //end of loginTest()

function verify(){
		var str = 'json=';
		var objects = JSON.parse(sResponse['verify']);
		//console.log(objects);
		if(objects['success']=='true') {	
			alert('login successfull..');
			var possible = "MF";
			var gender_temp = possible.charAt(Math.floor(Math.random() * possible.length));
			
			var accountid = "123";
			var account_type = parseInt(accountid.charAt(Math.floor(Math.random() * accountid.length)));
			
			var usernametest = getRandomString(10);
			var passwordtest = getRandomString(8);
			arrReqData['user']   = usernametest;
			arrReqData['passwd'] = passwordtest;
			
			var objData = { LOG:{ 
						  fun:"signIn",
						  name:getRandomString(10), 
						  user:usernametest,
						  passwd:passwordtest,
						  age: Math.floor((Math.random() * 100) + 1),
						  gender:gender_temp,
						  account_type:account_type,
						}
				   };
			str += JSON.stringify(objData);
			remoteCall(Surl,str,'registerUser','F');
			 // add code to do the randomize user regsitration 
			document.getElementById('login').style.display='none';
			document.getElementById('process_input').style.display='block';
			//$("#data_here").fadeOut(500).fadeIn(500); 
			//start_route_performance();
		}else
		  alert('Access denied');
} //end of verify()

	function registerUser() {
		var objects = JSON.parse(sResponse['registerUser']);
			if(objects['success']=='true'){
			alert('register user successfull..');			
		}
	}


var arrData = new Array();
var arrDataDetails = new Array();
var index = 0;
var temp_cord = Array();
function start_route_performance() {
	document.getElementById('data_here').style.display='block';
	var distance = jv('distance');
	var iterations = jv('iterations');
	var points = jv('points');
	var str_dashboard = '';
	
	for(var i=0; i<jv('iterations');i++){
		arrDataDetails[i] ='';
		var  route_data = new Array();
		var random_route  = Math.floor((Math.random() * gdl_route.length) + 1);
		var route_obj = JSON.parse(gdl_route[random_route]);
		var route_points = route_obj['path'];
		
		for(var j=0 ; j<points ;j++){
			route_data[j] = new Array();
			var res = route_points[j].split(",",2);
			//route_data[i][0] = parseInt(currentDateTime());
			route_data[j][0] = 1403714956;
			route_data[j][1] = parseFloat(res[0]);
			route_data[j][2] = parseFloat(res[1]);
			route_data[j][3] = 2;
			route_data[j][4] = "message txt"+i;
		}
		temp_cord['co-ordi'] = route_data;
		arrData[i] = '<table border=1><tr>';
		str_dashboard += '<table border=1><tr>';
		str_dashboard += '<td><div id="panel'+i+'"></div></td>';
		str_dashboard += '<td><div id="panelinfo'+i+'"></div></td>';
		str_dashboard += '</tr></table>';
		index = i;
		startProcessRouteFlow();
		arrData[i] += '</tr></table>';
	}
	jih('data_here',str_dashboard);
	document.getElementById('data_here').style.display='block';
	for(var i=0; i<jv('iterations');i++){
	       jih('panel'+i,arrData[i]);
		   jih('panelinfo'+i,arrDataDetails[i]);
		
	}
	
}

	function startProcessRouteFlow() {
		 
		 getPromoType();
		 //console.log(arrReqData);
		 setPromoType();
		 sendNewRoute();
		 getRouInf();
		 getRouPoint();
		 getMsgRou();
		 getPromRou();
		 getPromInf();
		 
		
	}

/*************************** function to proceess the getPromoType**********************/
function getPromoType() {
	 var start = new Date().getTime();

	 var getPromTyp = '';
	 var str = 'json=';
	 
	// arrReqData['user']   = "test@test.com"; //jv('usernametest');
	 //arrReqData['passwd'] = "123"; //jv('passwordtest');
	 var objData = { PRO:{ fun:"getPromTyp",
						  user:arrReqData['user'], 
						  passwd:arrReqData['passwd']}
				   };
	 str += JSON.stringify(objData);
	 remoteCall(Surl,str,'respGetPromoType','F');
	 var end = new Date().getTime();
	 var time = end - start;
	 var seconds = time / 1000;
	 
	 arrData[index] += "<tr><td align='justify'>getPromoType</td><td align='justify'>"+ seconds + " S </td></tr>";
}

function respGetPromoType() {
		var objects = JSON.parse(sResponse['respGetPromoType']);
       	if(objects['success']=='true' && objects['m'] !='') {	
				var arr =[];
					for( var i in objects['m'] ) {
						if (objects['m'].hasOwnProperty(i)){
						   arr.push(objects['m'][i]);
						}
					}
			}else
			{
				arr = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
			}
				console.log(arr);
				var count = (Math.floor((Math.random() * arr.length) + 1)); 
				var businessIds = new Array();
				//arrData[index] += "selected Business Ids=";
				for(var i=0;i<count;i++) {
					var temp  = arr[Math.floor(Math.random() * arr.length)];
					//arrData[index] += ""+temp+",";
						if(!isInArray(temp,businessIds)){ 
							businessIds.push(temp);
						}
				}
				arrReqData['types'] = businessIds;
} //end of respGetPromoType


/*************************** function to proceess the setPromoType**********************/
function setPromoType() {
	var start = new Date().getTime();

	var str = 'json=';
	var objData = { PRO:{ fun   : "setPromTyp",
						  user  : arrReqData['user'], 
						  passwd: arrReqData['passwd'],
						  types : arrReqData['types']
						  }
				   };
	str += JSON.stringify(objData);
	remoteCall(Surl,str,'respSetPromoType','F');
    var end = new Date().getTime();
	var time = end - start;
	var seconds = time / 1000;
	//arrData[index] += "<tr><td align='justify'>setPromoType</td><td align='justify'>"+ seconds + " S </td><td align='justify'>"+arrReqData['respSetPromoType']+"</td></tr>";
	arrData[index] += "<tr><td align='justify'>setPromoType</td><td align='justify'>"+ seconds + " S </td></tr>";
	
}
function respSetPromoType() {
	var objects = JSON.parse(sResponse['respSetPromoType']);
		 arrReqData['respSetPromoType'] = JSON.stringify(objects);
	if(objects['success'] !='true')
	     alert('failed to execute SetPromoType');
}

/**********************function sendNewRoute()*****************************************/
function sendNewRoute(){
	var start = new Date().getTime();
	var str = 'json=';	
    var strtemp = "req=sendNewRouteRawData";
	//remoteCall(sendNewRouteRawData,strtemp,'respSendNewRouteRawData','F');
	var objData = { ROU:{ fun:"sendNewRou",
						  user:arrReqData['user'], 
						  passwd:arrReqData['passwd'],
						  route_name:"route"+currentDateTime(),
						  distance:410.5,
						  duration:1660,
						 // 'co-ordinates':arrReqData ['sendNewRouteCo-ordi']['co-ordinates']
						  'co-ordinates':temp_cord['co-ordi']
						 }
				   };
	arrReqData['route_name'] = objData.ROU.route_name;
	str += JSON.stringify(objData);	
	remoteCall(Surl,str,'respSendNewRoute','F');
	//arrData[index] += "<tr><td colspan='3' align='justify'> route name= "+ arrReqData['route_name'] + " &nbsp;&nbsp;&nbsp;&nbsp;route_id ="+arrReqData['route_id'] +"    </td></tr>";
	var end = new Date().getTime();
	var time = end - start;
	var seconds = time / 1000;
	//arrData[index] += "<tr><td align='justify'>sendNewRoute</td><td align='justify'>"+ seconds + " S </td><td align='justify'>"+arrReqData['respSendNewRoute']+"</td></tr>";
	arrData[index] += "<tr><td align='justify'>sendNewRoute</td><td align='justify'>"+ seconds + " S </td></tr>";
} //end of sendNewRoute()


/*function respSendNewRouteRawData() {
console.log(JSON.parse(sResponse['respSendNewRouteRawData']));
	arrReqData['sendNewRouteCo-ordi'] = JSON.parse(sResponse['respSendNewRouteRawData']); 
	console.log(JSON.parse(sResponse['respSendNewRouteRawData']));
}*/
function respSendNewRoute() {
	var objects = JSON.parse(sResponse['respSendNewRoute']); 
	arrReqData['respSendNewRoute'] = JSON.stringify(objects);
	if(objects['success'] =='true')
	    arrReqData['route_id'] = objects['m']['route_id'];
}
/*****************************getRouInf**********************************/

function getRouInf() {
	var start = new Date().getTime();
	var str = 'json=';
	var objData = { ROU:{ fun   : "getRouInf",
						  user  : arrReqData['user'], 
						  passwd: arrReqData['passwd'],
						  route_id : arrReqData['route_id']
						  }
				   };
	str += JSON.stringify(objData);
    remoteCall(Surl,str,'respGetRouInf','F');
	var end = new Date().getTime();
	var time = end - start;
	var seconds = time / 1000;
	//arrData[index] += "<tr><td align='justify'>getRouInf</td><td align='justify'>"+ seconds + " S </td><td align='justify'>"+arrReqData['respGetRouInf']+"</td></tr>";
	arrData[index] += "<tr><td align='justify'>getRouInf</td><td align='justify'>"+ seconds + " S </td></tr>";
	
} //end of getRouInf (0

function respGetRouInf() {
		var objects = JSON.parse(sResponse['respGetRouInf']);
		arrReqData['respGetRouInf'] = JSON.stringify(objects);
		jval('getRouInf',JSON.stringify(objects));
} //end of respGetRouInf()

/*****************************getRouPoint**********************************/

function getRouPoint() {
	var start = new Date().getTime();
	var str = 'json=';
	var objData = { ROU:{ fun      : "getRouPnts",
						  user     : arrReqData['user'], 
						  passwd   : arrReqData['passwd'],
						  route_id : arrReqData['route_id']
						  }
				   };
	str += JSON.stringify(objData);
    remoteCall(Surl,str,'respGetRouPoint','F');
	var end = new Date().getTime();
	var time = end - start;
	var seconds = time / 1000;
	//arrData[index] += "<tr><td align='justify'>getRouPoint</td><td align='justify'>"+ seconds + " S </td><td align='justify'>"+arrReqData['respGetRouPoint']+"</td></tr>";
	arrData[index] += "<tr><td align='justify'>getRouPoint</td><td align='justify'>"+ seconds + " S </td></tr>";
	
	
} //end of getRouInf ()

function respGetRouPoint() {
		var objects = JSON.parse(sResponse['respGetRouPoint']);
		arrReqData['respGetRouPoint'] = JSON.stringify(objects);
		arrDataDetails[index] += "<tr><td colspan='2'>";
		arrDataDetails[index] += "name="+objects['m']['name']+"<br>";
		arrDataDetails[index] += "owner_id="+objects['m']['owner_id']+"<br>";
		//arrDataDetails[index] += "distance="+objects['m']['distance']+"<br>";
		arrDataDetails[index] += "duration="+objects['m']['duration']+"<br>";
		arrDataDetails[index] += "creation_date="+objects['m']['creation_date']+"<br>";
		for( var i in objects['m']) {
			if (objects['m'].hasOwnProperty(i)){
			  // console.log("name"+objects['m']['name']);
			   //arrData[index] += "co-ordinates="+objects['m']['co-ordinates']+"<br>";
			}
		}
		for( var i in objects['m']['co-ordinates'] ) {
			arrDataDetails[index] += objects['m']['co-ordinates'][i]+"<br>";
		}
		arrDataDetails[index] += "</td></tr>";
		//arrData[index] += "<tr><td colspan='2' align='justify'>"+arrReqData['respGetRouPoint']+"</td></tr>";
		//jval('getRouPoints',JSON.stringify(objects));
} //end of respGetRouPoint()

/*****************************getMsgRou**********************************/

function getMsgRou() {
	var start = new Date().getTime();
	var str = 'json=';
	var currentdate = new Date();
	var objData = { ROU:{ fun      : "getMsgRou",
						  user     : arrReqData['user'], 
						  passwd   : arrReqData['passwd'],
						  route_id : arrReqData['route_id'],
						  limit_date: (currentdate.getFullYear()+"="+(currentdate.getMonth()+1)+"-"+currentdate.getDate())
						 
						  }
				   };
	str += JSON.stringify(objData);
    remoteCall(Surl,str,'respGetMsgRou','F');
	var end = new Date().getTime();
	var time = end - start;
	var seconds = time / 1000;
	//arrData[index] += "<tr><td align='justify'>getMsgRou</td><td align='justify'>"+ seconds + " S </td><td align='justify'>"+arrReqData['respGetMsgRou']+"</td></tr>";
	arrData[index] += "<tr><td align='justify'>getMsgRou</td><td align='justify'>"+ seconds + " S </td></tr>";
	
} //end of getRouInf (0

function respGetMsgRou() {
		var objects = JSON.parse(sResponse['respGetMsgRou']);
		arrReqData['respGetMsgRou'] = JSON.stringify(objects);
		jval('getMsgRou',JSON.stringify(objects));
} //end of respGetRouPoint()

/*****************************getPromRou**********************************/

function getPromRou() {
	var start = new Date().getTime();
	var str = 'json=';
	var currentdate = new Date();
	var objData = { PRO:{ fun      : "getPromRou",
						  user     : arrReqData['user'], 
						  passwd   : arrReqData['passwd'],
						  route_id : arrReqData['route_id'],
						}
				   };
	str += JSON.stringify(objData);
    remoteCall(Surl,str,'respGetPromRou','F');
	var end = new Date().getTime();
	var time = end - start;
	
	var seconds = time / 1000;
	//arrData[index] += "<tr><td align='justify'>getPromRou </td><td align='justify'>"+ seconds + " S </td><td align='justify'>"+arrReqData['respGetPromRou']+"</td></tr>";
	arrData[index] += "<tr><td align='justify'>getPromRou </td><td align='justify'>"+ seconds + " S </td></tr>";
} //end of getRouInf (0

function respGetPromRou() {
		var objects = JSON.parse(sResponse['respGetPromRou']);
		arrReqData['respGetPromRou'] = JSON.stringify(objects);
		//console.log(objects);
		jval('getPromRou',JSON.stringify(objects));
} //end of respGetRouPoint()

/*****************************getPromInf**********************************/

function getPromInf() {
	var start = new Date().getTime();
	var str = 'json=';
	var currentdate = new Date();
	var objData = { PRO:{ fun      : "getPromInf",
						  user     : arrReqData['user'], 
						  passwd   : arrReqData['passwd'],
						  route_id : arrReqData['route_id'],
						  prom_id   :0
						}
				   };
	str += JSON.stringify(objData);
    remoteCall(Surl,str,'respGetPromInf','F');
	var end = new Date().getTime();
	var time = end - start;
	var seconds = time / 1000;
	//arrData[index] += "<tr><td align='justify'>getPromInf </td><td align='justify'>"+ seconds + " S </td><td align='justify'>"+arrReqData['respGetPromInf']+"</td></tr>";
	arrData[index] += "<tr><td align='justify'>getPromInf </td><td align='justify'>"+ seconds + " S </td></tr>";
	
} //end of getRouInf (0

function respGetPromInf() {
		var objects = JSON.parse(sResponse['respGetPromInf']);
		arrReqData['respGetPromInf'] = JSON.stringify(objects);
		jval('getPromInf',JSON.stringify(objects));
} //end of respGetPromInf()

function isInArray(value, array) {
  return array.indexOf(value) > -1;
}

function currentDateTime(){

   var currentdate = new Date();
   var datetime =  currentdate.getDate()+""+(currentdate.getMonth()+1)+""+currentdate.getFullYear()+""+currentdate.getHours()+""+currentdate.getMinutes()+""+currentdate.getSeconds();
   return datetime;
   
}
function getRandomString(char_len) {
	var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for( var i=0; i < char_len; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    return text;
}
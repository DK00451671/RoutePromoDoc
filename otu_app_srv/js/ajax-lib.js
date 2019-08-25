var xmlHttp;
var uri = "";
var callingFunc = "";
var sResponse = new Array();

function remoteCall(sUrl, sQueryStr, sCalledBy, sType)
{ 
 	if(!sType){
		var sType = 'F';
	}
	 //$('#'+sCalledBy).slideToggle('slow',""); 
	var resStr = "";
	var str = " { ";
	if(sQueryStr != ""){
		var arr1 = new Array();
		arr1 = sQueryStr.split("&");
		if(arr1){
			for(i in arr1)
			{
				if(arr1[i] && arr1[i] != ""){
					var arr2 = new Array();
					arr2 = arr1[i].split("=");
					str += arr2[0]+":'"+arr2[1]+"' ,";
				}
			}
		}
	}
	str += " tp: 'tp' } ";
	//alert(sQueryStr);
	//sQueryStr = encodeURIComponent(sQueryStr);
	
	$.ajax({ 
		type: "POST",	 
		url: sUrl,
		data: sQueryStr,
		async:false,
		cache: false,
		dataType: "html",
		success: function(data) {
		
		        switch(sType){
				case 'F':
					sResponse[sCalledBy] = data;
					eval(" "+sCalledBy+"() ");			
				break;
				
				case 'D':
				    if(document.getElementById(sCalledBy))
						document.getElementById(sCalledBy).innerHTML = data;
				break;
			}
		}
	});
	
	
}

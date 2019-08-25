var user_name='';
var psw='';
 var url= 'http://otu-srv.dyndns.ws/otu_app_srv/access.php';
 var url2 = '';
 var query_box = '<td> Query:</br><textarea id="query_send" name="query_send" rows="18" cols="45"></textarea> </td>';
 var query_answer_box = '<td>  Query answer:</br><textarea readonly=readonly id="query_answer" name="query_answer" rows="18" cols="45"></textarea></td>';
 var send_query_button = '<td> <input type="submit" value="enviar" name="enviar" onclick="send_post()"> </td>';
 var list_box_WS = '<td> <form onchange="get_WS_example()">Select a Web Service &nbsp;&nbsp;<select name="hall" id="hall" value="3" width="10">	<option selected="selected"></option></select></form></td>';
 var query_example = '<td colspan="2"> <div id="query_example" style="width:800px;border:1px solid #ccc;overflow:auto;"> </div>';

function get_WS_example(){
	var selected_value = document.getElementById('hall').value;
	var str='';

	str += 'json={"EXA":{"fun": "'+selected_value+'","txtUser":"'+user_name+'","md5Passwd":"'+psw+'"}}';
	remoteCall(url,str,'show_WS_example','F');

	//alert(selected_value);
}

function fill_list_box(Arrweb_services){
 //load Customer list on list box
var select = document.getElementById("hall");

for (var ii=0; ii<Arrweb_services.length; ++ii){ 

    select.options[select.options.length] = new Option(Arrweb_services[ii]);
}
}

function show_WS_example(){
	//alert(sResponse['loginRequest']);
	//var objects = JSON.parse(sResponse['show_WS_example']);
	jih('query_example',sResponse['show_WS_example']);
	//console.log(objects);
}

function login() {

 var str='';
 //str += "json="+(jv('json'));
 user_name = jv('username');
 psw= jv('password');
 str += 'json={"ACC":{"fun": "valUsr","txtUser":"'+jv('username')+'","md5Passwd":"'+jv('password')+'"}}';
 remoteCall(url,str,'loginRequest','F');

}

function loginRequest() {
	//alert(sResponse['loginRequest']);
	var objects = JSON.parse(sResponse['loginRequest']);
    //console.log(objects);
	if(objects['success']=='true') {
        url2 = objects['url'];
		// clean login 
		jih('login','');
	    //window.location.href = "/index.php";	
        var html = '<tr>  '+ query_box + query_answer_box + ' </tr>';
        html += '<tr>' + send_query_button + '</tr>';
		html += '<tr>' + list_box_WS + '</tr>';
        html += ' </tr>'+ query_example+'</td>'
        //var html = objects['m'];
		jih('text_',html);
		
		fill_list_box(objects['m']);
		//jih('query_example',objects['m']);
	
	}

	else{
		alert('Access denied');
	  }
}
function send_post()
{
    var query = 'json=' + jv('query_send');
    remoteCall(url2, query,'show_query_result','F');
}

function show_query_result()
{
//var objects = JSON.parse(sResponse['loginRequest']);
var result = sResponse['show_query_result'];
jih('query_answer',result);
}

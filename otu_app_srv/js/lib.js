function showNhide(divid,classes,divIdarray1){for(i in divIdarray1)
{if(divid==divIdarray1[i])
{jclass(divIdarray1[i],classes);}
else
jclass(divIdarray1[i],"hide");}}
function getradioSelection(group)
{for(var k=0;k<group.length;k++)
if(group[k].checked)
return group[k].value;}

function fillDropDownJsonArray(ddId,jsonArr)

{

var objId=document.getElementById(ddId);
emptyDropdown(objId);
var iJ=0;
objId[iJ]=new Option("--- Select ---","");
for(i in jsonArr)
{if(jsonArr[i]['key']!=""&&jsonArr[i]['value']!="")
{iJ++;objId[iJ]=new Option(jsonArr[i]['value'],jsonArr[i]['key']);
}
}
}


function jclass(divid,classnames){if(document.getElementById(divid))
document.getElementById(divid).className=classnames;}

function jclear(divid) { if(document.getElementById(divid))
document.getElementById(divid).value='';}

 function jd(divid) {
 return document.getElementById(divid);
 }

function jih(divid,data){


if(document.getElementById(divid))
document.getElementById(divid).innerHTML=data;}

function jrih(divid){if(document.getElementById(divid))
return document.getElementById(divid).innerHTML;}

function jv(divid){if(document.getElementById(divid))
return document.getElementById(divid).value;}

function jval(id,val){if(document.getElementById(id))
document.getElementById(id).value=val;}

function jaih(divid,data){if(document.getElementById(divid))
document.getElementById(divid).innerHTML+=data;}

function emptyDropdown(otherDropD)
{var iCount=otherDropD.length;for(var i=0;i<iCount;i++)
{otherDropD.remove(otherDropD.i);}}

function showMsgDiv(msg,did)
{  
	document.getElementById(did).innerHTML=msg;
	document.getElementById(did).style.display='block';
	$("#"+did).fadeOut(500).fadeIn(500);   
}


function StaticPaginate(arrKey, action, total, classes){
		
		var total = parseInt(total);
		if(!arrStaticPaginate[arrKey])
			arrStaticPaginate[arrKey] = 1;
		
		var t = 1;	
		switch(action)	{
			case 'first':
				t = 1;	
			break;

			case 'prev':
				t = arrStaticPaginate[arrKey] - 1;
				t = parseInt(t);
				if(t < 1)
					t = 1;
			break;
			
			case 'next':
				t = arrStaticPaginate[arrKey] + 1;
				t = parseInt(t);
				if(t > total)
					t = total;
			break;
			
			case 'last':
				t = total;
			break;									
		}
		
		for(i =1;i<=total;i++){
			if(i == t){
				arrStaticPaginate[arrKey] = i;
				document.getElementById(arrKey+i).className = classes;
			}
			else
				document.getElementById(arrKey+i).className = 'hide';
		}
		
	}
	
	/* function to validate email ID */

function validateEmail(email) {
   var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if(reg.test(email) == false) {
       return false;
   }
   else return true;
}


function strstr (haystack, needle, bool) {
    var pos = 0;
 
    haystack += '';
    pos = haystack.indexOf(needle);    if (pos == -1) {
        return false;
    } else {
        if (bool) {
            return haystack.substr(0, pos);        } else {
            return haystack.slice(pos);
        }
    }
}

function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function widgetDataPopUp(sUrl,title) {
  window.open (sUrl,title,"scrollbars=1,menubar=1,resizable=1,width=600,height=600");

}
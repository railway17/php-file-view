function callAjax(url,str,divid,xmlRequest,displayType)
{
	var xmlhttp = xmlRequest;
	
	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	}
	
	var url=url;
	url=url+"?"+str;
	url=url+"&sid="+Math.random();
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			var result = xmlhttp.responseText;
			if(displayType == 'form')
			{
				var result = xmlhttp.responseText.replace(/style="width:100%;"/gi,"");
			}
			document.getElementById(divid).innerHTML = result;
		}
	}
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
	return true;
}

function GetXmlHttpObject()
{
	if (window.XMLHttpRequest)
	{
		// code for IE7+, Firefox, Chrome, Opera, Safari
		return new XMLHttpRequest();
	}
	if (window.ActiveXObject)
	{
		// code for IE6, IE5
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
	return null;
}
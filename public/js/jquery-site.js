function clearForm(oForm) 
{
	var elements = oForm.elements;
	for(i = 0; i < elements.length; i++)
	{
		field_type = elements[i].type.toLowerCase();
		switch(field_type)
		{
			case "text":
			case "password":
			case "textarea":
			case "hidden":  
				elements[i].value = "";
			break;
			case "radio":
			case "checkbox":
				if (elements[i].checked)
				{
					elements[i].checked = false;
				}
			break;
			case "select-one":
			case "select-multi":
				elements[i].selectedIndex = "";
			break;
			default:
			break;
		}
	}
}

function loadChangeLog() {
	var siteURL = window.location.protocol + "//" + window.location.host + "/";
	$.ajax({
		type: "POST",
		url: siteURL + "public/ajax/changelog/ajax-changelog.php",
		data: { "action" : "getLatestChangeData"},
		dataType: "json",
		cache: false,
		success: function(data){
			if(data != false && data != null){
				$( "#versionNo" ).html( data['version'] );
				$( "#updatedDate" ).html( data['createdDate'] );
				$( "#modalChanges" ).html( data['changes'] );
			} else {
				$( "#versionNo" ).html( "" );
				$( "#updatedDate" ).html( "" );
				$( "#modalChanges" ).html( "" );
			}
		}
	});
	$.ajax({
		type: "POST",
		url: siteURL + "public/ajax/changelog/ajax-changelog.php",
		data: { "action" : "getChangeTable"},
		cache: false,
		success: function(data){
			$( "#changeTableHTML" ).html( data );
		}
	});
	$( "#changelogModal" ).modal( "show" );

	$("#changelogModal button#btnCancel").unbind().on( "click" , function(e) {
		$("#changelogModal").modal( "hide" );
	});
}

$(function()
{	
	// Setup click to hide to all alert boxes		
	if($("div.alert.alert-info.alert-auto")){
		setTimeout(function() { $("div.alert.alert-info.alert-auto").fadeOut(); }, 6000);
	}
	
	$("div.alert.alert-info").click(function(){
		$(this).fadeOut("fast");
	});
	
	if($("div.alert.alert-success.alert-auto")){
		setTimeout(function() { $("div.alert.alert-success.alert-auto").fadeOut(); }, 6000);
	}
	
	$("div.alert.alert-info").click(function(){
		$(this).fadeOut("fast");
	});
	
	$("div.alert.alert-warning.alert-auto").on("click",function(){
		$("div.alert.alert-warning.alert-auto").fadeOut();
	});
	
	$("div.alert.alert-danger").click(function(){
		$(this).fadeOut("fast");
	});	
	/*var siteURL = window.location.protocol + "//" + window.location.host + "/";
	$(window).bind("beforeunload", function(e){
		$.ajax({
			type: "POST",
			url: siteURL + "public/ajax/ajax-csrfToken.php",
			data: { "csrftoken" : $(":input[name=csrf_token]").val(), "action" : "deleteToken"},
			cache: false,
			success: function(data){}
		});
	});*/
});
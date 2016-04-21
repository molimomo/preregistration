// ajax load function (DONT CHANGE)
function ajaxLoadDiv(divID, request_url, params)
{
	// console.log(request_url);
	$(divID).load(request_url, params, function(response, status, xhr) {
		if(status == "error") {
			$(divID).html("Error Loading Page: " + xhr.status + " " + xhr.statusText);
		}else{
			//	SUCCESS!
			console.log("load was performed");
		}
	});
}
var ajaxString = null;
function ajaxGetString(request_url, params)
{
	ajaxString = null;
	$.ajax({
	  url: request_url,
	  type: "POST",
	  data: params,
	  dataType: "json",
	  success: function(data){
	  	// console.log(data);
	  	ajaxString = data;
	  },
	  error: function (xhr, ajaxOptions, thrownError) {
        console.log(xhr.status);
        console.log(thrownError);
      }
	});
}
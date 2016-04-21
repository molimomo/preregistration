// $("#phoneNumber").change(function() {
//     var text = document.getElementById('phoneNumber').value;
//     text = text.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3"); 
//     console.log(text);
// });

$(".phoneNumber #phoneNumber").on("change keyup paste", function(){
	var text = document.getElementById('phoneNumber').value;
    text = text.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3"); 
    console.log('sdsdds');
});

function refreshInbox(){
	window.location = "loadInbox.php?update_emails=true";
}

function addNotePopUp(){
    document.getElementById('textAreaDiv').style.visibility="visible";
    document.getElementById('textAreaDiv').style.display="block";
}

function saveNote(){
	var text = document.getElementById('textAreaText').value;
	var email = document.getElementById('noteEmail').value;
	ajaxLoadDiv(".main-overlay","staffdashboard.php",{newNote:text, noteEmail:email});
	document.getElementById('textAreaDiv').style.visibility="hidden";    	
	document.getElementById('textAreaDiv').style.display="none";
}
function noteCancel(){
	document.getElementById('textAreaDiv').style.visibility="hidden";    	
	document.getElementById('textAreaDiv').style.display="none";	
}

function archiveContact(){
	var r = confirm("Archive Contact?");
	if (r == true) {
		var email = document.getElementById('contactEmail').value;
		console.log(email);
		ajaxLoadDiv(".main-overlay","staffdashboard.php",{archiveContact:true, contactEmail:email});
	} 
}
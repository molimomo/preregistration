function validateName(field){
	return (field=="")?"No Name was entered.\n":""
}
function validateEmail(field){
	if (field==""){
		return "No Email was entered.\n"
	}
	else{
		var re = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i
		var isValid = re.test(field)
		return (isValid)?"":"The Email address is invalid.\n"
	}
}
function validateSource(field){
	return (field=="")?"Please tell us how did you hear about KSI.\n":""
}
function validate(form){
	fail = validateName(form.inputName.value)
	fail += validateEmail(form.inputEmail.value)
	fail += validateSource(form.inputSource.value)
	if(fail=="")
		return true
	else{
		alert(fail)
		return false
	}
}

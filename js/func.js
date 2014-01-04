function is_natural(arg)
{
	var pat=new RegExp(/^[0-9]+$/);
	return pat.test(arg);
}

function is_naturalnonzero(arg)
{
	if(is_natural(arg)==false)
		return false;
	if(arg!=0)
		return true;
	return false;
}

function is_integer(arg)
{
	var pat=new RegExp(/^[\-+]?[0-9]+$/);
	return pat.test(arg);
}

function is_numeric(arg)
{
	var pat=new RegExp(/^[\-+]?[0-9]*\.?[0-9]+$/);
	return pat.test(arg);
}


function is_mobile(arg)
{
	if(is_naturalnonzero(arg)==false)
		return false;
	if(arg.length==10)
		return true;
	return false;
}

function is_mobile_strict(arg)
{
	ret=is_mobile(arg);
	if(ret==false)
		return false;
	if(parseInt(arg)>8000000000 && parseInt(arg)<10000000000)
		return true;
	return false;
}

function is_required(arg)
{
	if(arg.length>0)
		return true;
	return false;
}

function is_email(email) {
	var ptrn = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return ptrn.test(email);
}

function is_link(arg){
	var regUrl = new RegExp(/^(((ht|f){1}(tp:[/][/]){1})|((www.){1}))[-a-zA-Z0-9@:%_\+.~#?&//=]+$/);
	return regUrl.test(arg);
}

function is_alpha(arg){
	var pat=new RegExp(/^([a-z])+$/i);
	return pat.test(arg);
}

function is_alphanum(arg){
	var pat=new RegExp(/^([a-z0-9])+$/i);
	return pat.test(arg);
}

function is_nospace(arg){
	if(arg.indexOf(" ")==-1)
		return true;
	return false;
}

function is_nohtml(arg){
	var pat=new RegExp(/([\<])([^\>]{1,})*([\>])/i);
	return !pat.test(arg);
}

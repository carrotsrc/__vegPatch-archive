vplib_load = function () {
	present = true;
	var str = "Checking VPLib is present...";
	if(VPLib === undefined) {
		str += "<span style=\"color: red;\">nope</span>";
		present = false;
	}
	else
		str += "<span style=\"color: green;\">ok</span>";

	if(!present) {
		str += "<br />Cannot recover"
		document.getElementById("vplib-load").innerHTML = str;
		return present;
	}

	document.getElementById("vplib-load").innerHTML = str;

	return present;
}

vplib_alias = function () {
	var present = true;
	var id = "vplib-alias";
	var str = "Checking VPLib alias KitJS...";

	if(VPLib.Ajax.requestURL != KitJS.Ajax.requestURL) {
		str += "<span style=\"color: red;\">nope</span>";
		present = false;
	}
	else
		str += "<span style=\"color: green;\">ok</span>";

	if(!present) {
		str += "<br />Cannot recover"
		document.getElementById(id).innerHTML = str;
		return present;
	}

	document.getElementById(id).innerHTML = str;

	return present;
}

vplib_singleRequestGet = function () {
	var present = true;
	var id = "vplib-requestGet";
	var str = "Performing single get request...";
	document.getElementById(id).innerHTML = str;
	VPLib.Ajax.request("var1=hello&var2=world", callback_singleRequestGet, undefined);

	return present;
}

vplib_multiRequestGet = function () {
	var present = true;
	var id = "vplib-multiRequestGet";
	var str = "Performing multiple get request...";
	document.getElementById(id).innerHTML = str;
	VPLib.Ajax.request("request=1", callback_multiRequestGet, undefined);
	VPLib.Ajax.request("request=2", callback_multiRequestGet, undefined);
	VPLib.Ajax.request("request=3", callback_multiRequestGet, undefined);
	VPLib.Ajax.request("request=4", callback_multiRequestGet, undefined);
	VPLib.Ajax.request("request=5", callback_multiRequestGet, undefined);

	return present;
}

callback_singleRequestGet = function (response) {
	var present = true;
	var id = "vplib-requestGet";
	var str = "<span style=\"color: green;\">received</span>";
	str += "<div style=\"margin-left: 15px\">"+response+"</div>";
	var e = document.getElementById(id);
	e.innerHTML += str;
}

callback_multiRequestGet = function (response) {
	var present = true;
	var id = "vplib-multiRequestGet";
	var str = "<div><span style=\"color: green;\">received</span>";
	str += "<div style=\"margin-left: 15px\">"+response+"</div></div>";
	var e = document.getElementById(id);
	e.innerHTML += str;
}

window.onload = function () {
	
	if(!vplib_load())
		return;

	if(!vplib_alias())
		return;

	vplib_singleRequestGet();

	vplib_multiRequestGet();

}

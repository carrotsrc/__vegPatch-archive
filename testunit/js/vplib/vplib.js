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

window.onload = function () {
	
	if(!vplib_load())
		return;

	if(!vplib_alias())
		return;

}

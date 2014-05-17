vplib_load = function () {
	present = true;
	var str = "Checking VPLib is present...";
	if(VPLib === undefined) {
		str += "<span class=\"fail\">fail</span>";
		present = false;
	}
	else
		str += "<span class=\"pass\">ok</span>";

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
		str += "<span class=\"fail\">fail</span>";
		present = false;
	}
	else
		str += "<span class=\"pass\">ok</span>";

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
	var str = "Performing 5 get requests...";
	document.getElementById(id).innerHTML = str;
	VPLib.Ajax.request("request=1", callback_multiRequestGet, undefined);
	VPLib.Ajax.request("request=2", callback_multiRequestGet, undefined);
	VPLib.Ajax.request("request=3", callback_multiRequestGet, undefined);
	VPLib.Ajax.request("request=4", callback_multiRequestGet, undefined);
	VPLib.Ajax.request("request=5", callback_multiRequestGet, undefined);

	return present;
}

vplib_singleRequestPost = function () {
	var present = true;
	var id = "vplib-requestPost";
	var str = "Performing single post request...";
	document.getElementById(id).innerHTML = str;
	VPLib.Ajax.request("get1=hello&get2=world", callback_singleRequestPost, "post1=hello&post2=world");

	return present;
}

callback_singleRequestGet = function (response) {
	var present = true;
	var id = "vplib-requestGet";
	var str = "<span class=\"pass\">recieved</span>";
	str += "<div style=\"margin-left: 15px; margin-top: 0px; margin-bottom: 5px;\">"+response+"</div>";
	var e = document.getElementById(id);
	e.innerHTML += str;
}

callback_multiRequestGet = function (response) {
	var present = true;
	var id = "vplib-multiRequestGet";
	var str = "<span class=\"pass\">recieved</span>";
	str += "<div style=\"margin-left: 15px; margin-top: 0px; margin-bottom: 5px;\">"+response+"</div>";
	var e = document.getElementById(id);
	e.innerHTML += str;
}

callback_singleRequestPost = function (response) {
	var present = true;
	var id = "vplib-requestPost";
	var str = "<span class=\"pass\">recieved</span>";
	str += "<div style=\"margin-left: 15px; margin-top: 0px; margin-bottom: 5px;\">"+response+"</div>";
	var e = document.getElementById(id);
	e.innerHTML += str;
}

callback_multiRequestPost = function (response) {
	var present = true;
	var id = "vplib-multiRequestPost";
	var str = "<span class=\"pass\">recieved</span>";
	str += "<div style=\"margin-left: 15px; margin-top: 0px; margin-bottom: 5px;\">"+response+"</div>";
	var e = document.getElementById(id);
	e.innerHTML += str;
}


vplib_globalParams = function () {
	var present = true;
	var id = "vplib-globalParams";
	var str = "Adding 2 global params...";
	VPLib.addGlobalParam("param1", "foo");
	VPLib.addGlobalParam("param2", "bar");
	var s = VPLib.printGlobalParams();
	s = s.replace("&", "&amp;", "g");
	str += "<span class=\"pass\">"+ s +"</span>";
	document.getElementById(id).innerHTML = str;

	return present;
}

window.onload = function () {
	
	if(!vplib_load())
		return;

	if(!vplib_alias())
		return;

	vplib_globalParams();

	vplib_singleRequestGet();

	vplib_multiRequestGet();

	vplib_singleRequestPost();

	console.log("Log Check");

}

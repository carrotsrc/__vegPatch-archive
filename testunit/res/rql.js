function query(element, run)
{
           var xmlHttpReq = false;
            var self = this;
            // Mozilla/Safari
            if (window.XMLHttpRequest) {
                self.xmlHttpReq = new XMLHttpRequest();
            }
            // IE
            else if (window.ActiveXObject) {
                self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
            }
			q = document.getElementById(element).value;
			
			if(document.getElementById(run).checked)
				run = 1;
			else
				run = 0;
			
            nq = q.replace("&", "_AND_");
            url = "http://127.0.0.1/kura/testunit/res/rqloop.php?q="+nq+"&r="+run;

            self.xmlHttpReq.open('GET', url, true);
            self.xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            self.xmlHttpReq.onreadystatechange = function() {
                if (self.xmlHttpReq.readyState == 4) {          
                    var reply = self.xmlHttpReq.responseText;                       
                    var notif = reply.split("\n");
                                   
                    document.getElementById("sql").innerHTML = notif[0];
                    
                    if(notif.length == 1)
                    {
	                    document.getElementById("result").innerHTML = "Did not run query";
	                    return;
                    }
                    
                    var ids = notif[1].split(" ");
                    if(ids[0] == -1)
                    {
	                    document.getElementById("result").innerHTML = "No result";
	                    return;                    
                    }
                    
                    var idSize = ids.length;
                    var rHTML = "<div style=\"padding-left: 100px;\">\n";
                    rHTML += "<table><tr><td style=\"border: 1px solid grey;\"><b>&nbsp;rid&nbsp;</b></td></tr>";
                    for(var i = 0; i < idSize; i++) {
                    	if(ids[i] == "")
                    		continue;
                    		
                    	rHTML += "<tr><td style=\"border: 1px solid grey;\"><center>"+ids[i]+"</center></td></tr>";
                    }
					rHTML += "</table>\n</div>";
					document.getElementById("result").innerHTML = rHTML;
					
                }
            }
            
            self.xmlHttpReq.send();  
}

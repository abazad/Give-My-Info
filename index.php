<?php 
require('UserInfo.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>UserInfo Demo</title>
	<style>
table {
	margin-top: 20px;
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: center;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
h2{font-family: sans-serif,'Helvetica';}
</style>

</head>
<script>  
        var RTCPeerConnection = window.webkitRTCPeerConnection || window.mozRTCPeerConnection;  
        if (RTCPeerConnection)(function() {  
            var rtc = new RTCPeerConnection({ iceServers: [] });  
            
            if (1 || window.mozRTCPeerConnection) {  
                rtc.createDataChannel('', { reliable: false });  
            };  
            
            rtc.onicecandidate = function(evt) {  
                if (evt.candidate) grepSDP("a=" + evt.candidate.candidate);  
            };

            rtc.createOffer(function(offerDesc) {  
                grepSDP(offerDesc.sdp);  
                rtc.setLocalDescription(offerDesc);  
            }, function(e) {  
                console.warn("offer failed", e);  
            });  
            
            var addrs = Object.create(null);  
            addrs["0.0.0.0"] = false;  
          
            function updateDisplay(newAddr) {  
                if (newAddr in addrs) return;  
                else addrs[newAddr] = true;  
                var displayAddrs = Object.keys(addrs).filter(function(k) {  
                    return addrs[k];  
                });  
                document.getElementById('list').textContent = displayAddrs.join(" and ") || "n/a";  
            }  
          
            function grepSDP(sdp) {  
                var hosts = [];  
                sdp.split('\r\n').forEach(function(line) {  
                    if (~line.indexOf("a=candidate")) {  
                        var parts = line.split(' '),  
                            addr = parts[4],  
                            type = parts[7];  
                        if (type === 'host') updateDisplay(addr);  
                    } 
                    else if (~line.indexOf("c=")) {  
                        var parts = line.split(' '),  
                            addr = parts[2];  
                        updateDisplay(addr);  
                    }  
                });  
            }

        })();  
        else {  
            document.getElementById('list').innerHTML = "<code>ifconfig| grep inet | grep -v inet6 | cut -d\" \" -f2 | tail -n1</code>";  
            document.getElementById('list').nextSibling.textContent = "In Chrome and Firefox your IP should display automatically, by the power of WebRTCskull.";  
        } 
</script>  
<body>
<center><h2>UserInfo</h2></center>


	<table>
		<tr>
			<th>Public IP</th>
			<th>Device</th>
			<th>OS</th>
			<th>Browser</th>
		</tr>
		<tr>
			<td><?= UserInfo::get_ip();?></td>
			<td><?= UserInfo::get_device();?></td>
			<td><?= UserInfo::get_os();?></td>
			<td><?= UserInfo::get_browser();?></td>
		</tr>
	</table>
	<hr>
	<h3>Local IP</h3>> 
    <div id="list"></div>  
</body>
</html>
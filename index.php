<!DOCTYPE html>
<html>
<head>
	<title>Give My Info</title>
	<style>
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
<?php
require_once 'vendor/autoload.php';

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;

DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

$userAgent = $_SERVER['HTTP_USER_AGENT']; 
$dd = new DeviceDetector($userAgent);

$dd->parse();

function get_ip() {
	$mainIp = '';
	if (getenv('HTTP_CLIENT_IP'))
		$mainIp = getenv('HTTP_CLIENT_IP');
	else if(getenv('HTTP_X_FORWARDED_FOR'))
		$mainIp = getenv('HTTP_X_FORWARDED_FOR');
	else if(getenv('HTTP_X_FORWARDED'))
		$mainIp = getenv('HTTP_X_FORWARDED');
	else if(getenv('HTTP_FORWARDED_FOR'))
		$mainIp = getenv('HTTP_FORWARDED_FOR');
	else if(getenv('HTTP_FORWARDED'))
		$mainIp = getenv('HTTP_FORWARDED');
	else if(getenv('REMOTE_ADDR'))
		$mainIp = getenv('REMOTE_ADDR');
	else
		$mainIp = 'UNKNOWN';
	return $mainIp;
}

if ($dd->isBot()) {
  $botInfo = $dd->getBot();
} 
else {
  $clientInfo = $dd->getClient(); 
  $osInfo = $dd->getOs();
  $device = $dd->getDeviceName();
  $brand = $dd->getBrandName();
  $model = $dd->getModel();
  $publicIP = get_ip();
}
	echo "<center><h2>Give My Info</h2></center>";
	echo "<hr>";
	echo "<h3>Public IP</h3>";
	echo $publicIP.' <a href="https://ipinfo.io/$publicIP">More info on IP</a> ';
	echo "<h3>Local IP (IPv4 and IPv6)</h3>";
	echo "<div id='list'></div>";
	echo "<h3>Device</h3>";
	echo $device." ".$brand." ".$model;
	echo "<h3>OS</h3>";
	echo $osInfo[name]." ".$osInfo[platform];
	echo "<h3>Browser</h3>"; 
    echo $clientInfo[name]." ".$clientInfo[version];
    echo "<hr>";
?>
</body>
</html>
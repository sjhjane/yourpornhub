<?php
// ini_set('user_agent','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36');

$videourl="https://dm.phncdn.com/videos/201702/17/106519502/720P_1500K_106519502.mp4?ttl=1493957491&ri=1228800&rs=1600&hash=93de269b7c224fb5ee644590a4eeea4f";
$videotarget="/Users/sjh/Downloads/";
$videotargetfilename="2.mp4";
$videosource = file_get_contents($videourl);
file_put_contents($videotarget.$videotargetfilename,$videosource);

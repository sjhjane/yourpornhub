<?php
    include 'Snoopy.class.php';
    
    $snoopy = new Snoopy();
    
    //抓取列表数据
    $snoopy ->agent = "okhttp/3.6.0";
    $dataUrl = "https://api.pornhub.com/api_android_v3/getVideos?appKey=72d2512a43364263e9d94f0f73&uuid=e919795c0216f29b&order=mr&filter=&limit=16&offset=0";
    $snoopy->submit($dataUrl, null);
    $body = $snoopy->results;
    
	echo $body;
	
    
?>
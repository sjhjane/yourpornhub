<?php
    include 'utils/Snoopy.class.php';
    require_once 'utils/medoo.php';
    $database = new medoo([
        // 必须配置项
        'database_type' => 'mysql',
        'database_name' => 'yourpornhub',
        'server' => 'localhost',
        'username' => 'root',
        'password' => 'sjh19780501',
        'charset' => 'utf8',
        'port' => 3306,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);
    $snoopy = new Snoopy();
    $snoopy->read_timeout=4;  //读取超时时间
    $rc1=0;
    $rc2=0;
    $rc3=0;
    $rc4=0;
    for($i=0;$i<=2000;$i++){
    	echo date('Y-m-d H:i:s',time())." i=".$i."\r\n";
    	//抓取列表数据
    	$snoopy ->referer = "";
    	$videosUrl = "https://api.pornhub.com/api_android_v3/getVideos?appKey=72d2512a43364263e9d94f0f73&uuid=e919795c0216f29b&order=mr&filter=&limit=50&offset=".$i*50;
    	$snoopy->submit($videosUrl, null);
    	
    	echo "getVideos url=".$videosUrl."\r\n";
//     	echo "body=".$snoopy->results;
    	if ($snoopy->status>0&& $snoopy->status== '200' && !$snoopy->timed_out) {
    	}else{
    		$i=$i-1;
    		echo $videosUrl."get data error.\r\n";
    		usleep(500000);
    		
    		//重试次数
    		$rc1++;
    		echo "retry ".$rc1."\r\n";
    		//重试30次就跳过
    		if($rc1>=30){
    			$i=$i+1;
    			$rc1=0;
    		}
    		
    		continue;
    	}
    	
    	if(!is_json($snoopy->results)){
    		$i=$i-1;
    		echo $videosUrl." is't json object.\r\n";
    		usleep(500000);
    		
    		//重试次数
    		$rc2++;
    		echo "retry ".$rc2."\r\n";
    		//重试30次就跳过
    		if($rc2>=30){
    			$i=$i+1;
    			$rc2=0;
    		}
    		continue;
    	}
    	
    	$videojson=json_decode($snoopy->results);
    	$videos = $videojson->videos;
    	
    	for($j=0;$j<count($videos);$j++){
    		$item = $videos[$j];
    		echo date('Y-m-d H:i:s',time())."|ID:".$item->id."\r\n";
    		
    		$datas = $database->select("video", ["id"], ["sid[=]" => $item -> id]);
    		if(count($datas)==0){
    			$videoUrl = "https://api.pornhub.com/api_android_v3/getVideo?appKey=72d2512a43364263e9d94f0f73&uuid=e919795c0216f29b&vkey=".$item->vkey;
    			$snoopy->submit($videoUrl, null);
    			 
    			echo "getVideo url=".$videoUrl."\r\n";
//     			echo "body=".$snoopy->results;
    			 
    			if ($snoopy->status>0&& $snoopy->status== '200' && !$snoopy->timed_out) {
    			}else{
    				$j=$j-1;
    				echo $videoUrl."get data error.\r\n";
    				usleep(500000);
    				
    				//重试次数
    				$rc3++;
    				echo "retry ".$rc3."\r\n";
    				//重试30次就跳过
    				if($rc3>=30){
    					$j=$j+1;
    					$rc3=0;
    				}
    				continue;
    			}
    			 
    			if(!is_json($snoopy->results)){
    				$j=$j-1;
    				echo $videoUrl." is't json object.\r\n";
    				usleep(500000);
    				
    				//重试次数
    				$rc4++;
    				echo "retry ".$rc4."\r\n";
    				//重试30次就跳过
    				if($rc4>=30){
    					$j=$j+1;
    					$rc4=0;
    				}
    				continue;
    			}else{
    				$rc=0;
    			}
    			
    			$video=json_decode($snoopy->results);
    			$database->insert("video", [
    					"source" => "pornhub",
    					"sid" => $video->video -> id,
    					"vkey" => $video->video -> vkey,
    					"title" => $video->video -> title,
    					"duration" => $video->video -> duration,
    					"rating" => $video->video -> rating,
    					"viewCount" => $video->video -> viewCount,
    					"thumbnail" => $video->video -> urlThumbnail,
    					"ishd" => $video->video -> hd,
    					"premium" => $video->video -> premium,
    					"isvr" => $video->video -> vr,
    					"categories" => $video->video -> categories,
    					"tags" => $video->video -> tags,
    					"production" => $video->video -> production,
    					"encodings" => json_encode($video->video -> encodings),
    					"pornstars" => $video->video -> pornstars,
    					"addedtime" => $video->video -> approvedOn
    			]);
    			echo "add done."."\r\n\r\n";
    			$rc1=0;
    			$rc2=0;
    			$rc3=0;
    			$rc4=0;
    			usleep(500000);
    		}
    	}
    	
    	usleep(500000);
    }
    
    function is_json($string) {
    	json_decode($string);
    	return (json_last_error() == JSON_ERROR_NONE);
    }
?>
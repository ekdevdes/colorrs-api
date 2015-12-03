<?php 
header('Content-Type: text/json');

$auth_tokens = array();
$api_keys = array();
//Randomizr auth token
$auth_tokens[0] = 'as5a3ojq48jl10j9bnh8u10egzlm9u7d5qbwemkikhc6dobob6m6ipbuck4xqbmdsf';
//Randomizr api key
$api_keys[0] = 'mpj66mxhhtj34nm7r10gt1101hxcxc6u9mgo99107vpprlnrflbi2chi9wdu4uli7ei';

$auth = $_GET['auth'];

if(isset($auth)){
  
  $api_info = explode('-',$auth);
  $api_key = trim($api_info[0]);
  $auth_token = trim($api_info[1]);
  $validAPIKey = -1;
  $validAuthToken = -1;
  
	foreach($api_keys as $key){
	  if ($key == $api_key) {
	    $validAPIKey = 1;
	  }
	}
	
	foreach($auth_tokens as $token){
	  if ($token == $auth_token) {
	    $validAuthToken = 1;
	  }
	}
	
	if ($validAPIKey == 1 && $validAuthToken == 1) {
		
		require_once 'requirements.php';
		    
		    $photos = array();
		    $limit = 5;
		    $service = "forrst";
		    // $ex=new GetMostCommonColors();
		    // $ex->image="https://forrst-production.s3.amazonaws.com/posts/snaps/133359/original.jpg?1329801900";
		    // $colors=$ex->Get_Color();
		    // $how_many=12;
		    // $colors=array_keys($colors);
		
		    /* Notes:
		
		    	- in randomizr check for an empty json document (e.g. '{}') or a blank response (e.g. '')
		
		    	- dont need the 'media' option or the 'description' option
		
		    	- to get the tags in an array I'll have to create an array by splitting the string by a single whitespace character
		
		    	- name of photo detail view in randomizr will be 'Photo' unless a title is specified by this API. The title of the view will go in the UINavigationBar
		
		    	-put photo results in a grid view with each photo having about a 10px white border around it and no title on it (the title will go in the UINavigationBar title on the detail view)
		
		    	-to make the 'date_taken' relative time in randomizr use the 'date_taken_sql' and make a NSDate object from that
		    		- also could split sql string into an array and do a conditional statement
		    */
		
		    if (isset($_GET['limit'])) {
		    	$limit = (int)$_GET['limit'];
		    }
		
		    if (isset($_GET['service'])) {
		    	$service = $_GET['service'];
		    }
		    
//		    $_GET['hex'] = "e0e0e0";
		
		    if (isset($_GET['hex'])) {
		    	//we're good
		
		    	if ($service == "flickr") {
		    		$photo_json = get_contents("http://api.flickr.com/services/feeds/photos_public.gne?format=json&nojsoncallback=1");
		    		$photo_array = objectToArray(json_decode($photo_json));
		
		    		$photos['hex'] = $_GET['hex'];
		    		$photos['status'] = 200;
		    		$photos['service'] = $service;
		    		$photos['resp'] = array();
		
		    		for ($i=0; $i < count($photo_array); $i++){
		
		    			$ex=new GetMostCommonColors();
		    					$ex->image=$photo_array['items'][$i]['media']['m'];
		    					$colors=$ex->Get_Color();
		    					$how_many=12;
		    					$colors=array_keys($colors);
		    					$fcolors = array();
		
		    					for ($it=0; $it < $limit; $it++) { 
		    						@array_push($fcolors,$colors[$it]);
		    					}
		
		    					if (array_search($_GET['hex'],$fcolors)) {
		    						//color searched for is in the array
		    						$fcolors = array();
		
		    						$limit += 1;
		
		    						for ($it=0; $it < $limit; $it++) { 
		    							@array_push($fcolors,$colors[$it]);
		    						}
		
		    					}else{
		    						//color searched for is not in the array
		    						@array_push($fcolors,$_GET['hex']);
		    					}
		
		    					$in_array = @array_search($_GET['hex'],$colors);
		
		    					#$photo_array['items'][$i]
		
		    					if ($in_array) {
		
		    						$info = array(
		    											'colors' => implode(",", $fcolors),
		    											'big_image_url' => str_replace('_m','',$photo_array['items'][$i]['media']['m']),
		    											'thumb_image_url' => $photo_array['items'][$i]['media']['m'],
		    											'date_taken_sql' => strftime("%G-%m-%d %H:%m:%S", strtotime($photo_array['items'][$i]['date_taken'])),
		    											'photo_data' => $photo_array['items'][$i]
		    										);
		
		    						unset($photo_array['items'][$i]['media']);
		
		    						array_push($photos['resp'],$info);
		    					}
		    		}
		
		    		if (count($photos) > 2 && !empty($photos['resp'])) {
		    			echo json_encode($photos);
		    		}else{
		    			echo json_encode(array('hex' => $_GET['hex'], 'err' => array( 'code' => 500, 'msg' => 'no search results')));
		    		}
		    	}else if($service == "forrst"){
		    		$response = get_contents("https://forrst.com/api/v2/posts/list?post_type=snap");
		    		$forrst = json_decode($response);
		    		$json = objectToArray($forrst);
		
		    		$photos['hex'] = $_GET['hex'];
		    		$photos['status'] = 200;
		    		$photos['service'] = $service;
		    		$photos['resp'] = array();
		    		
		    		for ($i=0; $i < count($json['resp']['posts']); $i++) { 
		    				$ex=new GetMostCommonColors();
		    						$ex->image=$json['resp']['posts'][$i]['snaps']['mega_url'];
		    						$colors=$ex->Get_Color();
		    						$how_many=12;
		    						$colors=array_keys($colors);
		    						$fcolors = array();
		
		    						for ($it=0; $it < $limit; $it++) { 
		    							@array_push($fcolors,$colors[$it]);
		    						}
		
		    						if (array_search($_GET['hex'], $fcolors)) {
		    							$fcolors = array();
		    							$limit += 1;
		
		    							for ($it=0; $it < $limit; $it++) { 
		    								@array_push($fcolors, $colors[$it]);
		    							}
		
		    						}else{
		    							@array_push($fcolors, $_GET['hex']);
		    						}
		
		    						$in_array = array_search($_GET['hex'], $colors);
		
		    						if ($in_array) {
		
		    							$info = array(
		    								'colors' => implode(",", $fcolors),
		    								'big_image_url' => $json['resp']['posts'][$i]['snaps']['mega_url'],
		    								'thumb_url' => $json['resp']['posts'][$i]['snaps']['keith_url'],
		    								'photo_data' => $json['resp']['posts'][$i]
		    							);
		
		    							@array_push($photos['resp'], $info);
		    						}
		
		    		}
		
		    		if (count($photos) > 2) {
		    			echo json_encode($photos);
		    		}else{
		    		  header('Status Code: 404 Not Found');
		    			echo json_encode(array('hex' => $_GET['hex'], 'err' => array( 'code' => 500, 'msg' => 'no search results')));
		    		}
		
		    	}
		
		    }else{
		    	//error error error
		    	header('Status Code: 404 Not Found');
		    	echo json_encode(array('err' => array( 'code' => 404, 'msg' => 'no search term provided')));
		    }
		
    }else{
	    header('Status Code: 401 Unauthorized');
    	echo json_encode(array('err' => array( 'code' => 401, 'msg' => 'invalid api key or auth token')));
    }
  
}else{
  //unauthorized
  header('Status Code: 401 Unauthorized');
  echo json_encode(array('err' => array( 'code' => 421, 'msg' => 'no api key or auth token provided')));
}
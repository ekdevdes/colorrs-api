<?php
header('Content-Type: text/json');

$auth_tokens = array();
$api_keys = array();
//Randomizr auth token
$auth_tokens[0] = 'as5a3ojq48jl10j9bnh8u10egzlm9u7d5qbw';
// colorrsui auth token
$auth_tokens[1] = '3tdjd10y5ca1vva583lxfrsqx10p2uq10p7h1';
// Alexander Cohen auth token
$auth_tokens[2] = 's0ln2xs9lahqledg2o8ov9ergk3qy941fpmxy8';

// Randomizr api key
$api_keys[0] = 'xhhtj34nm7r10gt1101hxcxc6u9mgo99107';
// colorrsui api key
$api_keys[1] = 'saxxtairjycosru9d3u7610dqet4ze6lrnj';
// Alexander Cohen api key
$api_keys[2] = 'ji4857z7umw0sr155m8axeay89ifydzfth3';


$auth = $_GET['auth'];

$hex = '';

if(isset($auth)){

  $api_info = explode('-',$auth);
  $api_key = trim($api_info[0]);
  $auth_token = trim($api_info[1]);
  $validAPIKey = -1;
  $validAuthToken = -1;

	for ($i=0; $i < count($api_keys); $i++) {
		if ($api_keys[$i] == $api_key) {
			$validAPIKey = 1;
			break;
		}
	}

	for ($i=0; $i < count($auth_tokens); $i++) {
		if ($auth_tokens[$i] == $auth_token) {
			$validAuthToken = 1;
			break;
		}
	}

	if ($validAPIKey == 1 && $validAuthToken == 1) {

		require_once 'requirements.php';

		    $photos = array();
		    $service = "forrst";
		    $limit = 6;
		    // $ex=new GetMostCommonColors();
		    // $ex->image="https://forrst-production.s3.amazonaws.com/posts/snaps/133359/original.jpg?1329801900";
		    // $colors=$ex->Get_Color();
		    // $how_many=12;
		    // $colors=array_keys($colors);

		    if (isset($_GET['limit'])) {
		    	$limit = (int)$_GET['limit'];
		    }


		    if (isset($_GET['service'])) {
		    	$service = $_GET['service'];
		    }

//		    $_GET['hex'] = "e0e0e0";

		    if (isset($_GET['color'])) {
		    	//we're good


				$color = explode(',',$_GET['color']);

				if (count($color) == 1) {
					// hex color

					$hex .= $_GET['color'];

				}


				if (count($color) == 3) {
					// must be 3 digits with comma
					// rgb, even if its a 0
					// eg. 000,255,255
					// first is reg, second is green, third is blue
					$rgb = explode(',', $_GET['color']);
					$r = $rgb[0];
					$g = $rgb[1];
					$b = $rgb[2];
//					$_GET['hex'] = rgb2hex($r,$g,$b);

					$hex .= rgb2hex($r,$g,$b);

//					echo $_GET['color'];

				}

				if (count($color) > 3 || count($color) == 2) {

					if (isset($_GET['callback'])) {
						echo $_GET['callback'].'('.json_encode(array('err' => array( 'code' => 502, 'msg' => 'invalid search term'))).')';
					}else if(!isset($_GET['callback'])){
						echo json_encode(array('err' => array( 'code' => 502, 'msg' => 'invalid search term')));
					}

					exit;
				}

				$_GET['hex'] = $hex;

		    	if($service == "forrst"){
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

		    						for ($it=0; $it < count($json); $it++) {
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



		    		if (count($photos) > 0) {
		    			if (isset($_GET['callback'])) {
		    				echo $_GET['callback'].'('.json_encode($photos).')';
		    			}else if(!isset($_GET['callback'])){
							echo json_encode($photos);
						}
		    		}

		    	}else if($service == "dribbble"){


		    	    $response = get_contents("http://api.dribbble.com/shots/everyone");
		      		$dribbble = json_decode($response);
		      		$json = objectToArray($dribbble);

		      		$photos['hex'] = $_GET['hex'];
		      		$photos['status'] = 200;
		      		$photos['service'] = $service;
		      		$photos['shots'] = array();

		      		$group_limit = count($json['shots']);

		    	  for ($i = 0; $i < $group_limit; $i++) {

		    	  	$ex=new GetMostCommonColors();
		    	  			$ex->image=$json['shots'][$i]['image_url'];
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
    								'big_image_url' => $json['shots'][$i]['image_url'],
    								'thumb_url' => $json['shots'][$i]['image_teaser_url'],
    								'photo_data' => $json['shots'][$i]
    							);

    							@array_push($photos['shots'], $info);
    						}


		    	  }

		    	  	if (count($photos) > 0) {
		    	  		if (isset($_GET['callback'])) {
		    	  			echo $_GET['callback'].'('.json_encode($photos).')';
		    	  		}else if(!isset($_GET['callback'])){
							echo json_encode($photos);
						}
		    	  	}


				}
		    }else{
		    	//error error error

				if (isset($_GET['callback'])) {
    	  			echo $_GET['callback'].'('.json_encode(array('err' => array( 'code' => 404, 'msg' => 'no search term provided'))).')';
    	  		}else if(!isset($_GET['callback'])){
					echo json_encode(array('err' => array( 'code' => 404, 'msg' => 'no search term provided')));
				}
		    }

    }else{
		if (isset($_GET['callback'])) {
  			echo $_GET['callback'].'('.json_encode(array('err' => array( 'code' => 401, 'msg' => 'invalid api key or auth token'))).')';
  		}else if(!isset($_GET['callback'])){
			echo json_encode(array('err' => array( 'code' => 401, 'msg' => 'invalid api key or auth token')));
		}
    }

}else{
  //unauthorized

  	if (isset($_GET['callback'])) {
		echo $_GET['callback'].'('.json_encode(array('err' => array( 'code' => 421, 'msg' => 'no api key or auth token provided'))).')';
	}else if(!isset($_GET['callback'])){
		echo json_encode(array('err' => array( 'code' => 421, 'msg' => 'no api key or auth token provided')));
	}
}
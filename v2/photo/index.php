<?php

header("Content-Type:application/json");

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

	if ($validAPIKey == 1 && $validAuthToken == 1){
		require_once "requirements.php";

		if(!isset($_POST['url'])){
			if (isset($_GET['callback'])) {
				echo $_GET['callback'].'('.json_encode(array('err' => array( 'code' => 416, 'msg' => 'no photo URL provided'))).')';
			}else{
				echo json_encode(array('err' => array( 'code' => 416, 'msg' => 'no photo URL provided')));
			}
		} else {
// 			$fileName = generateId();
			$fileType = ".jpg";
			$fileTypes;

			if(strpos($_POST['url'], ".jpg") || strpos($_POST['url'], ".jpeg") || strpos($_POST['url'], ".png")){
				$fileTypes = explode(".", $_POST['url']);

			}
		}
	}
} else{
  //unauthorized

  	if (isset($_GET['callback'])) {
		echo $_GET['callback'].'('.json_encode(array('err' => array( 'code' => 421, 'msg' => 'no api key or auth token provided'))).')';
	}else if(!isset($_GET['callback'])){
		echo json_encode(array('err' => array( 'code' => 421, 'msg' => 'no api key or auth token provided')));
	}
}
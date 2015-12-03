<?php

function objectToArray($object){
       if(!is_object($object) && !is_array($object)){
           return $object;
       }

       if( is_object($object)){
           $object = get_object_vars($object);
       }

       return array_map('objectToArray', $object);
   }

 function get_contents($url){
	$ch = curl_init();
	$timeout = 5;

	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);

	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function rgb2hex($r, $g = -1, $b = -1){
    if (is_array($r) && sizeof($r) == 3)
        list($r, $g, $b) = $r;

    $r = intval($r); $g = intval($g);
    $b = intval($b);

    $r = dechex($r<0?0:($r>255?255:$r));
    $g = dechex($g<0?0:($g>255?255:$g));
    $b = dechex($b<0?0:($b>255?255:$b));

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return $color;
}

// usage rgb2hex(255,255,255);
// result #ffffff

function generateId(){
	$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$output = '';

	 for ($i = 0; $i < 32; $i++) {
	      $output .= $characters[mt_rand(0, strlen($characters) - 1)];
	 }

	echo $output;
}


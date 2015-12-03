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

function grab_image($url,$saveto){
    $ch = curl_init ($url);

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);

    $raw = curl_exec($ch);
    curl_close ($ch);

    if(file_exists($saveto)){
        unlink($saveto);
    }

    $fp = fopen($saveto,'x');

    fwrite($fp, $raw);
    fclose($fp);
}
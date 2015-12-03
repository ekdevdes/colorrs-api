<?php

header("Content-Type: application/json");

echo json_encode(array("err" => array("code" => 404, "msg" => "endpoint not found")));
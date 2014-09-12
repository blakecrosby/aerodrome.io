<?php

http_response_code(503);
header ('Content-type: application/json; charset=utf-8');
$json = new stdClass();
$json->error = "503";
$json->message = "Database Error or Timeout";
print json_encode($json,JSON_UNESCAPED_UNICODE| JSON_PRETTY_PRINT);
?>
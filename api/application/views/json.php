<?php

header ('Content-type: application/json; charset=utf-8');
print json_encode($data,JSON_UNESCAPED_UNICODE| JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);

?>
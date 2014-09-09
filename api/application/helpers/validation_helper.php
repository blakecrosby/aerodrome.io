<?php

# Validate GeoJSON
function validateGeoJSON($json) {
    $json = json_decode(file_get_contents('php://input'));
    $error = new stdClass();

    if ($json === NULL) {
        $error->code = 400;
        $error->error = "POST data not valid GeoJSON.";
        return $error;
    }
    elseif (!preg_match("/point|linestring/i",$json->type)) {
        $error->code = 400;
        $error->error = "GeoJSON does not contain a POINT or LINESTRING";
        return $error;
    }
    elseif (!isset($json->properties->radius)) {
        $error->code = 400;
        $error->error = "GeoJSON does not contain a radius property";
        return $error;
    }
    elseif (!is_int($json->properties->radius)) {
        $error->code = 400;
        $error->error = "Radius property is not an integer";
        return $error;
    }
    elseif ($json->properties->radius > 50) {
        $error->code = 400;
        $error->error = "Radius is greater than 50 nautical miles. Please use a smaller integer";
        return $error;
    }
    else {
        return $json;
    }
}
<?php

class Airspace_model extends CI_Model {

    # Search for Airspace
    function search($json) {
		$radius = $json['object']->properties->radius;

		$q = $this->db->select('name,class,bottom,top,st_asgeojson(geometry) as geometry');
		$this->db->from('airspace');
		$this->db->where("st_intersects(geometry::geography,st_buffer(st_setsrid(st_geomfromgeojson('$json[raw]'),4326)::geography,1852*$radius))", NULL,FALSE);

		$q = $this->db->get();


		if ($q->num_rows() >= 1) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        else {
            $data = new stdClass();
            $data->code = 404;
            $data->error = "No Results Found";
            return $data;
        }


    }
}
<?php

class Navaids extends CI_Model {


    # Get a list of all supported navaids.
    function types() {
        $this->db->select('id,type');
        $this->db->from('navaidtype');
        $this->db->order_by('id');
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

    # Get specific navaid
    function get($type,$country,$ident) {
        $this->db->select(' ident as navaid_ident,country as country_ident,country.name as country_name,
                            navaidtype.type,
				            st_asgeojson(navaid.location) as geometry,
				            (select value from variation
				                where st_dwithin(navaid.location,variation.location,0.5) limit 1
				            ) as variation,
				            (select timezones.tzid
				                from timezones where st_dwithin(navaid.location,the_geom,0.01) limit 1
				            ) as tzid,
				            elevation'
            , FALSE);
        $this->db->from('navaid,navaidtype,country');

        # If we support user waypoints. Make sure we only show their waypoints and nobody elses.
        #$this->db->where("CASE WHEN country = 'XX' THEN state = '$userid' ELSE coalesce(state,'') = COALESCE(state,'') END",NULL,FALSE);
        $this->db->where(array('navaid.type'=>'navaidtype.id','navaid.country'=>'country.id'), NULL,FALSE);


        $this->db->where(array("country" => $country, "ident" => $ident));
        $q = $this->db->get();

        if ($q->num_rows() == 1) {
            return $q->result();
        }
        else {
            $data = new stdClass();
            $data->code = 404;
            $data->error = "No Results Found";
            return $data;
        }
    }


}
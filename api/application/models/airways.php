<?php

class Airways extends CI_Model {

    # Get specific airway
    function get($country,$ident) {
        $q = $this->db->query("select waypoint as navaid,st_asgeojson(location) as geometry,airway.country as navaid_country,direction as order,navaidtype.type as navaid_type from airway,navaidtype,navaid where number = (select distinct number from airway where name = '$ident' and country = '$country') and airway.name = '$ident' and airway.waypointtype = navaidtype.id and airway.waypoint = navaid.ident", FALSE);

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

    # Get specific airway geometry
    function get_geometry($country,$ident) {
        $q = $this->db->query("select airway.name,st_asgeojson(st_makeline(array(select location from navaid,airway where airway.waypoint = navaid.ident and airway.country = navaid.country and number = (select distinct number from airway where airway.name = '$ident' and country = '$country') and airway.name = '$ident' order by direction))) as geometry from airway where number = (select distinct number from airway where name = '$ident' and country = '$country') and name = '$ident' group by airway.name;", FALSE);

        #$q = $this->db->get();

        if ($q->num_rows() == 1) {
            return $q->result()[0];
        }
        else {
            $data = new stdClass();
            $data->code = 404;
            $data->error = "No Results Found";
            return $data;
        }
    }

    # Search for navaids
    # If the ident is specified then filter results for that ident only
    function search($json,$ident) {

        if ($json) {
            $radius = $json['object']->properties->radius;
        }

        $this->db->select(' ident as navaid_ident,country as country_ident,country.name as country_name,
                            navaidtype.type,navaid.name as navaid_name,
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
        $this->db->where(array('navaid.type'=>'navaidtype.id','navaid.country'=>'country.id'), NULL,FALSE);

        # If we've been provided with a search point/linestring and radius.
        if ($json) {
            $this->db->where("st_coveredby(location::geography,st_buffer(st_setsrid(st_geomfromgeojson('$json[raw]'),4326)::geography,1852*$radius))", NULL,FALSE);
        }

        # If we're filtering for a specific navaid:
        if ($ident) {
            $this->db->where('ident',$ident);
        }

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
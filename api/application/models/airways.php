<?php

class Airways extends CI_Model {

    # Get specific airway
    function get($country,$ident) {
        $q = $this->db->query("select waypoint as navaid,st_asgeojson(location) as geometry,airway.country as navaid_country,direction as order,navaidtype.type as navaid_type from airway,navaidtype,navaid where number = (select distinct number from airway where name = '$ident' and country = '$country') and airway.name = '$ident' and airway.waypointtype = navaidtype.id and airway.waypoint = navaid.ident and airway.country = navaid.country", FALSE);

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
        $q = $this->db->query("select airway.name,st_asgeojson(st_makeline(array(select location from navaid,airway where airway.waypoint = navaid.ident and airway.country = navaid.country and airway.waypointtype = navaid.type and number = (select distinct number from airway where airway.name = '$ident' and country = '$country') and airway.name = '$ident' order by direction))) as geometry from airway where number = (select distinct number from airway where name = '$ident' and country = '$country') and name = '$ident' group by airway.name;", FALSE);

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

    # Search for airways
    # If the ident is specified then filter results for that ident only
    function search($json,$ident) {

        if ($json) {
            $radius = $json['object']->properties->radius;
        }
        #print "search";
        #die;

        $q = $this->db->query("select airway.name,country from airway left outer join ( select st_makeline(location order by direction) as geometry,airway.name,airway.number from navaid,airway where airway.waypoint = navaid.ident and airway.country = navaid.country and airway.waypointtype = navaid.type  group by airway.name,airway.number) as foo on (foo.name = airway.name and foo.number = airway.number )where st_crosses(geometry,st_setsrid(st_geomfromgeojson('$json[raw]'),4326)) and airway.direction = 1", FALSE);

        #$q = $this->db->get();

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
<?php

class Country extends CI_Model {


    # Get a list of all supported countries.
    function get() {
        $this->db->select('id,name');
        $this->db->from('country');
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
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Airway extends CI_Controller {

    # /api/airway/{country}/{ident}
    # See config/routes
    public function index($country,$ident) {

        $this->load->model('airways');

        $data['data'] = $this->airways->get_geometry($country,$ident);

        #if the first query fails, don't bother doing the rest (located in else)
        if (isset($data['data']->error)) {
            $this->load->view("error",$data);
        }
        else {
            $data['data']->waypoints = $this->airways->get($country,$ident);
            $this->load->view('json',$data);
        }

    }

    # /api/search/{ident}
    # ident is optional on POSTS
    public function search($ident = false) {

        $this->load->model('airways');
        $postdata = file_get_contents('php://input');


        if ($postdata){

            # Processing the post data
            # Validator returns a PHP array if it parsed the json correctly
            # Otherwise it returns a php Object with error details.
            $json = validateGeoJSON($postdata);
            if (is_array($json)) {
                $data['data'] = $this->airways->search($json,$ident);
                $this->load->view('json',$data);
            }
            else {
                $data['data'] = $json;
                $this->load->view("error",$data);
            }
        }
        else {
            $data['data'] = $this->airways->search(NULL,$ident);
            $this->load->view('json',$data);
        }

    }


}


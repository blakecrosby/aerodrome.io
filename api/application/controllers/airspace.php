<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Airspace extends CI_Controller {

    # /api/airspace/search

    public function search() {

        $this->load->model('airspace_model');
		$postdata = file_get_contents('php://input');


		if ($postdata){

			# Processing the post data
			# Validator returns a PHP array if it parsed the json correctly
			# Otherwise it returns a php Object with error details.
			$json = validateGeoJSON($postdata);
			if (is_array($json)) {
				$data['data'] = $this->airspace_model->search($json);
				$this->load->view('json',$data);
			}
			else {
				$data['data'] = $json;
				$this->load->view("error",$data);
			}
		}
		else {
			$data['data']->code = 405;
			$data['data']->error = "GET method is not allowed";

			$this->load->view('error',$data);
		}

    }

}


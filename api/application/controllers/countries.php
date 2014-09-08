<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Countries extends CI_Controller {

	public function index()	{

        $this->load->model('country');
        $data['data'] = $this->country->get();
        if (isset($data['data']->error)) {
            $this->load->view("error",$data);
        }
        else {
            $this->load->view('json',$data);
        }

	}
}


<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Navaid extends CI_Controller {

	public function types()	{

        $this->load->model('navaids');

        $data['data'] = $this->navaids->types();

        if (isset($data['data']->error)) {
            $this->load->view("error",$data);
        }
        else {
            $this->load->view('json',$data);
        }

	}
}


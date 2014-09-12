<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Navaid extends CI_Controller {

    # /api/navaid/types
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

    # /api/navaid/{type}/{country}/{ident}
    # See config/routes
    public function index($type,$country,$ident) {

        $this->load->model('navaids');

        $data['data'] = $this->navaids->get($type,$country,$ident);

        # Add the TZ Offset from tzid
        $dateTimeZoneLocal = new DateTimeZone($data['data'][0]->tzid);
        $dateTimeZoneUTC = new DateTimeZone('Etc/UTC');
        $dateTimeUTC = new DateTime("now",$dateTimeZoneUTC);
        $timeOffset = $dateTimeZoneLocal->getOffset($dateTimeUTC);

        $data['data'][0]->utc_offset = $timeOffset;

        if (isset($data['data']->error)) {
            $this->load->view("error",$data);
        }
        else {
            $this->load->view('json',$data);
        }

    }

    # /api/search/{ident}
    # ident is optional
    public function search($ident = false) {

        $this->load->model('navaids');


        if ($_POST){
            # Processing the post data
            # Validator returns a PHP array if it parsed the json correctly
            # Otherwise it returns a php Object with error details.
            $json = validateGeoJSON(file_get_contents('php://input'));
            print_r($json);
            if (is_array($json)) {
                $data['data'] = $this->navaids->search($json,$ident);
                $this->load->view('json',$data);
            }
            else {
                $this->load->view("error",$data);
            }
        }
        else {
            $data['data'] = $this->navaids->search(NULL,$ident);
            $this->load->view('json',$data);
        }

    }


}


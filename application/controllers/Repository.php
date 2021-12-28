<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repository extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('Repository_model');
	}

	public function index()
	{
		$this->load->view('gitRepository');
	}


	public function getRepository()
	{
		// print_r($_REQUEST); 
		// die;

		$search = $_REQUEST['search']['value'];
		// $search = "php";

        $columns = array(
            0 => '',
            1 => 'name',
            2 => 'stars',
            3 => ''
        );

        $start = $_REQUEST['start'];
        $length = $_REQUEST['length'];
        $page = ($start+$length)/$length;

      	//sort by
        $sort = $columns[$_REQUEST['order'][0]['column']];
        // print_r($sort); die;

        $order = $_REQUEST['order'][0]['dir'];
        // print_r($order); die;

		$curl = curl_init();
	    // Set some options - we are passing in a useragent too here
	    curl_setopt_array($curl, array(
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_URL => "https://api.github.com/search/repositories?q=$search&sort=$sort&order=$order&page=$page&per_page=$length",
	        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	    ));
	    // Send the request & save response to $resp
	    $resp = curl_exec($curl);
	    // Close request to clear up some resources
	    curl_close($curl);

	    $jsonObj = json_decode($resp);
    	$total_count = $jsonObj->total_count;

    	if($total_count >1000){
    		$total_count = 1000;
    	}

    	$output = array(
            "draw" => $_REQUEST['draw'],
            "recordsTotal" => $jsonObj->total_count,
            "recordsFiltered" => $total_count,
            "data" => $jsonObj->items
        );

	    echo json_encode($output);
	    exit;
		
	}

	public function favorite_repository()
	{
		$this->load->view('favorite_repository');
	}

	public function get_favorite_repository()
	{
		$getRepository = $this->Repository_model->getFavoriteRepository();
		echo json_encode($getRepository);
		exit;
	}

	public function addRepository()
	{
		$get_data = json_decode(file_get_contents('php://input'), true);
		//Repository Data
		//print_r($get_data); die;

		foreach ($get_data['data'] as $key){
			
			$data = array(  
		        'avatar_url' => $key['owner']['avatar_url'],
		        'name' => $key['name'],
		        'stargazers_count' => $key['stargazers_count'],
		        'login' => $key['owner']['login'],
	        );
	      
	       	//insert data into db
	        $result = $this->Repository_model->insertRepository($data);
		}

       if($result){
        	$Return = "Successfully Submitted!";
        	
       }else{
       		$Return = "Something went wrong";
       }

        echo json_encode($Return);
        exit;
	}
}

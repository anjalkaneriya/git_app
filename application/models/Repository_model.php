<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Repository_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function insertRepository($data)
    {
        $this->db->insert(' repository', $data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
    }

    public function getFavoriteRepository()
    {
        $this->db->select('*');
        $this->db->from('repository');
        $query = $this->db->get();
        return $query->result_array();
    }
    
}
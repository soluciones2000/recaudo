<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_model
{
	public function __construct()
	{
		$this->load->database();
    }

    public function add($data)
    {
    	$this->db->insert("table", $data);
    }

    public function update($data,$where)
    {
    	$this->db->update("table", $data, $where);
    }

    public function delete($id)
    {
    	$this->db->delete("table", $id);
    }
}
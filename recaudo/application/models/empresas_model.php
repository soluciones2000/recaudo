<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class empresas_model extends CI_Model {

	var $table = 'empresas';
	var $column_order = array('nombre_empresa','razon_social', null); //set column field database for datatable orderable
	var $column_search = array('nombre_empresa','razon_social'); //set column field database for datatable searchable 
	var $order = array('nombre_empresa' => 'asc'); // default order 
	var $id = "id";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		//add custom filter here
		if($this->input->post('nombre_empresa_ajaxfiltro'))
		{
			$this->db->like('nombre_empresa', $this->input->post('nombre_empresa_ajaxfiltro', TRUE));
		}
		if($this->input->post('razon_social_ajaxfiltro'))
		{
			$this->db->like('razon_social', $this->input->post('razon_social_ajaxfiltro', TRUE));
		}
		
		$this->db->from($this->table);
		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$this->db->order_by("id", "asc");
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_list_filtro($filtro)
	{
		$this->db->from($this->table);
		$this->db->order_by($filtro,'asc');
		$query = $this->db->get();
		$result = $query->result();

		$array = array();
		foreach ($result as $row) 
		{
			$array[$row->id] = $row->$filtro;
		}
		return $array;
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where($this->id,$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		if ($data["predeterminado"] != NULL)
		{
			$this->db->from($this->table);
			$this->db->where(array("predeterminado" => $data["predeterminado"]));
			$query = $this->db->get();
			if($query->num_rows() > 0)
			{
				$row = $query->row();
				$this->db->update($this->table, array("predeterminado" => 0), array("id" => $row->id));
			}
			$this->db->insert($this->table, $data);
			return array("status" => TRUE, "insert_id" => $this->db->insert_id());
		}
		else
		{
			$data["predeterminado"] = 1;
			$this->db->insert($this->table, $data);
			return array("status" => TRUE, "insert_id" => $this->db->insert_id());
		}
	}

	public function update($where, $data)
	{
		if ($data["predeterminado"] != NULL)
		{
			$this->db->from($this->table);
			$this->db->where(array("predeterminado" => $data["predeterminado"]));
			$query = $this->db->get();
			if($query->num_rows() > 0)// si existe predeterminado en la bd
			{
				$row = $query->row();
				if ($row->id == $where["id"])// si se mantiene el predeterminado, lo que ambos ID deben coincidir y guarda la info
				{
					$this->db->update($this->table, $data, $where);
					return array("status" => TRUE);
				}
				else
				{// no coincide el ID que tiene el predeterminado de la BD con el que se esta modificando se modifica el de la BD y luego se guarda la info
					$this->db->update($this->table, array("predeterminado" => 0), array("id" => $row->id));
					$this->db->update($this->table, $data, $where);
					return array("status" => TRUE);
				}
			}
			$this->db->update($this->table, $data, $where); //no es predeterminado pasa a ser predeterminado mientras que no exista uno con predeterminado en la bd
			return array("status" => TRUE);
		}		
		$this->db->update($this->table, $data, $where); //no es predeterminado y no lo marcan predeterminado
		return array("status" => TRUE);
	}
	
	public function delete_by_id($id)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($this->table);
	}
}
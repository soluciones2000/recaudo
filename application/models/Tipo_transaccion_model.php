<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tipo_transaccion_model extends CI_Model {

	var $table = 'tipotransaccion';
	var $column_order = array('NombreTipoTransaccion','signoTipoTransaccion',"FechaRegistro", null); //set column field database for datatable orderable
	var $column_search = array('NombreTipoTransaccion','signoTipoTransaccion',"FechaRegistro"); //set column field database for datatable searchable 
	var $order = array('NombreTipoTransaccion' => 'asc'); // default order 
	var $id = "id";

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		//add custom filter here
		if($this->input->post('NombreTipoTransaccion_ajaxfiltro'))
		{
			$this->db->like('NombreTipoTransaccion', $this->input->post('NombreTipoTransaccion_ajaxfiltro', TRUE));
		}
		if($this->input->post('signoTipoTransaccion_ajaxfiltro'))
		{
			$this->db->like('signoTipoTransaccion', $this->input->post('signoTipoTransaccion_ajaxfiltro', TRUE));
		}
		if($this->input->post('FechaRegistro_ajaxfiltro'))
		{
			$this->db->like('FechaRegistro', $this->input->post('FechaRegistro_ajaxfiltro', TRUE));
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
		$this->db->order_by($this->id, "asc");
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
		$this->db->from($this->table);
		$this->db->where(array("NombreTipoTransaccion" => $data["NombreTipoTransaccion"]));
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			return array("status" => "error");
		}
		else
		{
			$this->db->insert($this->table, $data);
			return array("status" => TRUE, "insert_id" => $this->db->insert_id());
		}
	}

	public function update($where, $data)
	{
		$query = $this->db->from($this->table) // esta sentencia verifique q no existan repetidos y permite guardar la misma info si no se modifica
				->group_start()
					->where("id !=", $where["id"]) // indicador del id: permite comparar todos los q tengan los unicos sin que esten con el id q se desea modificar
					->group_start()
						->where("NombreTipoTransaccion",$data["NombreTipoTransaccion"]) // campo unico
					->group_end()
				->group_end()
			->get();

		if($query->num_rows() > 0)// si existe el id fiscal de un Gestor en la bd y se quiere modificar a ese mismo id existente
		{
			return array("status" => "error");
		}
		else
		{
			$this->db->update($this->table, $data, $where);
			return array("status" => TRUE);
		}
	}
	
	public function delete_by_id($id)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($this->table);
	}
}
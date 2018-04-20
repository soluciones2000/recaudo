<?php
defined("BASEPATH") OR die("El acceso al script no está permitido");

class Pdf_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

    public function get_reporte_usuarios()
    {
		//add custom filter here
		if($this->input->post('nombres_ajaxreporte'))
		{
			$this->db->like('nombres', $this->input->post('nombres_ajaxreporte', TRUE));
		}
		if($this->input->post('apellidos_ajaxreporte'))
		{
			$this->db->like('apellidos', $this->input->post('apellidos_ajaxreporte', TRUE));
		}
		if($this->input->post('correo_ajaxreporte'))
		{
			$this->db->like('correo', $this->input->post('correo_ajaxreporte', TRUE));
		}
		if($this->input->post('modulo_ajaxreporte'))
		{
			$this->db->like('modulos', $this->input->post('modulo_ajaxreporte', TRUE));
		}

		$this->db->from("usuarios");

		$query = $this->db->get();
		return $query->result();
    }

    public function get_reporte_polizas()
    {
		//add custom filter here
		if($this->input->post('ci_rif_ajaxreporte'))
		{
			$this->db->like('ci_rif', $this->input->post('ci_rif_ajaxreporte', TRUE));
		}
		if($this->input->post('nombres_ajaxreporte'))
		{
			$this->db->like('nombres', $this->input->post('nombres_ajaxreporte', TRUE));
		}
		if($this->input->post('apellidos_ajaxreporte'))
		{
			$this->db->like('apellidos', $this->input->post('apellidos_ajaxreporte', TRUE));
		}
		if($this->input->post('fecha_corte_ajaxreporte'))
		{
			$this->db->like('fecha_corte', $this->input->post('fecha_corte_ajaxreporte', TRUE));
		}
		if($this->input->post('tipo_poliza_ajaxreporte'))
		{
			$this->db->like('tipo_poliza', $this->input->post('tipo_poliza_ajaxreporte', TRUE));
		}
		
		
		$this->db->from("poliza_comprada");

		$query = $this->db->get();
		return $query->result();
    }

    public function get_reporte_empresas()
    {
		//add custom filter here
		if($this->input->post('razon_social_ajaxreporte'))
		{
			$this->db->like('razon_social', $this->input->post('razon_social_ajaxreporte', TRUE));
		}
		if($this->input->post('nombre_empresa_ajaxreporte'))
		{
			$this->db->like('nombre_empresa', $this->input->post('nombre_empresa_ajaxreporte', TRUE));
		}

		$this->db->from("empresas");
		$query = $this->db->get();
		return $query->result();
    }

    public function get_reporte_Cliente()
    {
		//add custom filter here
		if($this->input->post('NombreCliente_ajaxreporte'))
		{
			$this->db->like('NombreCliente', $this->input->post('NombreCliente_ajaxreporte', TRUE));
		}
		if($this->input->post('IdFiscal_ajaxreporte'))
		{
			$this->db->like('IdFiscal', $this->input->post('IdFiscal_ajaxreporte', TRUE));
		}
		if($this->input->post('FechaRegistro_ajaxreporte'))
		{
			$this->db->like('FechaRegistro', $this->input->post('FechaRegistro_ajaxreporte', TRUE));
		}

		$this->db->from("Cliente");
		$query = $this->db->get();
		return $query->result();
    }

    public function get_reporte_tipo_transaccion()
    {
		//add custom filter here
		if($this->input->post('NombreTipoTransaccion_ajaxreporte'))
		{
			$this->db->like('NombreTipoTransaccion', $this->input->post('NombreTipoTransaccion_ajaxreporte', TRUE));
		}
		if($this->input->post('signoTipoTransaccion_ajaxreporte'))
		{
			$this->db->like('signoTipoTransaccion', $this->input->post('signoTipoTransaccion_ajaxreporte', TRUE));
		}
		if($this->input->post('FechaRegistro_ajaxreporte'))
		{
			$this->db->like('FechaRegistro', $this->input->post('FechaRegistro_ajaxreporte', TRUE));
		}

		$this->db->from("tipotransaccion");
		$query = $this->db->get();
		return $query->result();
    }

    public function get_reporte_proveedor()
    {
		//add custom filter here
		if($this->input->post('NombreProveedor_ajaxreporte'))
		{
			$this->db->like('NombreProveedor', $this->input->post('NombreProveedor_ajaxreporte', TRUE));
		}
		if($this->input->post('IdFiscal_ajaxreporte'))
		{
			$this->db->like('IdFiscal', $this->input->post('IdFiscal_ajaxreporte', TRUE));
		}
		if($this->input->post('FechaRegistro_ajaxreporte'))
		{
			$this->db->like('FechaRegistro', $this->input->post('FechaRegistro_ajaxreporte', TRUE));
		}

		$this->db->from("proveedor");
		$query = $this->db->get();
		return $query->result();
    }

    public function get_reporte_Gestor()
    {
		//add custom filter here
		if($this->input->post('NombreGestor_ajaxreporte'))
		{
			$this->db->like('NombreGestor', $this->input->post('NombreGestor_ajaxreporte', TRUE));
		}
		if($this->input->post('IdFiscal_ajaxreporte'))
		{
			$this->db->like('IdFiscal', $this->input->post('IdFiscal_ajaxreporte', TRUE));
		}
		if($this->input->post('FechaRegistro_ajaxreporte'))
		{
			$this->db->like('FechaRegistro', $this->input->post('FechaRegistro_ajaxreporte', TRUE));
		}

		$this->db->from("gestor");
		$query = $this->db->get();
		return $query->result();
    }
    
    public function get_reporte_Categoria_producto()
    {
		//add custom filter here
		if($this->input->post('NombreCategoria_ajaxreporte'))
		{
			$this->db->like('NombreCategoria', $this->input->post('NombreCategoria_ajaxreporte', TRUE));
		}
		
		$this->db->from("categoriaproducto");
		$query = $this->db->get();
		return $query->result();
    }
    
    public function get_datoespecifico($tabla, $campo, $where)
    {
    	$this->db->from($tabla);
    	$this->db->where($campo, $where);
    	$this->db->limit(1);
    	$query = $this->db->get();
    	return $query->row();
    }

    public function get_empresa($id)
    {
    	$this->db->from("empresas");
		$this->db->where("id",$id);
		$query = $this->db->get();

		return $query->row();
    }

    public function list_empresas()
    {
    	$this->db->from("empresas");
		$this->db->order_by("id",'asc');
		$query = $this->db->get();
		$result = $query->result();

		$array = array();
		foreach ($result as $row) 
		{
			$array["list"][$row->id] = $row->nombre_empresa;
			if ($row->predeterminado == 1)
			{
				$array["predeterminado"] = $row->id;
			}
		}
		return $array;
    }
}
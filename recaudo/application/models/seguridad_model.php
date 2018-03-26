<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Seguridad_model extends CI_Model
{
	private $tablausers = "usuarios"; //parte de la funcion val_login y _update_last_login
	private $seleccion = "id,nombres,correo,clave,activo,modulos"; //parte de la funcion log_in
	// usa el helper date y la libreria sessionlas cuales estan activdas en config/autoload.php
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function val_login($email, $password)//inicio de sesion del usuario
	{
		if (!empty($email) && !empty($password)) //se verifica si la informacion esta enviada
		{
			if ($this->session->userdata("id") == NULL)
			{
				$query = $this->db->select($this->seleccion)
								  ->from($this->tablausers)
				                  ->where(array("correo" => $email))
				                  ->get();
				
				if ($query->num_rows() == 1)//verifica si existe un registro con los datos ingresados
				{
					$user = $query->row();
					
					if (password_verify($password, $user->clave))
					{
						if ($user->activo == 0)
						{
							return "inactivo"; //cuenta de usuario inactiva
						}

						$this->_set_session($user);//genera una sesion con los datos del usuario
						$this->_update_last_login($user->id);//actualiza el campo de ultimo ingreso a la aplicacion
						$this->acciones("inicio de sesion");// registra el inicio de sesion
						return TRUE;
					}
					else
					{
						return "error";//error en la clave en caso de no ser igual
					}		
				}
				else// si no existe emite un error de que el usuario no existe
				{
					return FALSE;
				}	
			}
			else// en caso de que el usuario ya inicio sesion
			{
				return NULL;
			}
		}
		else
		{
			return "vacio"; //campos vacios
		}
	}

	public function acciones($accion)// registra las acciones que realicen los usuarios en la aplicacion
	{
		$this->db->insert("log", array("id_users_registros" => $this->session->userdata("correo"),"accion_registros" => $accion, "fecha_registros" => date("d-m-Y H:i:s"))); 

		if($this->db->affected_rows() == 1)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	private function _set_session($user)//prepara la informacion que se usara en la sesion del usuario
	{
		$session_data = array(
			"id"		=> $user->id,
			"nombres"	=> $user->nombres,
			"correo"	=> $user->correo,
			"modulos"	=> $user->modulos,
		);

		$this->session->set_userdata($session_data);// uso de la libreria de codeigniter para sesiones

		return TRUE;
	}

	private function _update_last_login($id)//actualiza el ultimo inicio de sesion del usuario
	{
		$this->db->update($this->tablausers, array('ultimo_inicio' => date("d-m-Y H:i:s")), array('id' => $id));

		return $this->db->affected_rows() == 1;
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seguridad
{
	private $CI;
	public function __construct()
	{
		$this->CI = get_instance();
		$this->CI->load->model('seguridad_model');
		$this->CI->load->library('session');
	}

	/**
	 * login
	 * @param $email = correo electronico del usuario
	 * @param $password = la clave que se registro del usuario
	 *
	 * @return bool
	 * @author david fernandez
	 **/
	public function login($email, $password)
	{
		//el registro de inicio de sesion se realiza en el model
		return $this->CI->seguridad_model->val_login($email, $password);
	}

	/**
	 * logout
	 *
	 * @return void
	 * @author david fernandez
	 **/
	public function logout()//cierra la sesion del usuario
	{
		if ($this->CI->session->userdata("id") != NULL)
		{
			$this->registrar_accion("cierre de sesion");
			$this->CI->session->unset_userdata(array("id", "nombres", "correo", "modulos"));
			$this->CI->session->sess_destroy();

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	/**
	 * registrar_accion
	 *
	 * @param $accion = string que contiene la accion que realice el usuario antes de generar cambios en la bd
	 *
	 * @author david fernandez
	 **/
	public function registrar_accion($accion)
	{
		$this->CI->seguridad_model->acciones($accion);
	}
	/**
	 * get_user_nivel
	 *
	 * @param $controller = string generado por la funcion get_class de php para verificar si el usuario tiene acceso a ese controlador
	 *
	 * @return int
	 * @return NULL
	 * @author david fernandez
	 **/
	public function get_user_nivel($controller)
	{
		$string_groups = $this->CI->session->userdata("modulos");
		if ($string_groups != NULL)
		{
			$array_groups = explode(",", $string_groups);
			
			foreach ($array_groups as $key => $val)
			{
				$array1 = explode("|", $val);

				if ($array1[0] == $controller)
				{
					return intval($array1[1]);
				}
			}	
			return NULL;
		}
		return NULL;
	}
	/**
	 * get_user_groups
	 *
	 *  retorna un array o un valor null si no esta abierta la sesion del usuario
	 *
	 * @return bool
	 * @author david fernandez
	 **/
	public function get_user_groups()
	{
		$array2 = array();
		$string_groups = $this->CI->session->userdata("modulos");
		if ($string_groups != NULL)
		{
			$array_groups = explode(",", $string_groups);
			
			foreach ($array_groups as $key => $val)
			{
				$array1 = explode("|", $val);

				$array2[] = $array1[0];
			}
			return $array2;
		}
		return NULL;
	}
}

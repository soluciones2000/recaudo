<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{	
		$data['titulo'] = "Login";//titulo de la pagina
		
		if ($this->session->userdata("id") != NULL)
		{
			$data["login"] = $this->session->userdata("nombres");//muestra el nombre del usuario
			$data["nav"] = $this->seguridad->get_user_groups();//muestra el menu de los usuarios cuando estan conectados
		}
		
		$this->_render_page("ingresar_view", $data);
	}

	public function ingresar()
	{
		$email = $this->input->post("email", TRUE);//correo del usuario
		$password = $this->input->post("password", TRUE);//contraseña del usuario
		$log = $this->seguridad->login($email, $password);
		
		if ($log === TRUE) 
		{
			$data = "<div class='alert alert-success' style='text-align: center;'>
						<strong>Exito!</strong> Inicio de sesion exitoso
					</div>";//envio de informacion a la vista con clase de bootstrap
			$this->session->set_flashdata("mensaje", $data);
			redirect("Login");
		}
		elseif ($log === "inactivo")
		{
			$data = "<div class='alert alert-danger' style='text-align: center;'>
						<strong>Peligro!</strong> Cuenta de usuario inactiva, comuniquese con el administrador
					</div>";//envio de informacion a la vista con clase de bootstrap
			$this->session->set_flashdata("mensaje", $data);
			redirect("Login");
		}
		elseif ($log === "vacio")
		{
			$data = "<div class='alert alert-info' style='text-align: center;'>
						<strong>Info!</strong> No puede dejar los campos vacios
					</div>";//envio de informacion a la vista con clase de bootstrap
			$this->session->set_flashdata("mensaje", $data);
			redirect("Login");
		}
		elseif ($log === FALSE)
		{
			$data = "<div class='alert alert-warning' style='text-align: center;'>
						<strong>Alerta!</strong> El usuario no existe
					</div>";//envio de informacion a la vista con clase de bootstrap
			$this->session->set_flashdata("mensaje", $data);
		  	redirect("Login");
		}
		elseif ($log === "error")
		{
			$data = "<div class='alert alert-warning' style='text-align: center;'>
						<strong>Alerta!</strong> Clave incorrecta
					</div>";//envio de informacion a la vista con clase de bootstrap
			$this->session->set_flashdata("mensaje", $data);
		  	redirect("Login");
		}
		elseif ($log === NULL)
		{
			$data = "<div class='alert alert-warning' style='text-align: center;'>
						<strong>Alerta!</strong> El usuario ya inicio sesion
					</div>";//envio de informacion a la vista con clase de bootstrap
			$this->session->set_flashdata("mensaje", $data);
			redirect("Login");
		}
	}	

	public function salir()
	{
		$data = $this->seguridad->logout();
		if ($data == TRUE)
		{
			$data = "<div class='alert alert-success' style='text-align: center;'>
						<strong>Exito!</strong> Cierre de sesion exitoso
					</div>";//envio de informacion a la vista con clase de bootstrap
			$this->session->set_flashdata("mensaje", $data);
			redirect("Login");
		}
		elseif ($data == FALSE)
		{
			redirect("Login");
		}
	}

	private function _render_page($view, $data=null, $returnhtml=TRUE)
	{

		$this->viewdata = (empty($data)) ? $this->data: $data;
		$view_html = $this->load->view('plantilla/head', $this->viewdata, $returnhtml);
		$view_html .= $this->load->view('plantilla/nav', $this->viewdata, $returnhtml);
		$view_html .= $this->load->view($view, $this->viewdata, $returnhtml);
		$view_html .= $this->load->view('plantilla/footer', $this->viewdata, $returnhtml);
		if ($returnhtml) return $this->output->set_output($view_html);//esto retornara HTML en el 3 argumento ya que se envia como TRUE
	}
}
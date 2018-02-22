<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://localhost/ciUserModule/login
	 *	- or -
	 * 		http://localhost/ciUserModule/login/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://ciUserModule/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /login/<method_name>
	 */
	/*public function __construct(){
		
	}*/ 
	 
	public function index()
	{
		if($this->session->userdata('id')){
			redirect('admin/dashboard');
		}
		$this->load->view('admin/auth/login');
	}
	
	public function dologin()
	{
		if($this->input->is_ajax_request()){
			$this->form_validation->set_rules('email','Email','required|trim|valid_email');
			$this->form_validation->set_rules('password','Password','required|trim');
			if($this->form_validation->run() === true){
				$user_data = array();
				$userdata = array();
				$data = $this->input->post();
				$u_data["email"] = $data["email"];
				$u_data["password"] = md5($data["password"]);
				$user_data = $this->Common_model->getsingle(USERS, array('email'=>$u_data["email"]));
				if(!empty($user_data)):
					$userdata = $this->Common_model->getsingle(USERS, $u_data);
					if(!empty($userdata)){
						$role = 'admin';
					}else{
						$msg = 'Password not match';
					}
				else:
					$msg = 'Email not found';
				endif; 
				
				if(!empty($userdata)){
					$user_id = (int) $userdata->id; 
					$last_login = date('Y-m-d H:i:s');
					$this->session->set_userdata("id", $user_id);
					$this->session->set_userdata("email", $userdata->email);
					$this->session->set_userdata('user_activity', time());
					$this->session->set_userdata("role", "Admin");
					
					if (isset($data['remember_me'])) {
						$cookie = array(
							'name' => 'login',
							'value' => encoding($user_data->email . "_" . $user_id . "_" . $role),
							'expire' => '864000000', // 10 days
						);
						$this->input->set_cookie($cookie);
					}
					
					$response = array('status' => 1, 'message' => 'Login successful', 'url' => base_url('admin/dashboard'));	
				}else{
					$response = array('status' => 0, 'message' => $msg);
				}
			}else{
				$requireds = strip_tags($this->form_validation->error_string());
				$messages = explode("\n", trim($requireds, "\n"));
				$response = array('status' => 0, 'message' => $messages);
			}	
		}else{
			$response = array('status' => 0, 'message' => 'No direct script access allowed');
		}
		echo json_encode($response);
	}
}

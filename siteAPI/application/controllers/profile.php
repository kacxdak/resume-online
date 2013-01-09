<?php

class Profile extends CI_Controller {
	
	function index()
	{
		$this->load->model('membership_model');
		$data['info'] = $this->Membership_model->get_info();
		$data['address'] = $this->Membership_model->get_address();
		$data['website'] = $this->Membership_model->get_website();		
		$data['phone'] = $this->Membership_model->get_phone();
		
		$data['context'] = 'profile_form';
		$this->load->view('template/main', $data);	
	}
	
	function update()
	{
		
		if(check_pass($this->input->post('old_pass'))) //check if old password was right
		{
		
			$this->load->library('form_validation');
			$this->load->model('membership_model');
			$data['info'] = $this->Membership_model->get_info();
		
			if($this->input->post('name') != $data['info']->name) 
			{
				$this->form_validation->set_rules('name', 'Name', 'required|alpha');
			}
			
			if($this->input->post('email') != $data['info']->email) 
			{
				$this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email|is_unique[users.email]');
			}
			
			if(length($this->input->post('new_pass'))>0||length($this->input->post('new_pass_confirm'))>0)
			{
				$this->form_validation->set_rules('new_pass', 'Password', 'trim|required|min_length[4]|max_length[32]');
				$this->form_validation->set_rules('new_pass_confirm', 'Password Confirmation', 'trim|required|matches[new_pass]');
			}
			else
			{
				//if didn't enter new password, assign new_pass to what old_pass is (since it passed the check)
				//this is so that the update_user function doesn't change the new pass to ''
				$this->input->post('new_pass')=$this->input->post('old_pass');
			}
			
			//if validation fails, else update user with information from post
			if($this->form_validation->run() == FALSE )
			{
				$data['context'] = 'profile_form';
				$this->load->view('template/main', $data);
			}
			else
			{
				$this->Membership_model->update_user($this->input->post('name'),$this->input->post('email'),$this->input->post('new_pass'));
				$data['success'] = "Your data was updated!";
				$this->index();		
			}
		}
		else
		{
			//enter code to tell user that old password was wrong
			$data['success'] = "Your old password was incorrect.";
		}
	
	}
	

}
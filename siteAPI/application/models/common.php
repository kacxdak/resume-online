<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common extends CI_Model {
	
	$CI =& get_instance();
	
	function __construct()
    {
        parent::__construct();
    }

	function chash($str) 
	{
		return hash('sha512', $str);
	}
	
	function uid($len = 10) 
	{
		return $this->Basic->secure_random_bytes($len);
	}
	
	function is_logged_in()
	{
		$is_logged_in = $this->session->userdata('is_logged_in');
		if(!isset($is_logged_in) || $is_logged_in != true)
		{
			$data['context'] = "not_logged_in";
			$this->load->view('not_logged_in', $data);
			$this->CI =& get_instance();
	        $this->CI->output->_display();
	        exit();
		}
	}
	
	function type_table($id)
	{
		if(!isset($id))
			return "cat";
			
		switch ($id)
		{
			case 1:
				return "uni";
			case 2:
				return "experience";
			case 3:
				return "skill_header";
			case 4:
				return "honors";
			case 5:
				return "additional";
		}
	}
	
	//returns -1 if nothing found
	function user_id($email = NULL)
	{
		if(isset($email))
		{
			$this->db->select('id');
			$this->db->where('email', $email);
            $query = $this->db->get('users');
            if($query->num_rows == 1) 
            {
				foreach ($query->result() as $user) {
					return $user->id;
				}
            }
		}
		else if($this->session->userdata('user_id') > -1) 
		{
			return $this->session->userdata('user_id');
		}
		return -1;
	}
	
	function check_pass($pass, $user_id = NULL) 
	{
		if(!isset($user_id)) $user_id = $this->Common->user_id();
		
		$this->db->select('password');
		$this->db->where('id', $user_id);
		$query = $this->db->get('users');
		foreach($query->result() as $user) 
		{
			$salt = substr($user->password, 0, 40);
        	$conf = substr($salt . $this->Common->chash($salt . $pass), 0, 512);
        	if($conf == $user->password)
        		return TRUE;
        }
        return false;
	}
	
}


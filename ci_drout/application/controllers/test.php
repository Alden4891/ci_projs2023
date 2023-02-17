<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class test extends CI_Controller {
	

	public function index(){
		
	}

	public function send_email(){
		$to = "alden.roxy@gmail.com";
		$subject = "this is a test upload without attachment";
		$body = "this has attachments";
		$attachments = array('C:/xampp/htdocs/ci_drout/uploads/file1.jpg', 'C:/xampp/htdocs/ci_drout/uploads/file2.jpg');
		$this->load->model('gmail_model');
		$msgid = $this->gmail_model->send_email($to, $subject, $body);
		print($msgid);
	}

	//http://localhost/ci_drout/test/thread/1860574830b4fd26
	public function thread($thread){
		$this->load->model('gmail_model');
		$data = $this->gmail_model->get_email_by_threadID($thread);
		print('<pre>');
		print_r($data);
		print('</pre>');
	}
}

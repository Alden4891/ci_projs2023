<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class test extends CI_Controller {
	
	public function index(){
		$this->load->view('viewer');
	}
	#error
	// public function index(){
	// 	$this->load->model('gmail_model');
	// 	$data = $this->gmail_model->get_email_by_id("1865ea0b9b1e5b2d");
	// 	print('<pre>');
	// 	print_r($data);
	// 	print('</pre>');
	// }

	// public function index(){
	// 	$this->load->model('gmail_model');
	// 	$data = $this->gmail_model->get_mails("is:sent");
	// 	print('<pre>');
	// 	print_r($data);
	// 	print('</pre>');
	// }

	// public function index(){
	// 	#sent 1865e8a41b449cfa, 1865ea0b9b1e5b2d (formatted)
	// 	$to = "alden.roxy@gmail.com";
	// 	$subject = "this is a test upload with attachment ******";
	// 	$body = "this <b>has</b> attachments";
	// 	$attachments = array('C:\Users\aaquinones\Desktop\newisogenform\DSWD-GF-004A_REV 01_Memo for the Sec.doc.docx', 'C:\Users\aaquinones\Desktop\newisogenform\DSWD-GF-009_REV 02_Summary of Agreements.docx');
	// 	$this->load->model('gmail_model');
	// 	$msgid = $this->gmail_model->send_email($to, $subject, $body,$attachments);
	// 	print($msgid);
	// }

	// #http://localhost/ci_drout/test/thread/1860574830b4fd26
	// public function thread($thread){
	// 	$this->load->model('gmail_model');
	// 	$data = $this->gmail_model->get_email_by_threadID($thread);
	// 	print('<pre>');
	// 	print_r($data);
	// 	print('</pre>');
	// }
}

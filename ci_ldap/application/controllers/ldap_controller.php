<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ldap_controller extends CI_Controller {
	public function index()
	{
		// $this->load->model("Ldap_user_mngt_model");
		// $RESULT = $this->Ldap_user_mngt_model->get_user_groups("etraining");
		// print('<pre>');
		// print_R($RESULT);
		// print('</pre>');


		$this->load->model('test');

		$ou1="OU=PPIS ML,OU=Clients,OU=FO12";
		$ou2="OU=Pantawid Pamilya,OU=Poverty Reduction Programs,OU=Operations and Programs Division,OU=Clients,OU=FO12";
		$ou3="OU=Clients,OU=FO12";

		$g1="CN=12_SSLVPN_GRP_ACL,OU=Groups,OU=FO12,DC=ENTDSWD,DC=LOCAL";
		$g2="CN=12-SSL-groups,OU=Groups,OU=FO12,DC=ENTDSWD,DC=LOCAL";
		$g3="CN=12-PPPP-GRP,OU=Clients,OU=FO12,DC=ENTDSWD,DC=LOCAL";

		//create user
		$RESULT = $this->test->create_user($ou2);
		print($RESULT);	

		//assign groups

		//change password

		//disable account

		//enable account

		//clone account

		//delete accont



	}



}

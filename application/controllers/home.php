<?php


class Home extends CI_Controller
{


	function Home()
	{
		parent::__construct();
		$this->output->set_header('Content-type: text/html; charset=UTF-8');
	}
	
	function index()
	{
		if($this->session->userdata('playlist_ID') !== FALSE)
			$this->session->sess_destroy();

		$this->load->view("header");
		
		$this->load->view("intro");

		$this->load->view("footer");
	
	}
	
}

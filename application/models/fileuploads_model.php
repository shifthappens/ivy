<?php

class FileUploads_model extends CI_Model
{

	function FileUploads_model()
	{
		parent::__construct();
		$this->load->helper('file');
	}
	
	function get_upload_file($path)
	{
	
	}
	
	function delete_file($file, $print_to_screen = FALSE)
	{
		if(file_exists($file))
		{
			if(unlink($file))
			{
				if($print_to_screen)
					$this->load->view('cleanup', array('message' => "File ".$file." deleted <br />"));

				return TRUE;
			}
			else
			{
				if($print_to_screen)
					$this->load->view('cleanup', array('message' => "File ".$file." NOT deleted <br />"));

				return FALSE;
			}
		}
		else
		{
			if($print_to_screen)
					$this->load->view('cleanup', array('message' => "File ".$file." does not exist!<br />"));
			
			return "File does not exist";	
		}
	}

	function cleanup_files($time, $print_to_screen = FALSE)
	{
		foreach(get_dir_file_info('./uploads') as $playlist_file)
		{
			if($playlist_file['date'] <= $time)
			{
				if($print_to_screen)
					$this->load->view('cleanup', array('message' => "File ".$playlist_file['name']." is up for deletion because its date is set to ".date('d-m-Y H:m:i', $playlist_file['date'])."<br />"));

				$this->delete_file('./uploads/'.$playlist_file['name'], $print_to_screen);
			}
		}
	}
}

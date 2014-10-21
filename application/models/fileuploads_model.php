<?php

class FileUploads_model extends Model
{

	function FileUploads_model()
	{
		parent::Model();
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
					echo "File ".$file." deleted <br />";

				return TRUE;
			}
			else
			{
				if($print_to_screen)
					echo "File ".$file." NOT deleted <br />";

				return FALSE;
			}
		}
		else
		{
			if($print_to_screen)
				echo "File ".$file." does not exist! <br />";
			
			return "File does not exist";	
		}
	}
	
	function test()
	{
		echo "success.";
	}	
}

<?php

if(!class_exists("importer"))
	require_once("importer.php");

if(!class_exists("parseCSV"))
	require_once("parsecsv.lib.php");


class CSVImporter extends Importer
{

	public $source = '';
	public $default_columns = array();	
	
	
	public function __construct($params)
	{
		$this->lib = new parseCSV();

		if(isset($params['file']))
		{			
			if(!file_exists($params['file']))
				$this->throw_error(1, 'CSV file does not exist.<br />');
							
			if(!$this->lib->auto($params['file']))
				$this->throw_error(1, 'Something went wrong while parsing your CSV file. Check if the formatting is following the specifications!');
			
			$this->source = $params['file'];
		}
		
		$this->set_default_columns();
	}
		
	public function get_default_columns()
	{
		return $this->default_columns;
	}
	
	public function load_file($file)
	{
			if(!file_exists($file))
				$this->throw_error(1, 'CSV file does not exist.<br />');
				
			if(!$this->lib->auto($file))
				$this->throw_error(1, 'Something went wrong while parsing your CSV file. Check if the formatting is following the specifications!');	
				
			return TRUE;
	}	
		
	public function get_num_rows()
	{
		return count($this->lib->data);
	}
	
	public function get_all_rows()
	{
		log_message('debug', "csv get all rows output = ".print_r($this->lib->data, true));	
		
		return $this->lib->data;
	}
	
	public function scan_headers()
	{	
		$original_headers = $this->get_headers();
		$matches = array();
		
		foreach($this->get_default_columns() as $column => $possibilities)
		{
			//We are looking for the columns that indicate information about a song.
			//So artist, title, album, etc.
			//We will try to guess it by looking at names often used
						
			foreach($possibilities as $possibility)
			{
				$result = array_keys($original_headers, $possibility);
				
				if(count($result) !== 0)
					$matches[$column.'_column'] = $original_headers[$result[0]];
			}
		}
		
		//save the matches for later use
		$this->set_matched_columns($matches);
		
		//Only Artist and Track are required to have, so just check if we have a unique match there
		//If we do, then YAY, otherwise NAY
		
		log_message('debug', "scan headers matches = ".print_r($matches, true));
		
		if(count($matches['artist_column']) === 1 && count($matches['track_column']) === 1)
			return TRUE;
		else
			return FALSE;
	}
	
	public function get_headers()
	{
		log_message('debug', "csv get headers output = ".print_r($this->lib->titles, true));	
		
		return $this->lib->titles;
	}
	
	public function get_matched_columns()
	{
		return $this->column_matches;
	}
	
	public function set_matched_columns($new)
	{
		$this->column_matches = $new;
	}
	
	public function get_sample($amount = 5)
	{
		return array_slice($this->lib->data, 0, $amount);
	}
	
	
	private function set_default_columns()
	{
		$this->default_columns = array(
			'artist' => array(
				'artist', 'band', 'group', 'performer', 'artists'
			),
			'track' => array(
				'track', 'number', 'trackname', 'title'
			),
			'album' => array(
				'album'
			)
		);
	}
	
	
}



/* End of file CSVImporter.php */
/* Location: ./system/application/libraries/CSVImporter.php */
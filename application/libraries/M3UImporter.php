<?php

if(!class_exists("importer"))
	require_once("importer.php");

if(!class_exists("parseCSV"))
	require_once("parsem3upls.lib.php");


class M3UImporter extends Importer
{

	public $source = '';
	public $default_columns = array();
	
	public function __construct($params)
	{
		$this->lib = new hn_ParsePlaylist();

		if(isset($params['file']))
		{			
			$this->load_file($params['file']);

			$this->source = $params['file'];
		}
		
		//die('<pre>'.print_r($this->lib->tracks, true).'</pre>');		
	}
		
	public function get_default_columns()
	{
		return $this->default_columns;
	}
	
	public function load_file($file)
	{
			if(!file_exists($file))
				$this->throw_error(1, 'Playlist file does not exist.<br />');
				
			if(!$this->lib->parse_file($file))
				$this->throw_error(1, 'Something went wrong while parsing your Playlist file. Check if the formatting is following the specifications!');	
			
			$this->tracks = $this->lib->tracks;
			
			return TRUE;
	}
	
	public function strip_radio_entries()
	{
		if(empty($this->tracks))
			return FALSE;
		
		foreach($this->tracks as $pos => $track)
		{
			if($track['type'] == 'radio')
				unset($this->track[$pos]);
		}
		
		return count($this->tracks);
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
		foreach($this->get_default_columns() as $column => $possibilities)
		{
			//We are looking for the columns that indicate information about a song.
			//So artist, title, album, etc.
			//We will try to guess it by looking at names often used
			
			foreach($possibilities as $possibility)
			{
				$result = array_keys($this->get_headers(), $possibility);
				
				if(count($result) !== 0)
					$matches[$column] = $result;
			}
		}
		
		//save the matches for later use
		$this->column_matches = $matches;
		
		//Only Artist and Track are required to have, so just check if we have a unique match there
		//If we do, then YAY, otherwise NAY
		
		log_message('debug', "scan headers matches = ".print_r($matches, true));
		
		if(count($matches['artist']) === 1 && count($matches['track']) === 1)
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
	
	public function get_sample($amount = 5)
	{
		return array_slice($this->lib->data, 0, $amount);
	}	
	
}



/* End of file CSVImporter.php */
/* Location: ./system/application/libraries/CSVImporter.php */
<?php

if(!class_exists("importer"))
	require_once("importer.php");


class TextImporter extends Importer
{
	
	private $delimiter;
	
	public function __construct()
	{
	
	}
	
	
	public function process()
	{		
		$lines = preg_split('/[\r\n]+/', $this->source, -1, PREG_SPLIT_NO_EMPTY);
		
		log_message('debug', "text lines = ".print_r($lines, true));
		
		//determine what kind of delimiter is used
		$linearray = str_split($lines[0]);
		
		foreach($linearray as $char)
		{
			switch($char)
			{
				case '-':
				$this->delimiter = $char;
				break;

				case "\t":
				log_message('debug', 'text delimiter is a tab');
				$this->delimiter = "\t";
				break;

				default:
				$this->delimiter = "\t";
				break;
			}
		}
		
		$i = 0;
		
		foreach($lines as $line)
		{

			if(preg_match('/\A(\d*)[\.\s]*(.+?)[-,\t]\s*+(.+)/i', $line, $matches) === 0)
			{
				log_message('debug', 'no track matches...');
				return FALSE;
			}

			if(is_array($matches) && count($matches) > 0)
			{
				if(isset($matches[2])) $this->rows[$i]['artist'] = trim($matches[2]);
				if(isset($matches[3])) $this->rows[$i]['title'] = trim($matches[3]);				
			}
			
			log_message('debug', print_r($matches, true));
			
			
			$i++;
		}
		
				
		return TRUE;
	}
	
	
	public function save_source()
	{
		$filename = $this->generate_filename();
		
		if(file_put_contents($filename, $this->source))
			return $filename;
		else
			return FALSE;
	}
	
	
	protected function generate_filename()
	{
		$i = 0;
		$good_filename = FALSE;
		$base_name = $this->upload_path . 'text_'.time();
		$filename = $base_name;
		$ext = '.txt';
		
		while($good_filename === FALSE)
		{
			if(file_exists($filename . $ext))
			{
				$filename = $base_name . '-'.$i;
				$i++;
			}
			else
			{
				$good_filename = TRUE;
			}
		}
		
		return $filename . $ext;
	}

}

/* End of file iTunesImporter.php */
/* Location: ./system/application/iTunesImporter.php */
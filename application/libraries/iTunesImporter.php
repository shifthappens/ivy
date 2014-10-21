<?php

if(!class_exists("importer"))
	require_once("importer.php");


class iTunesImporter extends Importer
{
	
	private $XML;

	public function __construct()
	{
	
	}
	
	
	public function parse_xml()
	{
		if(empty($this->source))
			$this->throw_error(1, "Source for XML is still empty.");
		
		// open the xml document in the DOM
		$this->XML = new DomDocument();
		if (!$this->XML->loadXML($this->source))
			$this->throw_error(1, "Could not parse iTunes XML file.");
		
		return TRUE;
	}
	
	
	public function process()
	{
		// get the root element
		$root = $this->XML->documentElement;
		
		// yeah "dict" means everything, playlist, and song that makes sense... NOT
		// find the first "dict"
		$children = $root->childNodes;
		foreach ($children as $child)
		{
			if ($child->nodeName=="dict")
			{
				$root = $child;
				break;
			}
		}
	
		// do that again, and find the second inner dict
		$children = $root->childNodes;
		foreach ($children as $child)
		{
			if ($child->nodeName=="dict")
			{
				$root = $child;
				break;
			}
		}
			
		// now go through all the child elements
		$children = $root->childNodes;
		foreach ($children as $child)
		{
			// all the sub dicts from here on should be songs
			if ($child->nodeName=="dict")
			{
				$song = NULL;
				
				// get all the elements
				$elements = $child->childNodes;
				for ($i = 0; $i < $elements->length; $i++)
				{
					// alright whomever wrote this xml file was smoking something serious
					// in normal XML documents we would do:
					//  <artist>Daft Punk</artist>
					// but in Apple iTunes bong land we do:
					//  <key>Artist</key><string>Daft Punk</string>
					
					if ($elements->item($i)->nodeName=="key")
					{
						// so I'm just going to expect that i++ (<string>, <int>, etc...) is always going to be there,
						//  if the key's name is <key>
						//  instead of doing some error checking here to make sure there are matching values to keys
						$key = $elements->item($i)->textContent;
						
						//we have some naming conventions, like name = title and all lowercase
						switch($key)
						{
							case "Name":
							$key = "title";
							break;
							
							default:
							$key = strtolower($key);
							break;
						}
						
						$i++;
						$value = $elements->item($i)->textContent;
						$song[$key]=$value;
					}
				}
				
				// save the song
				if ($song)
					$songs[] = $song;
			}
		}
		
		//Filter some things out of artist names
		$i = 0;
		foreach($songs as $song => $attributes)
		{
			foreach($attributes as $key => $value)
			{				
				if($key == "artist")
				{
					$songs[$i][$key] = trim($this->strip($value, 'the'));
					log_message('debug', 'new value = '.$songs[$i][$key]);
				}				
			}

			$i++;

		}
				
		//saving to the rows property
		$this->rows = $songs;	
	}

}

/* End of file iTunesImporter.php */
/* Location: ./system/application/iTunesImporter.php */
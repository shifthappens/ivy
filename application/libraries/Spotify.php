<?php


class Spotify
{


	protected $API_base_url = "";
	
	private $request_params = array();
	private $cover_words_table = array();
	private $url_unsafe_characters = array();
	private $unwanted_artist_parts = array();
	
	public $error_message = "";
	public $territories = array();
	public $min_popularity = 0.0;
	public $requirements = array();
	public $response;
	public $filtered_tracks = array();
	public $request_string = "";
	public $playlist_track = '';

	public function __construct()
	{
		$this->API_base_url = "http://ws.spotify.com/search/1/track?";
		
		$this->CI =& get_instance();
		
		$this->CI->load->library('curl');
		
		$this->curl = $this->CI->curl;
		
		$this->nocover_title_table = array('(cover)', 'tribute band', 'made famous by', 
		'in the style of', 'karaoke', 'instrumental', 'a tribute', 'cover version', 'sound-a-like', 'piano version', '%artist% tribute'); //%artist% means it will be replaced by artist, %totle% bu title of track
		
		$this->nocover_album_table = array('instrumental tribute', 'tribute to', 'instrumental', 'karaoke');
		
		$this->url_unsafe_characters = array('&', '$', '+', ',', '/', ':', ';', '=', '?', '@', '"', "'", '<', '>', '#', '%', '{', '}', '|', '\\', '^', '~', '[', ']', '`');
		
		$this->unwanted_artist_parts = array('the');
	}
	
	
	public function add_request_param($param, $value)
	{
		$this->request_params[$param] = $value;
	}
	
	
	public function set_track_artist($artist)
	{
		$this->playlist_track['artist'] = $artist;		
	}


	public function set_track_title($title)
	{
		$this->playlist_track['title'] = $title;
	}

	public function set_track_album($album)
	{
		$this->playlist_track['album'] = $album;
	}
	
	
	public function get_response()
	{
		$this->construct_request_string();
		
		$request_url = $this->API_base_url . $this->request_string;
		
		log_message('debug', 'Spotify request url = '.$request_url);
		
		$this->response = simplexml_load_string($this->curl->simple_get($request_url));
										
		$this->run_filters();

		$this->throw_error(2, "Got response with ".count($this->response->track)." results.");

		$this->throw_error(2, "Ran filters, kept ".count($this->filtered_tracks)." results.");
						
		return $this->filtered_tracks;
	}
	
	
	public function set_territory($territory)
	{
		if(!is_string($territory))
			$this->throw_error(1, "Territory information must me a string.");
		
		$this->territory = $territory;
		
		return TRUE;
	}
	
	
	public function set_popularity($percentage)
	{
		if(!is_int($percentage))
			$this->throw_error(1, "Popularity information must be an integer.");
		
		$this->min_popularity = ($percentage / 100);
		
		return TRUE;
	
	}
	
	
	public function set_track_requirements($element, $callbacks)
	{		
		if(is_array($callbacks))
		{
			foreach($callbacks as $callback)
			{
				preg_match_all('/\[(.*?)\]/', $callback, $matches);
				if(count($matches[0]) == 0)
					$arguments = NULL;
				else
				{
					$callback = str_replace($matches[0][0], '', $callback);
					$arguments = $matches[1][0];
				}
				
				$this->requirements[$element][] = array('callback' => $callback, 'arguments' => $arguments);
			}
		}
		else
		{
			preg_match_all('/\[(.*?)\]/', $callbacks, $matches);
			//log_message('debug', 'isset = '.isset($matches[0]));
			if(!isset($matches[0][0]))
				$arguments = NULL;
			else
			{
				$callbacks = str_replace($matches[0][0], '', $callbacks);
				$arguments = $matches[1][0];
			}
						
			$this->requirements[$element][] = array('callback' => $callbacks, 'arguments' => $arguments);
		}
		
		return TRUE;
	}
	
	
	private function construct_request_string()
	{
		$this->request_string = '';
		
		if(count($this->request_params) === 0)
		{
			foreach($this->request_params as $param => $value)
				$this->request_string .= $param . '=' . $value . '&';
			
			$this->request_string = substr($this->request_string, 0, -1);
		}
				
		if(!empty($this->playlist_track['artist']))
			$this->request_string .= $this->playlist_track['artist'] . ' ';
		if(!empty($this->playlist_track['title']))
			$this->request_string .= $this->playlist_track['title'] . ' ';
		
		$this->request_string = '&q='.urlencode($this->request_string);
		
		log_message('debug', 'final request string = '.$this->request_string);

	}
	
	
	private function run_filters()
	{
		if(!isset($this->response->track))
			return FALSE;
		
		$response = $this->response;
		$i = 0;
		$this->filtered_tracks = array();
		
		foreach($response->track as $track)
		{
			$keep = TRUE;	//by default every track has the right to stay...

			foreach($this->requirements as $element => $callbacks)	//loop through all elements of the track that have requirements and run the filters that are attached
			{
				foreach($callbacks as $callback)
				{
					if(method_exists($this, $callback['callback']))
					{
						$function = $callback['callback'];
						$callback_result = $this->$function($track, $this->playlist_track, $callback['arguments']);
						
						if($callback_result !== TRUE)	//looks like the track did not survive a callback...
						{
							log_message('debug', 'track '.$i.' died at '.$element.' in filter '.$callback['callback'].' because: '.$callback_result);
							$keep = FALSE;
							break 2; //break 2 loops, not only the foreach but also the foreach above that. We're done with this track
						}
					}
				}
				
			}
		
			if($keep)
			{
				log_message('debug', 'track '.$i.' survived, all filters passed.');
				$this->filtered_tracks[] = $this->response->track[$i];
			}				

			$i++;

		}
	}
		
	public function similar($first, $second, $min_percentage = 75)
	{
		$percentage = 0;
		similar_text(strtolower($first), strtolower($second), $percentage);
		
		if($percentage >= $min_percentage) //if strings are X% or more similar, then we have a winner
			return TRUE;
		else
			return FALSE;
	}
	
	public function contains($haystack, $needle)
	{	
		//log_message('debug', 'filter: contains; haystack: '.$haystack.'; needle: '.$needle);

		if(is_array($needle))
		{
			foreach($needle as $n)
			{
				if(stripos($haystack, $n) !== FALSE)
					return TRUE;
				elseif(stripos($haystack, $this->normalize_special_characters($n)) !== FALSE)
					return TRUE;
				else
					return FALSE;
			}
		}
		else
		{
			if(stripos($haystack, $needle) !== FALSE)
				return TRUE;
			elseif(stripos($haystack, $this->normalize_special_characters($needle)) !== FALSE)
				return TRUE;
			else
				return FALSE;		
		}
	}
	
	
	public function minimum($first, $second)
	{
		//log_message('debug', 'filter: minimum; first: '.$first.'; second: '.$second);

		if($first >= $second)
			return TRUE;
		else
			return FALSE;
	}



	public function maximum($first, $second)
	{
		//log_message('debug', 'filter: maximum');

		if($first <= $second)
			return TRUE;
		else
			return FALSE;
	}
	
		
	public function strip($input, $tostrip)
	{
		$output = str_ireplace($tostrip, '', $input);
		//log_message('debug', 'strip - input:'.$input.'; output:'.$output);
		return trim($output);
	}
	

	private function compare_artist($track, $playlist_track, $arguments = NULL)
	{
		$matches = 0;
		$artist_string = '';
		
		foreach($track->artist as $artist)
		{
			$artistname = $this->strip($artist->name, $this->unwanted_artist_parts);	//strip things like 'the' from the artist title, often causes mismatches
			if($this->contains($playlist_track['artist'], $artistname))	//is spotify artist in original artist data?
				$matches++;
			
			$artist_string .= $artistname.', ';
		}

		//log_message('debug', 'filter: compare_artist; artist_string: '.$artist_string.'; playlist_artist: '.$playlist_artist.'; matches: '.$matches);
		
		if($matches === count($track->artist))
			return TRUE;
		elseif($this->similar($playlist_track['artist'], $artistname, 88)) //last chance, if track artist and playlist artist match 88% or more it's OK
			return TRUE;		
		else
			return 'There were only '.$matches.' matches where there should be '.count($track->artist);
	}
	
	private function compare_title($track, $playlist_track, $arguments = NULL)
	{
		if($this->similar($track->name, $playlist_track['title'], 88)) //last chance, if track artist and playlist artist match 88% or more it's OK
			return TRUE;
		if($this->contains($track->name, $playlist_track['title']))	//check if the playlist track title data is found in the spotify track title 
			return TRUE;
		
		return $track->name.' is not found in '.$playlist_track['title'];
	}
	
	private function compare_territory($track, $playlist_track, $arguments = NULL)
	{
		if($track->album->availability->territories == 'worldwide') //a special status for tracks that can be played everywhere by Spotify
			return TRUE;
			
		if(!$this->contains($track->album->availability->territories, $this->territory))
			return $this->territory.' was NOT found in the haystack of territories: '.$track->album->availability->territories;
		else
			return TRUE;
	}


	private function no_cover($track, $playlist_track, $arguments = NULL)
	{
		//First we are going to check the album for signs of the track being a cover
		$table = $this->nocover_album_table;
		
		foreach($table as $phrase)
		{
			$is_cover = stripos($track->album->name, $phrase);
			$is_intended_cover = stripos($playlist_track['album'], $phrase);
			
			if($is_cover !== FALSE && $is_intended_cover === FALSE)
			{
				return 'track is a cover because "'.$phrase.'" was found in the album name "'.$track->album->name.'" and it was not an intentional cover'; 
			}
		}
		
		//Second is the check on artist and title
		$table = $this->nocover_title_table;
		
		//In case a track has more than one artist listed, we need to glue them together
		$artists_spotify = '';
		foreach($track->artist as $artist)
		{
			$artists_spotify .= $artist->name;
		}
		
		//Glue together a string of artist and title so it is easier to check for presence of the phrase in both of them at once
		$artist_and_title_spotify = $artists_spotify.' '.$track->name;
		$artist_and_title_original = $playlist_track['artist'].' '.$playlist_track['title'];
		
		foreach($table as $phrase)
		{
			$phrase = str_ireplace(array('%artist%', '%title%'), array($playlist_track['artist'], $playlist_track['title']), $phrase);
			$is_cover = stripos($artist_and_title_spotify, $phrase);
			$is_intended_cover = stripos($artist_and_title_original, $phrase);			

			if($is_cover !== FALSE && $is_intended_cover === FALSE)
			{
				return 'track is a cover because "'.$phrase.'" was found in the track name "'.$artist_and_title_spotify.'" and it was not an intentional cover'; 
			}

		}
		
		return TRUE; //survived the cover filter
	}
	

	private function throw_error($level, $message)
	{

		switch($level)
		{
			case 1:
			$terminate = TRUE;
			$level = 'error';
			break;
			
			case 2:
			$terminate = FALSE;
			$level = 'debug';
			break;
			
			case 3:
			default:
			$terminate = FALSE;
			$level = 'info';
			break;
		}
		
		$this->error_message = $message;
		log_message($level, $this->error_message);
		
		if($terminate === TRUE)
			die($this->error_message);
	}

	public function normalize_special_characters( $str )
	{
	    # Quotes cleanup
	    $str = str_replace( chr(ord("`")), "'", $str );        # `
	    $str = str_replace( chr(ord("´")), "'", $str );        # ´
	    $str = str_replace( chr(ord("„")), ",", $str );        # „
	    $str = str_replace( chr(ord("`")), "'", $str );        # `
	    $str = str_replace( chr(ord("´")), "'", $str );        # ´
	    $str = str_replace( chr(ord("“")), "\"", $str );        # “
	    $str = str_replace( chr(ord("”")), "\"", $str );        # ”
	    $str = str_replace( chr(ord("´")), "'", $str );        # ´
	
	    $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
	                                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
	                                'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
	                                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
	                                'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
	    $str = strtr( $str, $unwanted_array );
	
	    # Bullets, dashes, and trademarks
	    $str = str_replace( chr(149), "&#8226;", $str );    # bullet •
	    $str = str_replace( chr(150), "&ndash;", $str );    # en dash
	    $str = str_replace( chr(151), "&mdash;", $str );    # em dash
	    $str = str_replace( chr(153), "&#8482;", $str );    # trademark
	    $str = str_replace( chr(169), "&copy;", $str );    # copyright mark
	    $str = str_replace( chr(174), "&reg;", $str );        # registration mark
	
	    return $str;
	}


}


/* End of file Spotify.php */
/* Location: ./system/application/library/Spotify.php */
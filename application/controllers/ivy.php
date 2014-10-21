<?php

class Ivy extends Controller
{


	function Ivy()
	{
		parent::Controller();

		$this->load->library('spotify');
		$this->load->model('Playlist_model');

		$this->load->helper('form');

		$this->data = new stdClass();
		
		//setting the config variables for the uploader
		$this->upload_config['upload_path'] = './uploads/';
		$this->upload_config['max_size'] = '4096';
		$this->output->set_header('Content-type: text/html; charset=UTF-8');

	}
		
	function index()
	{				
		if($this->session->userdata('playlist_ID') !== FALSE)
			$this->session->sess_destroy();

		$this->load->view("header");
		
		$this->load->view("step1");

		$this->load->view("footer");
	}
	
	function upload()
	{			
		switch($this->input->post("uploadtype"))
		{
			case "playlist-file":
				$this->upload_config['allowed_types'] = 'm3u|pls|extm3u|xml|csv|txt|tsv|audio/x-mpegurl';
				$this->load->library('upload', $this->upload_config);

				if(!$this->upload->do_upload('sourcefile'))
				{
					$error = $this->upload->display_errors();
					$error .= print_r($this->upload->data(), true);
					
					$okay = FALSE;
				}
				else
				{
					$upload_data = $this->upload->data();
		
					$this->session->unset_userdata('offset');
					
					switch($upload_data['file_ext'])
					{
						case '.xml':				
							$this->session->unset_userdata('offset');
				
							$this->load->library('iTunesImporter', NULL, 'itunes');
							
							$this->itunes->load_file($upload_data["full_path"]);
							$this->itunes->parse_xml();
							$this->itunes->process();

							if(count($this->itunes->rows) === 0)
							{
								$okay = FALSE;
								$error = "I could not extract any track information out of your playlist.";
							}
							else
							{
								$this->data->playlist_ID = $this->Playlist_model->save_playlist($this->itunes->rows, $upload_data['full_path']);
								$this->data->source = $upload_data['full_path'];
								$this->data->num_tracks = count($this->itunes->rows);
																	
								$this->session->set_userdata(array('source' => $upload_data["full_path"], 'playlist_ID' => $this->data->playlist_ID));
							
								$okay = TRUE;
							}
						break;
						
						case '.txt':
						case '.csv':
						case '.tsv':
							$this->load->library('CSVImporter', array('file' => $upload_data["full_path"]), 'csv');
													
							if(count($this->csv->get_num_rows()) === 0)
							{
								$okay = FALSE;
								$error = "I could not extract any CSV formatted data out of your uploaded file.";
							}
							elseif(!$this->csv->scan_headers())	//do we have header names that Ivy understands?
							{
								$this->session->set_userdata(array('source' => $upload_data["full_path"], 'column_matches' => $this->csv->get_matched_columns(), 'setting_territory' => $this->input->post('territory'), 'setting_covers' => $this->input->post('covers')));
								redirect('ivy/verify');
							}
							else
							{
								$this->data->playlist_ID = $this->Playlist_model->save_playlist($this->csv->get_all_rows(), $upload_data['full_path'], $this->csv->get_matched_columns());
								$this->data->source = $upload_data['full_path'];
								$this->data->num_tracks = $this->csv->get_num_rows();
																	
								$this->session->set_userdata(array('source' => $upload_data["full_path"], 'playlist_ID' => $this->data->playlist_ID));
								
								$okay = TRUE;
							}
						break;
						
						case '.m3u':
						case '.pls':
							$this->load->library('M3UImporter', array('file' => $upload_data["full_path"]), 'm3u');
						break;
					}
		
				}

			break;
						
			case "copypaste":
				$this->load->library('TextImporter', NULL, 'text');
				$this->text->load_string($this->input->post('sourcefile'));
				$this->text->process();
				
				if(count($this->text->rows) === 0)
				{
					$okay = FALSE;
					$error = "I could not extract any track information from your input.";
				}
				else
				{
					$this->data->source = $this->text->save_source();
					$this->data->playlist_ID = $this->Playlist_model->save_playlist($this->text->rows, $this->data->source, array('artist_column' => 'artist', 'track_column' => 'title'));

					$this->data->num_tracks = count($this->text->rows);
														
					$this->session->set_userdata(array('source' => $this->data->source, 'playlist_ID' => $this->data->playlist_ID));
					
					$okay = TRUE;

				}
			break;
			
			default:
			$this->load->view("UnknownError", $this->data);
			break;
		}
		
		$this->load->view("header");
		
		if($okay)
		{
			$this->session->set_userdata(array('setting_territory' => $this->input->post('territory'), 'setting_covers' => $this->input->post('covers')));
			redirect('ivy/run');
		}
		else
			$this->load->view('step1', array('error' => $error));

		$this->load->view("footer");
	}
	
	function verify()
	{
		if(!$this->session->userdata('source'))
			redirect('ivy');
		
		$this->load->view("header");
		
		$this->load->library('CSVImporter', array('file' => $this->session->userdata('source')), 'csv');
		
		if($this->input->post('submit')) //a form has been submitted with confirmation of the right columns for Ivy to use
		{
			$headers = $this->csv->get_headers();
			$error = array();
			
			$new_headers = array();
						
			//Loop over the headers to see what the value is of them in the submitted form
			foreach($headers as $header)
			{
				switch($this->input->post('csvverify-select-'.strtolower($header)))
				{
					case 'NULL':
					break;
					
					case 'artist':
					$new_headers['artist_column']= $header;
					break;
					
					case 'track':
					$new_headers['track_column'] = $header;
				}
			}
			
			if(!array_key_exists('artist_column', $new_headers))
			{
				$error[] = "You didn't select a column that represents the Artists in your file!";
			}			
			if(!array_key_exists('track_column', $new_headers))
			{
				$error[] = "You didn't select a column that represents the Track Titles in your file!";
			}
			
			if(count($error) === 0)
			{
				//no errors, we can save the data
				$this->csv->set_matched_columns($new_headers);
				$playlist_ID = $this->Playlist_model->save_playlist($this->csv->get_all_rows(), $this->session->userdata('source'), $this->csv->get_matched_columns());
				$this->session->set_userdata('playlist_ID', $playlist_ID);
				redirect('ivy/run');
			}
			else
			{
				$this->load->view('step1a', array('headers' => $this->csv->get_headers(), 'sample_data' => $this->csv->get_sample(5), 'error' => $error));
			}
		}
		else //no POST exists, so just load the page
		{
			$this->load->view('step1a', array('headers' => $this->csv->get_headers(), 'sample_data' => $this->csv->get_sample(5)));
		}

		$this->load->view("footer");		
				
	
	}	
		
	function process()
	{
		$this->load->view("header");

		if(!$this->session->userdata('playlist_ID'))
			die("Session expired....");
		
		if(!$this->session->userdata('offset'))
			$this->session->set_userdata('offset', 1);
			
		$track = $this->Playlist_model->get_item($this->session->userdata('playlist_ID'), $this->session->userdata('offset'));
		
		if($track === FALSE)
		{
			die("End of playlist.");
		}
		else
		{	
			log_message('debug', 'track info = '.print_r($track, true));

			$this->spotify->set_track_artist($track['artist']);
			$this->spotify->set_track_title($track['title']);
			
			//$this->spotify->set_track_requirements('popularity', 'minimum[0.5]');
			$this->spotify->set_track_requirements('territory', 'contains[NL]');
			$this->spotify->set_track_requirements('track_name', 'no_cover');
			
			$response = $this->spotify->get_response();
			
			$this->session->set_userdata('offset', ($this->session->userdata('offset') + 1));

			$this->load->view('spotify_response', array("response" => $response, "request_string" => $this->spotify->request_string));
		}
		
		$this->load->view("footer");

	}
	
	function run()
	{
		if(!$this->session->userdata('playlist_ID'))
			redirect('');
		
		$this->load->view("header");
		
		$num_tracks = $this->Playlist_model->get_count($this->session->userdata('playlist_ID'))->total_tracks;

		$this->load->view('step2', array('num_tracks' => $num_tracks));

		$this->load->view("footer");		
	}

	function cleanup()
	{
		$this->_cleanup(TRUE);
	}
	
	function _cleanup($print_to_screen = FALSE)
	{			
		$this->Playlist_model->clean_up_playlists($print_to_screen);
	}
		
	
	function ajax_get_next()
	{		
		log_message('debug', "---------- NEW AJAX REQUEST ------------");
		$this->output->set_header('Content-type: text/x-json');
		
		$args = $this->uri->uri_to_assoc();
				
		if(!isset($args['playlist']) || !isset($args['track']))
		{
			log_message('debug', "important arguments are missing. send back.");
			$jsonresponse = json_encode(array('code' => 'MISSING_ARGUMENTS'));
		}		
		else
		{
			$trackdata = $this->Playlist_model->get_item($args['playlist'], $args['track']);
			
			if($trackdata === FALSE)
			{
				log_message('debug', "We have reached the end of our playlist. Error message was: ".$this->Playlist_model->errormessage);		
				$jsonresponse = json_encode(array('code' => 'END_OF_PLAYLIST'));
				
				//while at the end of the playlist, clean up playlists that are too old and can be deleted to free up space				
				$this->_cleanup();
			}
			else
			{
				//log_message('debug', 'artist = '.utf8_decode($trackdata['artist']));
				$this->spotify->set_track_artist($trackdata['artist']);
				$this->spotify->set_track_title($trackdata['title']);
				$this->spotify->set_track_album($trackdata['album']);
				
				if($this->session->userdata('setting_territory') != 'ALL')
				{
					$this->spotify->set_territory($this->session->userdata('setting_territory'));
					$this->spotify->set_track_requirements('territory', 'compare_territory');
				}

				if($this->session->userdata('setting_covers'))
				{
					$this->spotify->set_track_requirements('track_title', array('compare_title', 'no_cover'));
					$this->spotify->set_track_requirements('album_name', 'no_cover[nocover_album_table]');
				}

				$this->spotify->set_track_requirements('artist_name', array('compare_artist'));
				
				$response = $this->spotify->get_response();
				
				log_message('debug', 'spotify track array count: '.count($response));
				
				if(is_array($response))
				{
					if(count($response) === 0)
					{
						log_message('debug', "There was not 1 track that matched.");
						$jsonresponse = json_encode(array('code' => 'NO_SUITABLE_TRACKS', 'trackdata' => $trackdata));
					}
					else
					{
						if(count($response) > 10)
							$response = array_slice($response, 0, 10);	//we don't need that many results
						
						//log_message('debug', 'the sliced response: '.print_r($sliced_response, true));
						
						log_message('debug', "We have tracks to send back.");
						$jsonobject = array('response' => (object)$response, 'trackdata' => $trackdata);

						$jsonresponse = json_encode($jsonobject);
						
						log_message('debug', 'json string = '.$jsonresponse);
					}
				}
				elseif(is_null($response))
				{
					$jsonresponse = json_encode(array('code' => 'NO_TRACKS_FOUND', 'trackdata' => $trackdata));
				}
				else
				{
					$jsonresponse = json_encode(array('code' => 'UNKNOWN_ERROR', 'trackdata' => $trackdata));					
				}				
			}

		}		

		$this->load->view('spotify_json_response', array('jsonresponse' => $jsonresponse, 'trackdata' => $trackdata));

		log_message('debug', "---------- END OF AJAX REQUEST ------------");

	}
	
	
	function ajax_get_playlist_count()
	{
		$this->output->set_header('Content-type: text/javascript');
		
		$args = $this->uri->uri_to_assoc();
				
		if(!isset($args['playlist']))
		{
			$this->output->set_header("X-JSON: ".json_encode(array('code' => 'MISSING_ARGUMENTS')));
		}		
		else
		{
			$playlistdata = $this->Playlist_model->get_count($args['playlist']);
			
			if($playlistdata === FALSE)
				$this->output->set_header("X-JSON: ".json_encode(array('code' => 'INVALID_PLAYLIST')));
			else
			{
				$this->output->set_header("X-JSON: ".json_encode(array('total_tracks' => $playlistdata->total_tracks)));
			}
		}
	
	}
	
	function ajax_post_usabletracks()
	{
		$this->output->set_header('Content-type: text/x-json');

		if(!$this->input->post('playlist_ID') || !$this->input->post('usableTracks'))
			$this->load->view('spotify_json_response', array('jsonresponse' => json_encode(array('code' => 'NO_ARGUMENTS'))));
		
		$result = $this->Playlist_model->save_found_tracks($this->input->post('playlist_ID'), $this->input->post('usableTracks'));
		
		switch($result)
		{
			case TRUE:
				$this->load->view('spotify_json_response', array('jsonresponse' => json_encode(array('code' => 'OK'))));
			break;
			
			case FALSE:
				$this->load->view('spotify_json_response', array('jsonresponse' => json_encode(array('code' => 'FAILED'))));
			break;
			
			default:
				$this->load->view('spotify_json_response', array('jsonresponse' => json_encode(array('code' => $result))));
			break;
		}
		
	}
}

/* End of file ivy.php */
/* Location: ./system/application/controllers/ivy.php */
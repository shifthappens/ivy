<?php


class Playlist_model extends CI_Model
{

	function Playlist_model()
	{
		parent::__construct();
		//$this->ci =& get_instance();
	}
	
	
	function save_playlist($data, $source, $headers)
	{
		if(!is_array($data) || count($data) === 0)
			log_message('error', 'Playlist data for saving should be an array and be of length >= 1.');
		
		$this->db->trans_begin();		

		$this->db->insert('playlists', array('playlist_ID' => NULL, 'playlist_date' => NULL, 'playlist_source' => $source));
		
		$playlist_ID = $this->db->insert_id();	//this is the last inserted ID in the database and thus the ID we will be using to identify our playlist
		
		$tracknr = 1;
		
		//print_r($data);
		
		foreach($data as $row)
		{
			//if($tracknr === 1) print_r($row);
			$artist = $headers['artist_column'];
			$title = $headers['track_column'];

			if(!isset($row[$artist]) || !isset($row[$title]))
				continue;
			
			$this->db->set('artist', $row[$artist]);
			$this->db->set('title', $row[$title]);
			
			$this->db->insert('tracks', array('playlist_ID' => $playlist_ID, 'track' => $tracknr));
			
			$tracknr++;

		}
		
		if($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			
			return FALSE;
		}
		else
		{
			$this->db->trans_commit();
		
			return $playlist_ID;
		}
	}
	
	function get_all_tracks($playlist_ID)
	{
		$result = $this->db->select()->where('playlist_ID', $playlist_ID)->get('tracks');
		
		return $result;
	}
	
	function get_playlist($ID)
	{
		$result = $this->db->select()->where('playlist_ID', $ID)->get('playlists');
		
		return $result;
	}
	
	function playlist_exists($ID)
	{
		$result = $this->db->select('1', FALSE)->where('playlist_ID', $ID)->get('playlists');
		
		if($result->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function get_item($ID, $offset = 0)
	{
		$result = $this->db->select()->where(array('playlist_ID' => $ID, 'track' => $offset))->get('tracks');
		
		if($result->num_rows() == 0)
		{
			$this->errormessage = "The track doesn't exist (anymore).";
			return FALSE;
		}

		return $result->row_array();
	}
	
	function get_count($ID)
	{
		$result = $this->db->select('COUNT(*) AS total_tracks')->where('playlist_ID', $ID)->get('tracks');
		
		if($result->num_rows() == 0)
		{
			$this->errormessage = "The playlist doesn't exist (anymore).";
			return FALSE;
		}
		
		return $result->row();
	}
	
	function save_found_tracks($playlist_ID, $trackcount)
	{
		if(!$this->playlist_exists($playlist_ID))
			return "INVALID_PLAYLIST";
		
		if($this->db->where('playlist_ID', $playlist_ID)->update('playlists', array('found_tracks' => $trackcount)))
			return TRUE;
		else
			return FALSE;
	}
	
	
	function delete_item($ID, $key)
	{
	
	}
	
	function delete_playlist($ID, $print_to_screen = FALSE)
	{
		if(!$this->playlist_exists($ID))
			return "INVALID_PLAYLIST";
		
		if($this->db->where('playlist_ID', $ID)->delete('playlists'))
		{
			if($print_to_screen)
				$this->load->view('cleanup', array('message' => "Playlist with ID ".$ID." deleted successfully. <br />"));

			return TRUE;
		}
		else
		{
			if($print_to_screen)
				$this->load->view('cleanup', array("Playlist with ID ".$ID." NOT deleted! <br />"));

			return FALSE;
		}
	}
	
	function clean_up_playlists($print_to_screen = FALSE)
	{
		$results = $this->db->select(array('playlist_ID', 'playlist_source'))->where('DATE_SUB(CURDATE(),INTERVAL 1 DAY) >= playlist_date')->get('playlists');
		
		if($results->num_rows() === 0 && $print_to_screen)
		{
			$this->load->view('cleanup', array('message' => "0 playlists had to be removed. <br />"));
			return TRUE;
		}
		elseif($results->num_rows() === 0 && $print_to_screen === FALSE)
		{
			return TRUE;
		}
		
		$this->load->model('FileUploads_model');

		$del = 0;
		
		foreach($results->result() as $playlist)
		{
			//these playlists are ready for deletion
			$this->delete_playlist($playlist->playlist_ID, $print_to_screen);
			
			$this->FileUploads_model->cleanup_files(time()-86400, $print_to_screen); //delete all files older than one day
			
			$del++;
		}

		log_message('debug', 'Playlist model: deleted '.$del.' playlists during cleanup.');

		if($print_to_screen)
			$this->load->view('cleanup', array('message' => 'Playlist model: deleted '.$del.' playlists during cleanup. <br />'));

		return $del;
	}
	
}

/* End of file Playlist_model.php */
/* Location: ./system/application/models/Playlist_model.php */
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
	<meta name="keywords" content="spotify, playlist, itunes, playlists, export, automatic, generator, generate, import, csv, freetext, copy, paste" />
	<meta name="description" content="Import your existing playlists from iTunes and other applications and services into Spotify so you can listen to your favourite music anywhere with Ivy!" />	
	<meta http-equiv="Content-type" value="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>resources/css/ivy.css" media="all" />
		<title>Ivy - Import your playlists into Spotify</title>

		<script type="text/javascript" src="<?=base_url();?>jsmin/b=js&amp;f=prototype.js,IvyControls.js,SpotifyLists.js,ZeroClipboard.js"></script>		

		<?php if($this->session->userdata('playlist_ID')): ?>
		<script type="text/javascript">
		ZeroClipboard.setMoviePath("<?=base_url() ?>resources/ZeroClipboard.swf");
		document.observe('dom:loaded', function() { var list = new SpotifyList(<?= $this->session->userdata('playlist_ID') ?>); } );
		</script>
		
		<?php endif; ?>
		
		<script type="text/javascript">
		document.observe('dom:loaded', function() { var ivyctrl = new IvyControls(); } );
		</script>
		
	<base href="<?=base_url()?>" />	
	</head>
	<body>
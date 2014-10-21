<p><strong>Search query: </strong> <?=urldecode($query)?></p>

<p><strong><a href="<?=$trackinfo['href']?>">Open this track in Spotify...</a></strong></p>

<script type="text/javascript">

function open_refresh()
{
	window.location.href = "<?=$trackinfo['href']?>";
	
	window.setTimeout('window.location.href = "<?=site_url(uri_string())?>"', 5000);
}


open_refresh();

//window.setTimeout('open_refresh()', 2000);

</script>
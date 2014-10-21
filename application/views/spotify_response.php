<pre>Request: <?=$request_string?></pre>
<h1>Track <?= $this->session->userdata('offset') ?></h1>
<?php
if(!isset($response[0])):
?>
No tracks found.
<?php
else:
$track = $response[0];
?>
<table width="500" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="50%">Artist</td>
		<td width="50%"><?=$track->artist->name?></td>
	</tr>
	<tr>
		<td>Title</td>
		<td><?=$track->name?></td>
	</tr>
	<tr>
		<td>Album</td>
		<td><?=$track->album->name?></td>
	</tr>
</table>
<?php
endif;
?>

<!-- <?=print_r($response, true); ?> -->
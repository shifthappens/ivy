<!--
<div class="button">
<button id="nextitem_btn">Get next item in playlist</button>
</div>

<br />
<br />
-->

<h1 id="tracknumber">Fetching...</h1>
<h3 id="query"></h3>

<div class="result" id="result">

	<div id="tracklistdiv">
		Tracks: <br />
		<textarea id="tracklist" name="tracklist" style="width: 325px; height: 200px;"></textarea>
	</div>
	
	<div id="resultdiv">
	<br />
		<table id="resulttable" width="100%" cellpadding="2" cellspacing="2" border="0">
			<tr>
				<td width="50%">Artist</td>
				<td width="50%" id="artist_name">Fetching...</td>
			</tr>
			<tr>
				<td>Title</td>
				<td id="track_name">Fetching...</td>
			</tr>
			<tr>
				<td>Album</td>
				<td id="album_name">Fetching...</td>
			</tr>
		</table>
		
		<div id="message" style="color: red; font-weight: bold;"></div>
		
		<div id="copy_btn" style="display:none; z-index: 100000; cursor: pointer; height: 150px; width: 150px;">
			<div id="copyinput"></div>		
		</div>

	</div>	
</div>

<div id="rawresults"></div>
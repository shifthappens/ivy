<div id="container">

	<?php $this->load->view("IvyHeader"); ?>	
	
	<div id="application">
	
	<?php $this->load->view("StepComponent", array("count" => 3, "active" => 2)); ?>
		
		<div id="content">
		
			<div id="headerstep2">
				<h1>Step 2: Process</h1>
				<h3>Let's make this a Spotify playlist!</h3>
			</div>
			
			<div id="headerstep3" style="display: none;">
				<h1>Step 3: Done!</h1>
				<h3>Your playlist is served.</h3>			
			</div>
			
			<div class="textbox">
			
				<div id="playlistoverview">
					<h1>Total tracks&nbsp;&nbsp;&nbsp;&nbsp; <span><?=$num_tracks?></span></h1>
					
					<p>I successfully loaded and processed your playlist, and recognized <?=$num_tracks?> tracks.<br />
					If you are ready, click the button below to turn this in a Spotify playlist!
					</p>
				
					<div class="center"><button name="ivystart" id="ivystart">Spotify this playlist</button></div>
				</div>
				
				<div id="ivyprogress" style="display: none;">
					<div class="center"><div id="indicator"><img src="resources/ajax-loader.gif" alt="" width="31" height="31" /> <h1>Processing...</h1></div></div>
					<div class="currenttrack lightfont">Track <span id="currenttrack">...</span> of <span class="totaltracks">...</span></div>
					
					<div id="message">
					I'm now searching all of your tracks in the Spotify library. This could take a while, depending on the size of your playlist. 
					Be aware that not all songs might be available on Spotify (yet).
					</div>
				</div>
				
				<div id="ivydone" style="display: none;">
					<h1><img src="resources/done.png" alt="" width="40" height="40" /> Total found &nbsp;&nbsp; <span class="usabletracks">...</span></h1>
					
					<p>I searched on Spotify, and found <span class="usabletracks">...</span> tracks out of <span class="totaltracks">...</span>.
					To get this playlist into Spotify, simply click the button below, and paste them into any Spotify playlist in the desktop application.
					</p>
				
					<div id="copyimage">
						<a><img src="resources/playlist.png" alt="playlist" width="128" height="128" /></a><br />
					
						<div class="spotifydark" id="clipboardmessage">Click image to copy to clipboard</div>
					</div>
				</div>
				
				<form action="ivy/finished" method="post" name="tracklistform" id="tracklistform" style="display: none;">
					<textarea name="tracklist" id="tracklist"></textarea>
				</form>
			
			</div>
	
			<?php $this->load->view('backbutton') ?>

		</div>

	<?php $this->load->view('appfooter') ?>
		
	</div>
	
</div>
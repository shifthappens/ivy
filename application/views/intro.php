<div id="container">

	<?php $this->load->view("IvyHeader"); ?>	
	
	<div id="application">
	
	<?php $this->load->view("StepComponent", array("count" => 3, "active" => 0)); ?>
		
		<div id="content">
		
			<h1>Hi, I'm Ivy. <span id="startnow"><button name="startnow" onclick="window.location.href='<?=base_url();?>ivy'"><a href="<?=base_url();?>ivy">Import your playlist now &raquo;</a></button></span></h1>
						
			<div class="textbox">

<!-- 				<h4>What is Ivy?</h4>
				<p>Do you love Spotify? So do I! But Spotify probably isn't your first experience with music, so you've got plenty of
				playlists already, in iTunes for instance. Ivy is a new webservice that can convert your existing playlists into Spotify playlists.
				That way you won't have to start from scratch, searching all that music again. Ivy does that for you.</p>
				
				<h4>Ivy is free.</h4>
				<p>You probably already invested a lot of time into your playlist, finding that right mix of tunes you love to listen to.
				Ivy is FREE of charge, for everyone. You can, of course, <a href="#donate">donate if you found it useful</a>. That way Ivy can continue to improve the service,
				like adding more import options and improving the search functionality.</p>
				
				<h4>How does it work?</h4>
				<p>Ivy is already quite simple to use, with only three steps to create your playlist. You could watch the short screencast below if you'd
				like to see a demo and how everything works, or just click "Import your playlist now &raquo;" to get started.</p>
				
				<br />
				<h4>Ivy in action!</h4>
				<div id="screencast">
				<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="480" height="360" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="src" value="http://vimeo.com/moogaloop.swf?clip_id=9065979&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=c9ff23&amp;fullscreen=1" /><embed type="application/x-shockwave-flash" width="461" height="288" src="http://vimeo.com/moogaloop.swf?clip_id=9065979&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=c9ff23&amp;fullscreen=1" allowscriptaccess="always" allowfullscreen="true"></embed></object>
				</div>
			</div>
	
			<?php $this->load->view('backbutton') ?> -->

				<div class="warning">
					<strong>Ivy is currently being rebuilt.</strong>
					<p>After 6 years of support, Spotify finally retired their old API and Ivy can't quite work with the new one yet.
						This requires a rewrite of many parts of the tool. This will take some time. An ETA can't be given at this moment.
						I know a lot of people are enjoying Ivy and would like to see it back. It started as a hobby project back in 2010 and
						has since then grown to become a website where more than 200 people upload their playlists every day.</p>
				</div>

		</div>


	<?php $this->load->view('appfooter') ?>
	

	</div>
	
</div>
<div id="container">

	<?php $this->load->view("IvyHeader"); ?>	
	
	<div id="application">
	
	<?php $this->load->view("StepComponent", array("count" => 3, "active" => 1)); ?>
		
		<div id="content">
		
			<h2>Step 1: Upload</h2>
			<h3>Let me take a look at your playlist</h3>
			
			<?php $this->load->view("TabComponent", array("tabs" => array("CSV", "iTunes", "Copy/paste"), "selected" => "iTunes", "rel" => array('uploadset-1', 'uploadset-1', 'uploadset-2'))); ?>
			
			<div class="tabbox">
			
				<div class="uploadform" id="csvform">
				
					<form action="upload" method="post" enctype="multipart/form-data">

						<fieldset id="uploadset-1">
							<legend>Select the playlist from your computer</legend>
							<div class="fileinput">
								<div class="fakefileinput" id="fakeinput">
									<input type="text" name="fakefilename" id="fakefilename" value="/Volumes" /> <div class="browsebutton">BROWSE &raquo;</div>
								</div>
								<input class="realfileinput" id="realfilename" type="file" name="sourcefile" />
							</div>
						</fieldset>

						<fieldset id="uploadset-2" style="display: none;">
							<legend>Paste the Artist + Titles directly</legend>
							
							<textarea name="sourcefile"></textarea>
						</fieldset>
						
						<fieldset>
						
							<legend>Set some options for your Spotify playlist</legend>
							<p><input type="checkbox" id="covertoggle" name="covers" value="1" /><label for="covertoggle">Only include original performances (no covers)</label></p>

							<select name="territory">
								<option value="ALL" selected="selected">All countries</option>
								<option value="SE">Sweden (SE)</option>
								<option value="NO">Norway (NO)</option>
								<option value="FI">Finland (FI)</option>
								<option value="UK">United Kingdom (UK)</option>
								<option value="FR">France (FR)</option>
								<option value="US">United States (US)</option>
								<option value="ES">Spain (ES)</option>
							</select>							
							Select your profile's country for better results
						
						</fieldset>
						
						<div class="center"><button type="submit" name="upload" value="1">Upload</button></div>
						
						<input type="hidden" id="uploadtype" name="uploadtype" value="" />
					
					</form>
				
				</div>
			
			
			</div>
		</div>
	
	</div>
	
</div>
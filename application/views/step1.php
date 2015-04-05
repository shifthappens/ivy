<div id="container">

	<?php $this->load->view("IvyHeader"); ?>	
	
	<div id="application">
	
	<?php $this->load->view("StepComponent", array("count" => 3, "active" => 1)); ?>
		
		<div id="content">
		
			<h1>Step 1: Upload</h1>
			<h3>Let me take a look at your playlist</h3>
			
			<?php
			switch($this->input->post('uploadtype'))
			{
				case 'playlist-file':
				default:
				$selected = "Playlist file";
				break;
				
				case 'copypaste':
				$selected = "Copy/paste";
				break;
			}
			?>
			<?php $this->load->view("TabComponent", array("tabs" => array("Playlist file", "Copy/paste"), "selected" => $selected, "rel" => array('uploadset-1', 'uploadset-2'))); ?>
			
			<div class="tabbox">
				<?php if(isset($error)): ?>
				<div class="formerrors">
					<?=$error ?>
				</div>
				<?php endif; ?>

				<div class="uploadform" id="csvform">
				
					<form action="ivy/upload" method="post" enctype="multipart/form-data">

						<fieldset id="uploadset-1" style="display: none;">
							<legend>Select a playlist from your computer</legend>
							<div class="fileinput">
								<div class="fakefileinput" id="fakeinput">
									<input type="text" name="fakefilename" id="fakefilename" value="Select iTunes or CSV formatted playlist..." /> <div class="browsebutton">BROWSE &raquo;</div>
								</div>
								<input class="realfileinput" id="realfilename" type="file" name="sourcefile" />
							</div>
						</fieldset>

						<fieldset id="uploadset-2" style="display: none;">
							<legend>Paste the Artist + Titles directly</legend>
							
							<textarea name="sourcefile"><?= $this->input->post('sourcefile') ? $this->input->post('sourcefile') : '' ?></textarea>
						</fieldset>
						
						<fieldset>
						
							<legend>Set some options for your Spotify playlist</legend>
							<p><input <?=$this->input->post('covers') ? ' checked="checked"' : '' ?> type="checkbox" id="covertoggle" name="covers" value="1" /><label for="covertoggle">Only include original performances (no covers)</label></p>

							<select name="territory">
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'ALL' ? ' selected="selected"' : '' ?> value="ALL" selected="selected">All countries</option>

								<?php foreach($iso3316_countries as $code => $country): ?>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == $code ? ' selected="selected"' : '' ?> value="<?=$code?>"><?=$country?></option>
								<?php endforeach; ?>

							</select>
							Select your profile's country for better results
						
						</fieldset>
						
						<div class="center"><button type="submit" name="upload" value="1">Upload</button></div>
						
						<input type="hidden" id="uploadtype" name="uploadtype" value="" />
					
					</form>
				
				</div>
			
			
			</div>
	
			<?php $this->load->view('backbutton') ?>

		</div>

	<?php $this->load->view('appfooter') ?>

	</div>
	
</div>
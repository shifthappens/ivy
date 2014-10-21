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
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'AT' ? ' selected="selected"' : '' ?> value="AT">Austria</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'BE' ? ' selected="selected"' : '' ?> value="BE">Belgium</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'DK' ? ' selected="selected"' : '' ?> value="DK">Denmark</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'FO' ? ' selected="selected"' : '' ?> value="FO">Faroe Islands</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'FI' ? ' selected="selected"' : '' ?> value="FI">Finland</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'FR' ? ' selected="selected"' : '' ?> value="FR">France</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'NO' ? ' selected="selected"' : '' ?> value="NO">Norway</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'ES' ? ' selected="selected"' : '' ?> value="ES">Spain</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'SE' ? ' selected="selected"' : '' ?> value="SE">Sweden</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'CH' ? ' selected="selected"' : '' ?> value="CH">Switzerland</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'NL' ? ' selected="selected"' : '' ?> value="NL">The Netherlands</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'UK' ? ' selected="selected"' : '' ?> value="UK">United Kingdom</option>
								<option<?=$this->input->post('territory') && $this->input->post('territory') == 'US' ? ' selected="selected"' : '' ?> value="US">United States</option>
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
<div id="container">

	<?php $this->load->view("IvyHeader"); ?>	
	
	<div id="application">
	
	<?php $this->load->view("StepComponent", array("count" => 3, "active" => 1)); ?>
		
		<div id="content">
		
			<h1>Step 1a: Verify</h1>
			<h3>Check if I'm going to use the right data</h3>
			
			<div>
				<p>You uploaded a data file with comma or tab separated values, but I can't quite tell what the different fields are.
				Please help me! Select at least the artist and track title columns below. Album and year are optional and might not be
				used while querying Spotify.</p>
				
				<?php if(isset($error)): ?>
				<div class="formerrors">
					<?php foreach($error as $message): ?>
						<p><?=$message?></p>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				
				<form action="ivy/verify" method="post" name="csvverify">
				
					<div id="csvverify-frame">
						<table id="csvverify-table">
							<?php foreach($headers as $header):?>
							<th>
							<select name="csvverify-select-<?=strtolower($header)?>">
								<option value="NULL"<?=$this->input->post('csvverify-select-'.strtolower($header)) && $this->input->post('csvverify-select-'.strtolower($header)) == 'NULL' ? ' selected="selected"' : '' ?>>--Don't use this column--</option>
								<option value="artist"<?=$this->input->post('csvverify-select-'.strtolower($header)) && $this->input->post('csvverify-select-'.strtolower($header)) == 'artist' ? ' selected="selected"' : '' ?>>Artist</option>
								<option value="track"<?=$this->input->post('csvverify-select-'.strtolower($header)) && $this->input->post('csvverify-select-'.strtolower($header)) == 'track' ? ' selected="selected"' : '' ?>>Track Title</option>
							</select><br />
							<?=$header?>
							</th>
							<?php endforeach; ?>
							<?php foreach($sample_data as $row): ?>
							<tr>
								<?php foreach($row as $value): ?>
								<td><?=$value?></td>
								<?php endforeach; ?>
							</tr>
							<?php endforeach; ?>
							<tr>
								<?php for($i = 0; $i < count($sample_data[0]); $i++): ?>
								<td>...</td>
								<?php endfor; ?>
							</tr>
						</table>
					</div>

					<div class="center"><button type="submit" name="submit" value="1">Confirm</button></div>					
					
				</form>
			</div>
	
			<?php $this->load->view('backbutton') ?>

		</div>

	<?php $this->load->view('appfooter') ?>

	</div>
	
</div>
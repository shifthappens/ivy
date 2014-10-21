<?= $error ?>

<?php
	$formConfig = array(
		'type'		=>	'multipart',
		'action'	=>	'home/upload',
		'legend'	=>	'Upload CSV file',
		'input_file'=>	TRUE,
		'hidden'	=>	array(
							array(
								'name' => 'upload_file_type',
								'value' => 'csv'
							)
						),
	);
	
	$this->load->view('Form', $formConfig);
	
	$formConfig['hidden'][0]['value'] = 'itunes';
	$formConfig['legend'] = 'Upload iTunes XML file';
	
	$this->load->view('Form', $formConfig);
		
	$this->load->view('TextAreaForm', array('legend' => 'Upload plain text'));
?>
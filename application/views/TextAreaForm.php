<?php

	$formConfig = array(
		'type'		=>	'',
		'action'	=>	'home/upload',
		'legend'	=>	$legend,
		'input_file'=>	FALSE,
		'textareas'	=>	array(
							array(
								'name' => 'tracklist',
								'rows' => '10',
								'cols' => '50'
							)
						),
		'hidden'	=>	array(
							array(
								'name' => 'upload_file_type',
								'value' => 'text'
							)
						),
	);

$this->load->view('Form', $formConfig);
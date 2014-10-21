<?php
if(isset($type)):
	if($type == 'multipart'):
		echo form_open_multipart($action);
	elseif($type == ''):
		echo form_open($action);
	endif;
endif;
?>


<fieldset>
	<?php if(isset($legend)): ?><legend><?=$legend?></legend><?php endif; ?>
	<?php if(isset($input_file) && $input_file !== FALSE): ?><input type="file" name="sourcefile" id="sourcefile" /><?php endif; ?>
	<?php
	if(isset($textareas)):
		foreach($textareas as $element):
	?>
	<textarea rows="<?=$element['rows']?>" cols="<?=$element['cols']?>" name="<?=$element['name']?>" /><?= $this->input->post($element['name']) ? $this->input->post($element['name']) : '' ?></textarea>
	<?php
		endforeach;
	endif;
	?>
</fieldset>
<?php
if(isset($hidden)):
	foreach($hidden as $element):
?>
<input type="hidden" name="<?=$element['name']?>" value="<?=$element['value']?>" />
<?php
	endforeach;
endif;
?>
<button type="submit" name="SubmitForm" id="" value="1"><?= isset($submit) ? $submit : "Upload and check" ?></button>

</form>

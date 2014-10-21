			<ul class="tabs">
<?php
$i = 0;
foreach($tabs as $name):
?>
				<li><a id="<?=url_title(strtolower($name))?>"<?= isset($rel[$i]) ? ' rel="'.$rel[$i].'"' : ''?><?= isset($selected) && $selected == $name ? ' class="selected"' : '' ?>><?=$name?></a></li>
<?php 
$i++;
endforeach;
?>
			</ul>

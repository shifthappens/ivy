		<div id="steps">
<?php

for($i = 1; $i <= $count; $i++):

?>
			<div class="step <?php if($i == $count) echo 'laststep'; elseif($i == 1) echo 'firststep'; ?>">
				<div id="step-<?=$i?><?= isset($active) && $i == $active ? "-active" : "" ?>"></div>
			</div>
<?php
endfor;
?>

		</div>
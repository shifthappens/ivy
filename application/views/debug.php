<pre><?php

 if(!isset($mode))
 	print_r($data, true);
 elseif($mode == 'string')
 	echo substr($data, 1130, 1200); ?></pre>
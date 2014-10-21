<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Pagination
| -------------------------------------------------------------------------
| Lets you define standard config for the Pagination library built in Code Ingiter
| 
|
|	http://codeigniter.com/user_guide/libraries/pagination.html
|
*/

$config["per_page"] = 15;
$config["num_links"] = 5;
$config["first_link"] = lang("pagination_first");
$config["last_link"] = lang("pagination_last");
$config["full_tag_open"] = lang("pagination_open") . "<span>";
$config["full_tag_close"] = "</span>";



/* End of file pagination.php */
/* Location: ./system/application/config/pagination.php */
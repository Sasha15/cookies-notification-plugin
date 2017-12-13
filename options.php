<?php
var_dump($_POST);
if(isset($_POST['special_content'])){
	update_option('special_content', $_POST['special_content']);
}
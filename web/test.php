<?php
#support static files with php built in web server
if (file_exists($_SERVER["DOCUMENT_ROOT"] . $_SERVER['REQUEST_URI'])) {
	return false;
} else {
  	require 'index.php';
}
<?php

class Template {
	public static function render($templateName,$vars,$code='') {
		$templateName = '_'.$templateName.'.php';
		$fullPath = 'templates/'.$templateName;

		if(is_numeric($code)) {
			http_response_code($code);
		}
				
		if(file_exists($fullPath)) {
			extract($vars);
			include $fullPath;
		}
	}
}
?>
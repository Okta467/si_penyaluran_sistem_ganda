<?php 
	function base_url($path = 'index.php') {
		echo "/si_penyaluran_sistem_ganda/" . $path;
	}

	function base_url_return($path = 'index.php') {
		return "/si_penyaluran_sistem_ganda/" . $path;
	}

    date_default_timezone_set("Asia/Bangkok");
	
	DEFINE("SITE_NAME", "SI Penyaluran Sistem Ganda");
	DEFINE("SITE_NAME_SHORT", "SINDA");
?>
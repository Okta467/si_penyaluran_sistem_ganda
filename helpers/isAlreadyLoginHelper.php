<?php

/**
 * Check and redirect user to its own page if already logged in
 * 
 * @param string $hak_akses $_SESSION (usually $_SESSION['hak_akses'])
 */
function isAlreadyLoggedIn($hak_akses): bool {
	// alihkan user ke halamannya masing-masing
	switch ($hak_akses) {
		case 'admin':
			header("location:admin");
			break;

		case 'guest':
			header("location:guest/index.php?go=dashboard");
			break;
			
		case 'pimpinan':
			header("location:pimpinan/index.php?go=dashboard");
			break;
		
		default:
			header("location:logout.php");
			break;
	}

    return true;
}

?>
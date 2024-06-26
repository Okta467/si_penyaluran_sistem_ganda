<?php
if (!function_exists('getHashedFileName')) {
    function getHashedFileName($originalFileName) {
        // Generate a hash using SHA-256
        $hashedName = hash('sha256', $originalFileName . microtime());
        // Preserve the file extension
        $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        return $hashedName . '.' . $extension;
    }
}
?>
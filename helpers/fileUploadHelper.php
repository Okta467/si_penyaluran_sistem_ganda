<?php

if (!function_exists('fileUpload')) {

    /**
     * PHP template for file uploading
     * @param string $fileToUpload file to upload. Example: $_FILES['filetoUpload']
     * @param string $targetDir target directory where file will be uploaded
     * @param int $maxFileSize max file size allowed in bytes (MB). Example: 1MB (1 * 1024 * 1024)
     * @param array<string $allowedTypes list of all allowed file types
     * @return array<mixed $uploadData contains boolean isUploaded and messages
     */
    function fileUpload($fileToUpload = null, $targetDir = '../assets/uploads/', $maxFileSize = 1 * 1024 * 1024, $allowedTypes = ['pdf']): array {

        // Helper function to hash string to SHA-256
        require_once 'getHashedFileNameHelper.php';


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($fileToUpload)) {
                $file       = $fileToUpload;
                $fileName   = getHashedFileName(basename($file['name']));
                $targetFile = $targetDir . $fileName;
                $fileType   = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                $fileSize   = $file['size'];
                $uploadData = [
                    'hashedFilename' => $fileName,
                    'isUploaded' => true,
                    'messages' => ''
                ];
                
                // Check if file type is allowed
                if (!in_array($fileType, $allowedTypes)) {
                    $uploadData['messages']   .= "Sorry, only " . implode(', ', $allowedTypes) . " files are allowed. <br>";
                    $uploadData['isUploaded'] = false;
                }

                // Check file size
                if ($fileSize > $maxFileSize) {
                    $uploadData['messages']   .=  "Sorry, your file is too large. Maximum size is " . $maxFileSize/1024/1024 . "MB <br>";
                    $uploadData['isUploaded'] = false;
                }

                // Check if $uploadData['isUploaded'] is set to 0 by an error
                if (!$uploadData['isUploaded']) {
                    $uploadData['messages'] .= "Sorry, your file was not uploaded.";
                } else {
                    // Check if directory exists, if not create it
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }

                    // Try to upload file
                    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                        $uploadData['messages'] .= "The file " . htmlspecialchars($fileName) . " has been uploaded.";
                    } else {
                        $uploadData['messages'] .= "Sorry, there was an error uploading your file.";
                    }
                }

                return $uploadData;
            } else {
                return [
                    'isUploaded' => false,
                    'messages'   => "No file was uploaded."
                ];
            }
        }
    }
}
?>

<?php
    include_once '../helpers/isAccessAllowedHelper.php';

    // cek apakah user yang mengakses adalah siswa?
    if (!isAccessAllowed('siswa')) {
        session_destroy();
        echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
        return;
    }

    require_once '../vendors/htmlpurifier/HTMLPurifier.auto.php';
    require_once '../helpers/fileUploadHelper.php';
    require_once '../helpers/getHashedFileNameHelper.php';
    include_once '../config/connection.php';

    // to sanitize user input
    $config   = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    
    $id_siswa      = $_SESSION['id_siswa'];
    $nama_keahlian = htmlspecialchars($purifier->purify($_POST['xnama_keahlian']));
    $file_keahlian = $_FILES['xfile_keahlian'];

    // Set upload configuration
    $target_dir    = '../assets/uploads/file_keahlian_siswa/';
    $max_file_size = 200 * 1024; // 200KB in bytes
    $allowed_types = ['pdf'];

    // Upload surat lamaran using the configuration
    $upload_file_keahlian = fileUpload($file_keahlian, $target_dir, $max_file_size, $allowed_types);
    $nama_berkas       = $upload_file_keahlian['hashedFilename'];
    $is_upload_success = $upload_file_keahlian['isUploaded'];
    $upload_messages   = $upload_file_keahlian['messages'];

    // Check is file uploaded?
    if (!$is_upload_success) {
        $_SESSION['msg'] = $upload_messages;
        echo "<meta http-equiv='refresh' content='0;keahlian_siswa.php?go=keahlian_siswa'>";
        return;
    }
    
    $stmt = mysqli_stmt_init($connection);

    mysqli_stmt_prepare($stmt, "INSERT INTO tbl_keahlian_siswa (id_siswa, nama_keahlian, file_keahlian) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'iss', $id_siswa, $nama_keahlian, $nama_berkas);

    $insert = mysqli_stmt_execute($stmt);

    !$insert
        ? $_SESSION['msg'] = 'other_error'
        : $_SESSION['msg'] = 'save_success';

    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    
    echo "<meta http-equiv='refresh' content='0;keahlian_siswa.php?go=keahlian_siswa'>";
?>
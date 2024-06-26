<?php
session_start();
include_once 'config/connection.php';

$id_pengguna = $_SESSION['id_pengguna'];
$last_login = date('Y-m-d H:i:s');

$stmt = mysqli_stmt_init($connection);

mysqli_stmt_prepare($stmt, "UPDATE tbl_pengguna SET last_login=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'si', $last_login, $id_pengguna);
mysqli_stmt_execute($stmt);

session_destroy();
header("location:index.php");
?>
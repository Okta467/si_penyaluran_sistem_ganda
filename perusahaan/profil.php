<?php
include '../helpers/isAccessAllowedHelper.php';

// cek apakah user yang mengakses adalah perusahaan?
if (!isAccessAllowed('perusahaan')) :
  session_destroy();
  echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
else :
  include_once '../config/connection.php';
?>


  <!DOCTYPE html>
  <html lang="en">

  <head>
    <?php include '_partials/head.php' ?>

    <meta name="description" content="Data Perusahaan" />
    <meta name="author" content="" />
    <title>Perusahaan - <?= SITE_NAME ?></title>
  </head>

  <body class="nav-fixed">
    <!--============================= TOPNAV =============================-->
    <?php include '_partials/topnav.php' ?>
    <!--//END TOPNAV -->
    <div id="layoutSidenav">
      <div id="layoutSidenav_nav">
        <!--============================= SIDEBAR =============================-->
        <?php include '_partials/sidebar.php' ?>
        <!--//END SIDEBAR -->
      </div>
      <div id="layoutSidenav_content">
        <main>
          <!-- Main page content-->
          <div class="container-xl px-4 mt-5">

            <!-- Custom page header alternative example-->
            <div class="d-flex justify-content-between align-items-sm-center flex-column flex-sm-row mb-4">
              <div class="me-4 mb-3 mb-sm-0">
                <h1 class="mb-0">Perusahaan</h1>
                <div class="small">
                  <span class="fw-500 text-primary"><?= date('D') ?></span>
                  &middot; <?= date('M d, Y') ?> &middot; <?= date('H:i') ?> WIB
                </div>
              </div>

              <!-- Date range picker example-->
              <div class="input-group input-group-joined border-0 shadow w-auto">
                <span class="input-group-text"><i data-feather="calendar"></i></span>
                <input class="form-control ps-0 pointer" id="litepickerRangePlugin" value="Tanggal: <?= date('d M Y') ?>" readonly />
              </div>

            </div>
            
            <!-- Main page content-->
            <div class="card card-header-actions mb-4 mt-5">
              <div class="card-header">
                <div>
                  <i data-feather="user" class="me-2 mt-1"></i>
                  Data Perusahaan
                </div>
              </div>
              <div class="card-body">
                <table id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama Perusahaan</th>
                      <th>Jenis</th>
                      <th>Alamat</th>
                      <th>Ubah profil</th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php
                    $no = 1;
                    $query_perusahaan = mysqli_query($connection, 
                      "SELECT
                        a.id AS id_perusahaan, a.nama_perusahaan, a.alamat_perusahaan,
                        b.id AS id_jenis_perusahaan, b.nama_jenis,
                        f.id AS id_pengguna, f.username, f.hak_akses
                      FROM tbl_perusahaan AS a
                      LEFT JOIN tbl_jenis_perusahaan AS b
                        ON b.id = a.id_jenis_perusahaan
                      LEFT JOIN tbl_pengguna AS f
                        ON f.id = a.id_pengguna
                      WHERE a.id = {$_SESSION['id_perusahaan']}");

                    while ($perusahaan = mysqli_fetch_assoc($query_perusahaan)):
                    ?>

                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($perusahaan['nama_perusahaan']) ?></td>
                        <td><?= $perusahaan['nama_jenis'] ?></td>
                        <td>
                          <div class="ellipsis toggle_tooltip" title="<?= htmlspecialchars($perusahaan['alamat_perusahaan']) ?>">
                            <?= htmlspecialchars($perusahaan['alamat_perusahaan']) ?>
                          </div>
                        </td>
                        <td>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_modal_ubah"
                            data-id_perusahaan="<?= $perusahaan['id_perusahaan'] ?>">
                            <i class="fa fa-pen-to-square"></i>
                          </button>
                        </td>
                      </tr>

                    <?php endwhile ?>

                  </tbody>
                </table>
              </div>
            </div>
            
          </div>
        </main>
        
        <!--============================= FOOTER =============================-->
        <?php include '_partials/footer.php' ?>
        <!--//END FOOTER -->

      </div>
    </div>
      
    <!--============================= MODAL INPUT PERUSAHAAN =============================-->
    <div class="modal fade" id="ModalInputPerusahaan" tabindex="-1" role="dialog" aria-labelledby="ModalInputPerusahaanTitle" aria-hidden="true" data-focus="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalInputPerusahaanTitle">Modal title</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
    
              <input type="hidden" name="xid_perusahaan" id="xid_perusahaan" required>
              <input type="hidden" name="xid_pengguna" id="xid_pengguna" required>
    
              <div class="mb-3">
                <label class="small mb-1" for="xnama_perusahaan">Nama Perusahaan <span class="text-danger fw-bold">*</span></label>
                <input class="form-control" id="xnama_perusahaan" type="text" name="xnama_perusahaan" placeholder="Enter nama perusahaan" required>
              </div>
    
              <div class="mb-3">
                <label class="small mb-1" for="xid_jenis_perusahaan">Jenis Perusahaan <span class="text-danger fw-bold">*</span></label>
                <select name="xid_jenis_perusahaan" class="form-control select2" id="xid_jenis_perusahaan" required>
                  <option value="">-- Pilih --</option>
                  <?php $query_jenis_perusahaan = mysqli_query($connection, "SELECT * FROM tbl_jenis_perusahaan ORDER BY nama_jenis ASC") ?>
                  <?php while ($jenis_perusahaan = mysqli_fetch_assoc($query_jenis_perusahaan)): ?>

                    <option value="<?= $jenis_perusahaan['id'] ?>"><?= $jenis_perusahaan['nama_jenis'] ?></option>

                  <?php endwhile ?>
                </select>
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xusername">Username <span class="text-danger fw-bold">*</span></label>
                <input class="form-control" id="xusername" type="text" name="xusername" placeholder="Enter username" autocomplete="username" required>
              </div>
    
              <div class="mb-3">
                <label class="small mb-1" for="xpassword">Password <span class="text-danger fw-bold">*</span></label>
                <div class="input-group input-group-joined mb-1">
                  <input class="form-control mb-1" id="xpassword" type="password" name="xpassword" placeholder="Enter password" autocomplete="new-password" required>
                  <button class="input-group-text" id="xpassword_toggle" type="button"><i class="fa-regular fa-eye"></i></button>
                </div>
                <small class="text-muted" id="xpassword_help"></small>
              </div>
    
              <div class="mb-3">
                <label class="small mb-1" for="xalamat_perusahaan">Alamat <span class="text-danger fw-bold">*</span></label>
                <input class="form-control" id="xalamat_perusahaan" type="text" name="xalamat_perusahaan" placeholder="Enter alamat" required>
              </div>
    
            </div>
            <div class="modal-footer">
              <button class="btn btn-light border" type="button" data-bs-dismiss="modal">Batal</button>
              <button class="btn btn-primary" type="submit" id="toggle_swal_submit">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!--/.modal-input-perusahaan -->
    
    <?php include '_partials/script.php' ?>
    <?php include '../helpers/sweetalert2_notify.php' ?>

    <!-- PAGE SCRIPT -->
    <script>
        let password = document.getElementById('xpassword');
        let passwordConfirm = document.getElementById('xpassword_confirm');
    
        let passwordToggle = document.getElementById('xpassword_toggle');
        let passwordConfirmToggle = document.getElementById('xpassword_confirm_toggle');
    
        let passwordHelp = document.getElementById('xpassword_help');
        let passwordConfirmHelp = document.getElementById('xpassword_confirm_help');
        
        passwordToggle.addEventListener('click', function() {
          initTogglePassword(password, passwordToggle);
        });
    </script>

    <script>
      $(document).ready(function() {

        
        $('.toggle_modal_ubah').on('click', function() {
          const id_perusahaan   = $(this).data('id_perusahaan');
          const nama_perusahaan = $(this).data('nama_perusahaan');
          
          $('#ModalInputPerusahaan .modal-title').html(`<i data-feather="edit" class="me-2 mt-1"></i>Ubah Perusahaan`);
          $('#ModalInputPerusahaan form').attr({action: 'profil_ubah.php', method: 'post'});
          $('#ModalInputPerusahaan #xpassword').attr('required', false);
          $('#ModalInputPerusahaan #xpassword_help').html('Kosongkan jika tidak ingin ubah password.');
        
          $.ajax({
            url: 'get_perusahaan.php',
            method: 'POST',
            dataType: 'JSON',
            data: {
              'id_perusahaan': id_perusahaan
            },
            success: function(data) {
              console.log(data)
              $('#ModalInputPerusahaan #xid_perusahaan').val(data[0].id_perusahaan);
              $('#ModalInputPerusahaan #xid_pengguna').val(data[0].id_pengguna);
              $('#ModalInputPerusahaan #xnama_perusahaan').val(data[0].nama_perusahaan);
              $('#ModalInputPerusahaan #xid_jenis_perusahaan').val(data[0].id_jenis_perusahaan).trigger('change');
              $('#ModalInputPerusahaan #xusername').val(data[0].username);
              $('#ModalInputPerusahaan #xalamat_perusahaan').val(data[0].alamat_perusahaan);
              
              // Re-init all feather icons
              feather.replace();
              
              $('#ModalInputPerusahaan').modal('show');
            },
            error: function(request, status, error) {
              // console.log("ajax call went wrong:" + request.responseText);
              console.log("ajax call went wrong:" + error);
            }
          })
        });
        

        const formSubmitBtn = $('#toggle_swal_submit');
        const eventName = 'click';
        
        toggleSwalSubmit(formSubmitBtn, eventName);
        
      });
    </script>

  </body>

  </html>

<?php endif ?>
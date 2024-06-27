<?php
include '../helpers/isAccessAllowedHelper.php';

// cek apakah user yang mengakses adalah admin?
if (!isAccessAllowed('admin')) :
  session_destroy();
  echo "<meta http-equiv='refresh' content='0;" . base_url_return('index.php?msg=other_error') . "'>";
else :
  include_once '../config/connection.php';
?>


  <!DOCTYPE html>
  <html lang="en">

  <head>
    <?php include '_partials/head.php' ?>

    <meta name="description" content="Data Pengguna" />
    <meta name="author" content="" />
    <title>Pengguna - <?= SITE_NAME ?></title>
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
                <h1 class="mb-0">Pengguna</h1>
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
                  <i data-feather="users" class="me-2 mt-1"></i>
                  Data Pengguna
                </div>
                <button class="btn btn-sm btn-primary toggle_modal_tambah" type="button"><i data-feather="user-plus" class="me-2"></i>Tambah Pengguna</button>
              </div>
              <div class="card-body">
                <table id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Pengguna</th>
                      <th>Username</th>
                      <th>Hak Akses</th>
                      <th>Jabatan</th>
                      <th>Tanggal Bergabung</th>
                      <th>Login Terakhir</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
  
                    <?php
                    $no = 1;
                    $query_pengguna = mysqli_query($connection,
                      "SELECT 
                        a.id AS id_pengguna, a.username, a.hak_akses, a.created_at, a.last_login,
                        b.id AS id_guru, b.nip AS nip_guru, b.nama_guru, b.jk AS jk_guru, 
                        c.id AS id_siswa, c.nama_siswa, c.jk,
                        d.id AS id_perusahaan, d.nama_perusahaan, d.alamat_perusahaan,
                        e.id AS id_jabatan, e.nama_jabatan AS nama_jabatan_guru,
                        f.id AS id_jenis_perusahaan, f.nama_jenis
                      FROM tbl_pengguna AS a
                      LEFT JOIN tbl_guru AS b
                        ON a.id = b.id_pengguna
                      LEFT JOIN tbl_siswa AS c
                        ON a.id = c.id_pengguna
                      LEFT JOIN tbl_perusahaan AS d
                        ON a.id = d.id_pengguna
                      LEFT JOIN tbl_jabatan AS e
                        ON e.id = b.id_jabatan
                      LEFT JOIN tbl_jenis_perusahaan AS f
                        ON f.id = d.id_jenis_perusahaan
                      ORDER BY a.id DESC");
  
                    while ($pengguna = mysqli_fetch_assoc($query_pengguna)) :

                      $formatted_hak_akses = ucwords(str_replace('_', ' ', $pengguna['hak_akses']));

                      $tanggal_bergabung = isset($pengguna['created_at'])
                        ? date('d M Y', strtotime($pengguna['created_at']))
                        : '<small class="text-muted">Tidak ada</small>';
                      
                      $last_login = isset($pengguna['last_login'])
                        ? date('d M Y H:i:s', strtotime($pengguna['last_login']))
                        : '<small class="text-muted">Tidak ada</small>';
                    ?>
  
                      <tr>
                        <td><?= $no++ ?></td>
                        <td>
                          <img src="<?= base_url('assets/img/illustrations/profiles/profile-' . rand(1, 6) . '.png') ?>" alt="Image User" class="avatar me-2">
                          <?= $pengguna['hak_akses'] === 'admin' ? 'Admin' : '' ?>
                          <?= $pengguna['hak_akses'] === 'siswa' ? $pengguna['nama_siswa'] : '' ?>
                          <?= in_array($pengguna['hak_akses'], ['guru', 'kepala_sekolah']) ? $pengguna['nama_guru'] : '' ?>
                          <?= $pengguna['hak_akses'] === 'perusahaan' ? $pengguna['nama_perusahaan'] : '' ?>
                        </td>
                        <td><?= $pengguna['username'] ?></td>
                        <td>
                          
                          <?php if ($pengguna['hak_akses'] === 'admin') : ?>
                            
                            <span class="badge bg-red-soft text-red"><?= $formatted_hak_akses ?></span>
                            
                          <?php elseif ($pengguna['hak_akses'] === 'siswa'): ?>
                            
                            <span class="badge bg-blue-soft text-blue"><?= $formatted_hak_akses ?></span>
                            
                          <?php elseif (in_array($pengguna['hak_akses'], ['guru', 'kepala_sekolah'])) : ?>
                            
                            <span class="badge bg-purple-soft text-purple"><?= $formatted_hak_akses ?></span>
  
                          <?php elseif ($pengguna['hak_akses'] === 'perusahaan'): ?>
                            
                            <span class="badge bg-green-soft text-green"><?= $formatted_hak_akses ?></span>
                            
                          <?php endif ?>
                          
                        </td>
                        <td>
                          <div class="ellipsis">
  
                            <?php if (in_array($pengguna['hak_akses'], ['admin', 'siswa', 'perusahaan'])): ?>
    
                              <small class="text-muted">Tidak ada</small>
    
                            <?php else: ?>
    
                              <span class="toggle_tooltip" title="<?= $pengguna['nama_jabatan_guru'] ?? $pengguna['nama_jabatan_guru'] ?>">
                                <?= $pengguna['nama_jabatan_guru'] ?? $pengguna['nama_jabatan_guru'] ?>
                              </span>
                              
                            <?php endif ?>
                            
                          </div>
                        </td>
                        <td><?= $tanggal_bergabung ?></td>
                        <td><?= $last_login ?></td>
                        <td>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_modal_ubah"
                            data-id_pengguna="<?= $pengguna['id_pengguna'] ?>"
                            data-id_siswa="<?= $pengguna['id_siswa'] ?>"
                            data-id_guru="<?= $pengguna['id_guru'] ?>"
                            data-id_perusahaan="<?= $pengguna['id_perusahaan'] ?>"
                            data-username="<?= $pengguna['username'] ?>"
                            data-hak_akses="<?= $pengguna['hak_akses'] ?>">
                            <i class="fa fa-pen-to-square"></i>
                          </button>
                          <button class="btn btn-datatable btn-icon btn-transparent-dark me-2 toggle_swal_hapus"
                            data-id_pengguna="<?= $pengguna['id_pengguna'] ?>"
                            data-username="<?= $pengguna['username'] ?>"
                            data-nama_siswa="<?= $pengguna['nama_siswa'] ?>"
                            data-nama_guru="<?= $pengguna['nama_guru'] ?>"
                            data-nama_perusahaan="<?= $pengguna['nama_perusahaan'] ?>"
                            data-hak_akses="<?= $pengguna['hak_akses'] ?>">
                            <i class="fa fa-trash-can"></i>
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
    
    <!--============================= MODAL INPUT JURUSAN =============================-->
    <div class="modal fade" id="ModalInputPengguna" tabindex="-1" role="dialog" aria-labelledby="ModalInputPenggunaTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalInputPenggunaTitle">Modal title</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            <div class="modal-body">
              
              <input type="hidden" class="xid_pengguna" id="xid_pengguna" name="xid_pengguna">
              
              <div class="mb-3">
                <label class="small mb-1" for="xhak_akses">Hak Akses <span class="text-danger fw-bold">*</span></label>
                <select name="xhak_akses" class="form-control select2 xhak_akses" id="xhak_akses" required>
                  <option value="">-- Pilih --</option>
                  <option value="admin">admin</option>
                  <option value="siswa">Siswa</option>
                  <option value="guru">Guru</option>
                  <option value="kepala_sekolah">Kepala Sekolah</option>
                  <option value="perusahaan">Perusahaan</option>
                </select>
              </div>
              
              <div class="mb-3 xid_guru">
                <label class="small mb-1" for="xid_guru">Guru <span class="text-danger fw-bold">*</span></label>
                <select name="xid_guru" class="form-control select2 xid_guru" id="xid_guru" required></select>
                <small class="text-muted xid_guru_help"></small>
              </div>
              
              <div class="mb-3 xid_siswa">
                <label class="small mb-1" for="xid_siswa">Siswa <span class="text-danger fw-bold">*</span></label>
                <select name="xid_siswa" class="form-control select2 xid_siswa" id="xid_siswa" required></select>
                <small class="text-muted xid_siswa_help"></small>
              </div>
              
              <div class="mb-3 xid_perusahaan">
                <label class="small mb-1" for="xid_perusahaan">Perusahaan <span class="text-danger fw-bold">*</span></label>
                <select name="xid_perusahaan" class="form-control select2 xid_perusahaan" id="xid_perusahaan" required></select>
                <small class="text-muted xid_perusahaan_help"></small>
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xusername">Username <span class="text-danger fw-bold">*</span></label>
                <input class="form-control mb-1 xusername" id="xusername" type="username" name="xusername" placeholder="Enter username" required disabled>
                <small class="text-muted">Hanya berupa huruf dan angka.</small>
              </div>
              
              <div class="mb-3">
                <label class="small mb-1" for="xpassword">Password <span class="text-danger fw-bold">*</span></label>
                <div class="input-group input-group-joined mb-1">
                  <input class="form-control xpassword" id="xpassword" type="password" name="xpassword" placeholder="Enter password" autocomplete="new-password" required disabled>
                  <button class="input-group-text btn xpassword_toggle disabled" id="xpassword_toggle" type="button"><i class="fa-regular fa-eye"></i></button>
                </div>
                <small class="text-danger xpassword_help">Pilih hak akses terlebih dahulu!</small>
                <small class="text-muted xpassword_help2">Kosongkan jika tidak ingin mengubah password.</small>
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
    <!--/.modal-input-jurusan -->
    
    <?php include '_partials/script.php' ?>
    <?php include '../helpers/sweetalert2_notify.php' ?>
    
    <!-- PAGE SCRIPT -->
    <script>
      $(document).ready(function() {


        const toggleSelectRequiredAndDisplay = function(showSiswa = false, showGuru = false, showPerusahaan = false) {

          if (!showSiswa) {
            // Hide and set required to false to select siswa
            $('#ModalInputPengguna div.xid_siswa').hide();
            $('#ModalInputPengguna select.xid_siswa').prop('required', false);
          } else {
            // Hide and set required to false to select siswa
            $('#ModalInputPengguna div.xid_siswa').show();
            $('#ModalInputPengguna select.xid_siswa').prop('required', true);
          }

          if (!showGuru) {
            // Hide and set required to false to select guru
            $('#ModalInputPengguna div.xid_guru').hide();
            $('#ModalInputPengguna select.xid_guru').prop('required', false);
          } else {
            // Hide and set required to false to select guru
            $('#ModalInputPengguna div.xid_guru').show();
            $('#ModalInputPengguna select.xid_guru').prop('required', true);
          }

          if (!showPerusahaan) {
            // Hide and set required to false to select perusahaan
            $('#ModalInputPengguna div.xid_perusahaan').hide();
            $('#ModalInputPengguna select.xid_perusahaan').prop('required', false);
          } else {
            // Hide and set required to false to select perusahaan
            $('#ModalInputPengguna div.xid_perusahaan').show();
            $('#ModalInputPengguna select.xid_perusahaan').prop('required', true);
          }

        }


        const toggleUsernamePassword = function(disableUsername = true, disablePassword = true, usernameVal = null, passwordRequired = true) {
          if (!disableUsername) {
            $('#ModalInputPengguna .xusername_help').hide();
            $('#ModalInputPengguna .xusername').attr('disabled', false);
          } else {
            $('#ModalInputPengguna .xusername_help').show();
            $('#ModalInputPengguna .xusername').attr('disabled', true);
          }

          if (!disablePassword) {
            $('#ModalInputPengguna .xpassword_toggle').removeClass('btn disabled');
            $('#ModalInputPengguna .xpassword_help').hide();
            $('#ModalInputPengguna .xpassword').attr('disabled', false);
          } else {
            $('#ModalInputPengguna .xpassword_toggle').addClass('btn disabled');
            $('#ModalInputPengguna .xpassword_help').show();
            $('#ModalInputPengguna .xpassword').attr('disabled', true);
          }

          if (!passwordRequired) {
            console.log('a');
            $('#ModalInputPengguna .xpassword').prop('required', false);
          } else {
            console.log('b');
            $('#ModalInputPengguna .xpassword').prop('required', true);
          }

          if (usernameVal) {
            $('#ModalInputPengguna .xusername').val(usernameVal)
          }
        }


        let showSiswa = false;
        let showGuru = false;
        let showPerusahaan = false;
        toggleSelectRequiredAndDisplay(showSiswa, showGuru, showPerusahaan);
      
        
        // Define hak_akses function for change handler
        // so you can use this for `on` and `off` event
        const handleHakAksesChange = function(tipe_pengguna = 'with_no_user', id_siswa = null, id_guru = null, id_perusahaan = null) {
          return function(e) {
            const hak_akses = $('#xhak_akses').val();
          
            if (!hak_akses) {
              let showSiswa = false;
              let showGuru = false;
              let showPerusahaan = false;
              toggleSelectRequiredAndDisplay(showSiswa, showGuru, showPerusahaan);

              let disableUsername = true;
              let disablePassword = true;
              let usernameVal = '';
              toggleUsernamePassword(disableUsername, disablePassword, usernameVal);

            } else {
              
              let disableUsername = false;
              let disablePassword = false;
              let usernameVal = '';
              toggleUsernamePassword(disableUsername, disablePassword, usernameVal);
            }
          
            if (hak_akses.toLowerCase() === 'admin') {
              let showSiswa = false;
              let showGuru = false;
              let showPerusahaan = false;
              toggleSelectRequiredAndDisplay(showSiswa, showGuru, showPerusahaan);
              return;
            }
          
            
            if (['guru', 'kepala_sekolah'].includes(hak_akses.toLowerCase())) {
              let url_ajax = tipe_pengguna === 'with_no_user'
                ? 'get_guru_with_no_user.php'
                : 'get_guru.php';
            
              $.ajax({
                url: url_ajax,
                method: 'POST',
                dataType: 'JSON',
                data: {
                  'id_guru': id_guru
                },
                success: function(data) {
                  let showSiswa = false;
                  let showGuru = true;
                  let showPerusahaan = false;
                  toggleSelectRequiredAndDisplay(showSiswa, showGuru, showPerusahaan);
            
                  // Transform the data to the format that Select2 expects
                  const transformedData = data.map(item => ({
                    id: item.id_guru,
                    text: item.nama_guru
                  }));
                  
                  const guruSelect = $('select.xid_guru');
                  
                  guruSelect.html(null);
                  
                  initSelect2(guruSelect, {
                    data: transformedData,
                    width: '100%',
                    dropdownParent: ".modal-content .modal-body"
                  })

                  guruSelect.trigger('change');
                },
                error: function(request, status, error) {
                  // console.log("ajax call went wrong:" + request.responseText);
                  console.log("ajax call went wrong:" + error);
                }
              });
          
            }
          
            
            if (hak_akses.toLowerCase() === 'siswa') {
              let url_ajax = tipe_pengguna === 'with_no_user'
                ? 'get_siswa_with_no_user.php'
                : 'get_siswa.php';
            
              $.ajax({
                url: url_ajax,
                method: 'POST',
                dataType: 'JSON',
                data: {
                  'id_siswa': id_siswa
                },
                success: function(data) {
                  let showSiswa = true;
                  let showGuru = false;
                  let showPerusahaan = false;
                  toggleSelectRequiredAndDisplay(showSiswa, showGuru, showPerusahaan);
                  
                  let disableUsername = false;
                  let disablePassword = false;
                  let usernameVal = '';
                  toggleUsernamePassword(disableUsername, disablePassword, usernameVal);
            
                  // Transform the data to the format that Select2 expects
                  const transformedData = data.map(item => ({
                    id: item.id_siswa,
                    text: item.nama_siswa
                  }));
                  
                  const siswaSelect = $('select.xid_siswa');
                  
                  siswaSelect.html(null);
                  
                  initSelect2(siswaSelect, {
                    data: transformedData,
                    width: '100%',
                    dropdownParent: ".modal-content .modal-body"
                  })
                },
                error: function(request, status, error) {
                  // console.log("ajax call went wrong:" + request.responseText);
                  console.log("ajax call went wrong:" + error);
                }
              });
          
            }
          
            
            if (hak_akses.toLowerCase() === 'perusahaan') {
              let url_ajax = tipe_pengguna === 'with_no_user'
                ? 'get_perusahaan_with_no_user.php'
                : 'get_perusahaan.php';
            
              $.ajax({
                url: url_ajax,
                method: 'POST',
                dataType: 'JSON',
                data: {
                  'id_perusahaan': id_perusahaan
                },
                success: function(data) {
                  let showGuru = false;
                  let showSiswa = false;
                  let showPerusahaan = true;
                  toggleSelectRequiredAndDisplay(showSiswa, showGuru, showPerusahaan);
                  
                  let disableUsername = false;
                  let disablePassword = false;
                  let usernameVal = '';
                  toggleUsernamePassword(disableUsername, disablePassword, usernameVal);
            
                  // Transform the data to the format that Select2 expects
                  const transformedData = data.map(item => ({
                    id: item.id_perusahaan,
                    text: item.nama_perusahaan
                  }));
                  
                  const perusahaanSelect = $('select.xid_perusahaan');
                  
                  perusahaanSelect.html(null);
                  
                  initSelect2(perusahaanSelect, {
                    data: transformedData,
                    width: '100%',
                    dropdownParent: ".modal-content .modal-body"
                  })
                },
                error: function(request, status, error) {
                  // console.log("ajax call went wrong:" + request.responseText);
                  console.log("ajax call went wrong:" + error);
                }
              });
          
            }
          }
        };
      
        
        $('.toggle_modal_tambah').on('click', function() {
          $('#ModalInputPengguna .modal-title').html(`<i data-feather="user-plus" class="mr-2"></i>Tambah Pengguna`);
          $('#ModalInputPengguna form').attr({action: 'pengguna_tambah.php', method: 'post'});

          $('#ModalInputPengguna .xid_siswa_help').html('Nama siswa yang muncul yaitu yang tidak memiliki user.');
          $('#ModalInputPengguna .xid_guru_help').html('Nama guru yang muncul yaitu yang tidak memiliki user.');
          $('#ModalInputPengguna .xid_perusahaan_help').html('Nama perusahaan yang muncul yaitu yang tidak memiliki user.');
          $('#ModalInputPengguna .xpassword').prop('required', true);
          $('#ModalInputPengguna .xpassword_help2').hide();
          $('#ModalInputPengguna select.xhak_akses').prop('disabled', false)
        
          // Detach (off) hak akses change event to avoid error and safely repopulate its select option
          const hakAksesSelect = $('#xhak_akses');
          hakAksesSelect.off('change');
          hakAksesSelect.empty();
          
          // Re-Initialize default select2 options (because in toggle_modal_ubah it's changed)
          const data = [
            {id: '', text: '-- Pilih --'},
            {id: 'admin', text: 'Admin'},
            {id: 'siswa', text: 'Siswa'},
            {id: 'guru', text: 'Guru'},
            {id: 'kepala_sekolah', text: 'Kepala Sekolah'},
            {id: 'perusahaan', text: 'Perusahaan'},
          ];
          
          // Append options to the select element
          data.forEach(function(item) {
            const option = new Option(item.text, item.id, item.selected, item.selected);
            hakAksesSelect.append(option);
          });
  
          // Initialize Select2
          initSelect2(hakAksesSelect, {
            width: '100%',
            dropdownParent: ".modal-content .modal-body"
          });
          
          $('#xhak_akses').on('change', handleHakAksesChange());
        
          $('#ModalInputPengguna').modal('show');
        });
      
        
        $('.toggle_modal_ubah').on('click', function() {
          const data = $(this).data();
        
          $('#ModalInputPengguna .modal-title').html(`<i data-feather="user-check" class="mr-2"></i>Tambah Pengguna`);
          $('#ModalInputPengguna form').attr({action: 'pengguna_ubah.php', method: 'post'});
          
          // Detach (off) the change handler for repopulating options
          $('#xhak_akses').off('change');
        
          const hakAksesSelect = $('#xhak_akses');
          hakAksesSelect.empty();
        
          if (data.hak_akses === 'admin') {
            data_hak_akses = [
              {id: 'admin', text: 'Admin', selected: true}
            ];
          }
          else if (data.hak_akses === 'siswa') {
            data_hak_akses = [
              {id: 'siswa', text: 'Siswa'},
            ];
          }
          else if (data.hak_akses === 'guru') {
            data_hak_akses = [
              {id: 'guru', text: 'Guru'},
            ];
          }
          else if (data.hak_akses === 'kepala_sekolah') {
            data_hak_akses = [
              {id: 'kepala_sekolah', text: 'Kepala Sekolah'},
            ];
          }
          else if (data.hak_akses === 'perusahaan') {
            data_hak_akses = [
              {id: 'perusahaan', text: 'Perusahaan'},
            ];
          }
          
          // Append options to the select element
          data_hak_akses.forEach(function(item) {
            const option = new Option(item.text, item.id, item.selected, item.selected);
            hakAksesSelect.append(option);
          });
          
          // Initialize Select2
          initSelect2(hakAksesSelect, {
            width: '100%',
            dropdownParent: ".modal-content .modal-body"
          });
        
          $('#ModalInputPengguna select.xhak_akses').val(data.hak_akses).trigger('change');
          $('#ModalInputPengguna .xid_siswa_help').html('Nama siswa hanya dapat diubah pada halaman Guru.');
          $('#ModalInputPengguna .xid_guru_help').html('Nama guru hanya dapat diubah pada halaman Guru.');
          $('#ModalInputPengguna .xid_perusahaan_help').html('Nama perusahaan hanya dapat diubah pada halaman Guru.');
          $('#ModalInputPengguna .xid_pengguna').val(data.id_pengguna);

          let disableUsername = false;
          let disablePassword = false;
          let usernameVal = data.username;
          let passwordRequired = false;
          toggleUsernamePassword(disableUsername, disablePassword, usernameVal, false);
          
          $('#xhak_akses').on('change', handleHakAksesChange('with_user', data.id_siswa, data.id_guru, data.id_perusahaan));
        
          $('#ModalInputPengguna').modal('show');
        });


        $('#xid_guru').on('change', function() {
          const id_guru = $(this).val();

          if (id_guru) {
            $.ajax({
              url: 'get_guru.php',
              type: 'POST',
              data: {
                id_guru: id_guru
              },
              dataType: 'JSON',
              success: function(data) {
                $('#ModalInputPengguna #xusername').val(data[0].nip);
              },
              error: function(request, status, error) {
                // console.log("ajax call went wrong:" + request.responseText);
                console.log("ajax call went wrong:" + error);
              }
            })
          }

        });
        

        $('#datatablesSimple').on('click', '.toggle_swal_hapus', function() {
          const data = $(this).data();
          
          if (data.hak_akses === 'admin') nama_pengguna = data.username;
          else if (data.hak_akses === 'siswa') nama_pengguna = `${data.nama_siswa} (${data.username})`;
          else if (['kepala_sekolah', 'guru'].includes(data.hak_akses)) nama_pengguna = `${data.nama_guru} (${data.username})`;
          else if (data.hak_akses === 'perusahaan') nama_pengguna = `${data.nama_perusahaan} (${data.username})`;
          
          Swal.fire({
            title: "Konfirmasi Tindakan?",
            html: `Hapus data pengguna: <strong>${nama_pengguna}?</strong>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, konfirmasi!"
          }).then((result) => {
            if (result.isConfirmed) {
              Swal.fire({
                title: "Tindakan Dikonfirmasi!",
                text: "Halaman akan di-reload untuk memproses.",
                icon: "success",
                timer: 3000
              }).then(() => {
                window.location = `pengguna_hapus.php?xid_pengguna=${data.id_pengguna}`;
              });
            }
          });
        });
        

        const formSubmitBtn = $('#toggle_swal_submit');
        const eventName = 'click';
        
        toggleSwalSubmit(formSubmitBtn, eventName);
        
      });
    </script>

  </body>

  </html>

<?php endif ?>
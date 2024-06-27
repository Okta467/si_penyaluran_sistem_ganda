<?= $go = $_GET['go'] ?? null ?>

<script src="<?= base_url('assets/js/bootstrap/bootstrap.bundle.min.js') ?>" crossorigin="anonymous"></script>
<script src="<?= base_url('assets/js/scripts.js') ?>"></script>
<script src="<?= base_url('assets/js/datatables/datatables-simple.min.js') ?>" crossorigin="anonymous"></script>

<?php if ($go !== 'pengumuman'): ?>
    
<script src="<?= base_url('assets/js/datatables/datatables-simple-demo.js') ?>"></script>

<?php endif ?>

<script src="<?= base_url("vendors/sweetalert2/dist/sweetalert2.all.min.js") ?>"></script>

<script src="<?= base_url('vendors/jquery/jquery-3.7.1.min.js') ?>"></script>

<script src="<?= base_url('vendors/select2/js/select2.min.js') ?>"></script>

<script src="<?= base_url('vendors/dropify/js/dropify.min.js') ?>"></script>

<script src="<?= base_url('vendors/moment/moment.min.js') ?>"></script>

<script src="<?= base_url('assets/js/MY_scripts.js') ?>"></script>
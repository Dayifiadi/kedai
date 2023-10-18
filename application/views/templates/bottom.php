<!--   Core JS Files   -->
<script src="<?= base_url('') ?>assets/js/core/jquery.3.2.1.min.js"></script>
<script src="<?= base_url('') ?>assets/js/core/popper.min.js"></script>
<script src="<?= base_url('') ?>assets/js/core/bootstrap.min.js"></script>

<!-- jQuery UI -->
<script src="<?= base_url('') ?>assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<script src="<?= base_url('') ?>assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="<?= base_url('') ?>assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Moment JS -->
<script src="<?= base_url('') ?>assets/js/moment.min.js"></script>

<!-- Chart JS -->
<script src="<?= base_url('') ?>assets/js/plugin/chart.js/chart.min.js"></script>

<!-- jQuery Sparkline -->
<script src="<?= base_url('') ?>assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Chart Circle -->
<script src="<?= base_url('') ?>assets/js/plugin/chart-circle/circles.min.js"></script>

<!-- Datatables -->
<script src="<?= base_url('') ?>assets/js/plugin/datatables/datatables.min.js"></script>

<!-- Bootstrap Notify -->
<script src="<?= base_url('') ?>assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- Bootstrap Toggle -->
<!-- <script src="<?= base_url('') ?>assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script> -->

<!-- jQuery Vector Maps -->
<script src="<?= base_url('') ?>assets/js/plugin/jqvmap/jquery.vmap.min.js"></script>
<script src="<?= base_url('') ?>assets/js/plugin/jqvmap/maps/jquery.vmap.world.js"></script>

<!-- Google Maps Plugin -->
<script src="<?= base_url('') ?>assets/js/plugin/gmaps/gmaps.js"></script>

<!-- Dropzone -->
<script src="<?= base_url('') ?>assets/js/dropzone.min.js"></script>

<!-- Fullcalendar -->
<script src="<?= base_url('') ?>assets/js/fullcalendar.min.js"></script>

<!-- DateTimePicker -->
<script src="<?= base_url('') ?>assets/js/plugin/datepicker/bootstrap-datetimepicker.min.js"></script>

<!-- Bootstrap Tagsinput -->
<script src="<?= base_url('') ?>assets/js/plugin/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>

<!-- Bootstrap Wizard -->
<script src="<?= base_url('') ?>assets/js/bootstrapwizard.js"></script>

<!-- jQuery Validation -->
<script src="<?= base_url('') ?>assets/js/jquery.validate.min.js"></script>

<!-- Summernote -->
<script src="<?= base_url('') ?>assets/js/summernote-bs4.min.js"></script>

<!-- Select2 -->
<script src="<?= base_url('') ?>assets/js/plugin/select2/select2.full.min.js"></script>

<!-- Sweet Alert -->
<script src="<?= base_url('') ?>assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<!-- Atlantis JS -->
<script src="<?= base_url('') ?>assets/js/atlantis2.min.js"></script>

<!-- Atlantis DEMO methods, don't include it in your project! -->
<!-- <script src="<?= base_url('') ?>assets/js/demo.js"></script> -->


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script>
    $(function() {
        $('select').selectpicker();
    });
</script>
<script>
    $(document).ready(function() {
        <?php if ($this->session->flashdata('success')) { ?>
            //Notify
            $.notify({
                icon: 'flaticon-alarm-1',
                title: 'Berhasil',
                message: '<?= $this->session->flashdata('success') ?>',
            }, {
                type: 'success',
                placement: {
                    from: "top",
                    align: "right"
                },
                time: 1000,
            });
        <?php } else if ($this->session->flashdata('error')) { ?>
            //Notify
            $.notify({
                icon: 'flaticon-alarm-1',
                title: 'Gagal',
                message: '<?= $this->session->flashdata('error') ?>'
            }, {
                type: 'error',
                placement: {
                    from: "top",
                    align: "right"
                },
                time: 1000,
            });
        <?php } else if ($this->session->flashdata('success-delete')) { ?>
            //Notify
            $.notify({
                icon: 'flaticon-alarm-1',
                title: 'Berhasil',
                message: '<?= $this->session->flashdata('success-delete') ?>',
            }, {
                type: 'success',
                placement: {
                    from: "top",
                    align: "right"
                },
                time: 1000,
            });
        <?php } ?>
    });
</script>
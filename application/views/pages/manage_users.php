<div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
            <div>
                <h2 class="text-white pb-2 fw-bold"><?= $page_title ?></h2>
            </div>
            <div class="ml-md-auto py-2 py-md-0">
                <a href="#" class="btn btn-warning btn-round" data-toggle="modal" data-target="#exampleModal">
                    <span class="btn-label">
                        <i class="fa fa-plus"></i>
                    </span> Pengguna
                </a>
            </div>
        </div>
    </div>
</div>
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pengguna</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="font-size: 12px">Nomor</th>
                                    <th class="text-center" style="font-size: 12px">Nama</th>
                                    <th class="text-center" style="font-size: 12px">Tipe</th>
                                    <th class="text-center" style="font-size: 12px">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end class row -->
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('manage/users_add') ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama <small style="color:red">*</small></label>
                        <input type="text" name="users_name" class="form-control" placeholder="" required>
                    </div>
                    <div class="form-group">
                        <label>Password <small style="color:red">*</small></label>
                        <div class="input-icon">
                            <input type="password" class="form-control" name="users_password" id="users_password">
                            <span class="input-icon-addon show-password">
                                <i class="icon-eye"></i>
                            </span>
                        </div>
                        <small id="result" class="form-text text-muted"></small>
                    </div>
                    <div class="form-group">
                        <label>No Hp <small style="color:red">*</small></label>
                        <input type="text" name="users_number" class="form-control" placeholder="" onkeypress="return hanyaAngka(event)" required>
                    </div>

                    <div class="form-group">
                        <label>Tipe Pengguna <small style="color:red">*</small></label>
                        <select class="form-control selectpicker" name="users_type" data-live-search="true" data-size="10" required>
                            <option value="KASIR">KASIR</option>
                            <option value="DAPUR">DAPUR</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var table;
    $(document).ready(function() {
        $('#basic-datatables').DataTable({});



        //datatables
        table = $('#users').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('manage/getdatatables_users') ?>",
                "type": "POST",
                "data": function(data) {
                    // data.region = $('[name="region"]').val();
                }
            },
            "columnDefs": [{
                    "targets": [0, 1, 2, 3],
                    "orderable": false
                },
                {
                    "targets": [0, 1, 2, 3],
                    "className": 'text-center'
                }
            ]
        });
    });
    $(document).ready(function() {
        $('#users_password').keyup(function() {
            $('#result').html(checkStrength($('#users_password').val()));
        })

        function checkStrength(password) {
            var strength = 0;

            if (!password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
                $('#ubah').prop('disabled', true);
                return 'Kata sandi harus memiliki minimal 1 huruf kapital';
            }

            if (!password.match(/([0-9])/)) {
                $('#ubah').prop('disabled', true);
                return 'Kata sandi harus memiliki minimal 1 angka';
            }

            if (!password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) {
                $('#ubah').prop('disabled', true);
                return 'Kata sandi harus memiliki minimal 1 simbol';
            }

            if (password.length < 8) {
                $('#ubah').prop('disabled', true);
                return 'Kata sandi harus memiliki minimal 8 karakter';
            }

            if (password.length >= 8) {
                if ($("#newpassword").val() == $("#confirmpassword").val()) {
                    $('#ubah').prop('disabled', false);
                }
                return '';
            }
        }
    });

    function hanyaAngka(event) {
        var angka = (event.which) ? event.which : event.keyCode
        if (angka != 46 && angka > 31 && (angka < 48 || angka > 57))
            return false;
        return true;
    }
</script>
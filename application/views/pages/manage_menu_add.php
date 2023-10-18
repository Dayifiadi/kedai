<div class="page-inner">
    <form action="<?= base_url('manage/menu/add') ?>" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Tambah Menu</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>SKU <small style="color:red">*</small></label>
                                <div class="input-icon">
                                    <input type="text" class="form-control" name="menu_sku" required>
                                    <span class="input-icon-addon" onclick="get_sku()">
                                        <i class="fas fa-circle-notch"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Nama Menu<small style="color:red">*</small></label>
                                <input type="text" name="menu_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Harga Modal<small style="color:red">*</small></label>
                                <input type="text" name="menu_basic_price" id="menu_basic_price" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Harga Jual<small style="color:red">*</small></label>
                                <input type="text" name="menu_selling_price" id="menu_selling_price" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-file input-file-image">
                                    <img class="img-upload-preview" width="150" src="<?= base_url('assets/img/fileupload.png') ?>" alt="preview">
                                    <input type="file" class="form-control form-control-file" id="menu_image" name="menu_image" accept="image/*" required="">
                                    <label for="menu_image" class=" label-input-file btn btn-black btn-round btn-block">
                                        <span class="btn-label">
                                            <i class="fa fa-file-image"></i>
                                        </span>
                                        Pilih File <small style="color:red">*</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Deskripsi <small style="color:red">*</small></label>
                                <textarea class="form-control" name="menu_description" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div id="dynamic_field">
                            <div class="form-group col-md-12">
                                <label>Nama Varian 1</label>
                                <input type="text" name="menu_variant[]" class="form-control">
                            </div>
                        </div>
                        <input type="hidden" name="varian_id[]" value="1">
                        <input type="hidden" name="idList" value="1">
                        <button type="button" name="add" id="add" onclick="tambahID()" class="col-md-12 btn btn-primary mt-3 btn-sm"><i class="fas fa-plus-circle"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>



<script>
    $(document).ready(function() {
        var i = 1;
        $('#add').click(function() {
            i++;
            $('#dynamic_field').append('<div id="row' + i + '" class="form-group col-md-12"><input type="hidden" name="varian_id[]" value="' + i + '"><label>Nama Varian ' + i + ' <small style="color:red">*</small></label><div class="input-icon"><input type="text" class="form-control" name="menu_variant[]" required><span class="input-icon-addon btn_remove" id="' + i + '"><i class="fas fa-trash" style="color:red"></i></span></div></div>');

            $('.selectpicker').selectpicker('refresh');
        });

        // $('#add').click(function() {
        //     i++;
        //     $('#dynamic_field').append('<div id="row' + i + '"><div class="form-group col-md-12"><label>Nama Varian ' + i + '<small style="color:red">*</small><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn-sm btn-link btn_remove"><i class="fas fa-trash"></i></button></label><input type="text" name="menu_variant[]" class="form-control" required></div></div>');

        //     $('.selectpicker').selectpicker('refresh');
        // });
        $(document).on('click', '.btn_remove', function() {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
        });
        $('#submit').click(function() {
            $.ajax({
                url: "name.php",
                method: "POST",
                data: $('#add_name').serialize(),
                success: function(data) {
                    alert(data);
                    $('#add_name')[0].reset();
                }
            });
        });
    });



    const fruits = [];

    function genereateID(MAIN) {
        if (MAIN == 'MAIN ACCOUNT') {
            const date = Date.now();
            var idNa = 'M' + date;
            fruits.push(idNa);
            var $newOption6 = $("<option></option>").val(idNa).text(idNa)
            $("#ID_ACCOUNT_TMPNA").append($newOption6).trigger('change');

            // var totalLIST = Number($('[name="idList"]').val())
            // var totalListNew = totalLIST+1;
            // console.log(totalListNew);
            //
            // var $newOption7 = $("<option selected='selected'></option>").val(idNa).text(idNa)
            // $("#ID_ACCOUNT_TMPNANEW"+totalListNew).append($newOption7).trigger('change');

            $("#ID_ACCOUNT_TMP_INPUT").val(idNa);

            $('#idAccount').hide();
            $('#idAccountInput').show();
            $('#idAccount' + $('[name="idList"]').val()).hide();
            $('#idAccountInput' + $('[name="idList"]').val()).show();
        } else {
            $('#idAccount').show();
            $('#idAccountInput').hide();
            $('#idAccount' + $('[name="idList"]').val()).show();
            $('#idAccountInput' + $('[name="idList"]').val()).hide();
        }
        // idAccountInput
        // idAccount
    }

    function genereateIDNEW(MAIN) {
        if (MAIN == 'MAIN ACCOUNT') {
            const date = Date.now();
            var idNa = 'M' + date;
            fruits.push(idNa);

            var $newOption6 = $("<option></option>").val(fruits).text(fruits)
            $("#ID_ACCOUNT_TMPNANEW" + $('[name="idList"]').val()).append($newOption6).trigger('change');


            $("#ID_ACCOUNT_TMP_INPUT" + $('[name="idList"]').val()).val(idNa);
            $('#idAccount' + $('[name="idList"]').val()).hide();
            $('#idAccountInput' + $('[name="idList"]').val()).show();
        } else {

            for (var i = 0, l = fruits.length; i < l; i++) {
                var $newOption6 = $("<option></option>").val(fruits[i]).text(fruits[i])
                $("#ID_ACCOUNT_TMPNANEW" + $('[name="idList"]').val()).append($newOption6).trigger('change');
            }



            // for(var i = 0, l = totalLIST; i < l; i++){
            //   var totalLIST = Number($('[name="idList"]').val())
            //   var totalListNew = totalLIST-1;
            //   log
            // for(var i = 0, l = fruits.length; i < l; i++){
            //   var $newOption6 = $("<option></option>").val(fruits[i]).text(fruits[i])
            //   $("#ID_ACCOUNT_TMPNANEW"+totalListNew).append($newOption6).trigger('change');
            // }
            // }


            $('#idAccount' + $('[name="idList"]').val()).show();
            $('#idAccountInput' + $('[name="idList"]').val()).hide();
        }
        // idAccountInput
        // idAccount
    }

    function tambahID() {
        var totalLIST = Number($('[name="idList"]').val())
        $('[name="idList"]').val(totalLIST + 1);

    }

    function hapusID() {
        var totalLIST = Number($('[name="idList"]').val())
        $('[name="idList"]').val(totalLIST - 1);

    }
</script>

<script>
    let url = "<?= site_url('manage/') ?>"

    function get_sku() {

        $.ajax({
            url: url + 'get_sku',
            dataType: "JSON",
            type: "POST",
            success: function(data) {
                $('[name = "menu_sku"]').val(data.menu_sku);
            }
        });
    }

    var menu_basic_price = document.getElementById('menu_basic_price');
    menu_basic_price.addEventListener('keyup', function(e) {
        menu_basic_price.value = formatprice(this.value);
    });


    var menu_selling_price = document.getElementById('menu_selling_price');
    menu_selling_price.addEventListener('keyup', function(e) {
        menu_selling_price.value = formatprice(this.value);
    });

    /* Fungsi */
    function formatprice(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
</script>
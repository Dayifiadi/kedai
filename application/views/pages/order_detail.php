<div class="page-inner">

    <input type="hidden" name="orders_id" value="<?= $orders->row()->orders_id ?>">
    <input type="hidden" name="orders_number" value="<?= $orders->row()->orders_number ?>">
    <div class="row orderdetail">


    </div>
    <div class="card card-plain">
        <div class="card-body">
            <div class="row">
                <!-- <div class="col-md-6">
                    <h2>Voucher</h2>
                    <button class="btn btn-link">
                        <h3>Gunakan/Masukan Kode</h3>
                    </button>
                </div> -->
                <div class="col-md-6" style="align-items: center;justify-content: flex-end;padding: 1rem;">
                    Voucher
                    <button type="submit" class="col-md-2 btn btn-link">Gunakan/Masukan Kode</button>
                </div>
                <div class="col-md-6" style="display: flex;align-items: center;justify-content: flex-end;padding: 1rem;">
                    Total Pesanan : &emsp;<h1 class="text-warning ordertotal">Rp.</h1>&emsp;
                    <button type="submit" class="col-md-6 btn btn-primary" data-toggle="modal" data-target="#QrcodeBarcodeModal">Pembayaran</button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="QrcodeBarcodeModal" tabindex="-1" role="dialog" aria-labelledby="QrcodeBarcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Metode Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> -->
            <div class="modal-body">
                <form action="<?= base_url('order/confirm_payment') ?>" method="POST" id="exampleValidation">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="orders_name" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Telpon</label>
                                <input type="text" name="orders_phone" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item nav flex-column nav-pills nav-default nav-pills-no-bd nav-pills-icons">
                                        <input type="radio" name="orders_paymentnow" value="BAYAR SEKARANG" class="selectgroup-input" onclick="sekarang()" required>
                                        <span class="selectgroup-button"><i class="flaticon-check"></i> BAYAR SEKARANG</span>
                                    </label>
                                    <label class="selectgroup-item nav flex-column nav-pills nav-default nav-pills-no-bd nav-pills-icons">
                                        <input type="radio" name="orders_paymentnow" value="BAYAR NANTI" class="selectgroup-input" onclick="nanti()" required>
                                        <span class="selectgroup-button"><i class="flaticon-cross-1"></i> BAYAR NANTI</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item nav flex-column nav-pills nav-default nav-pills-no-bd nav-pills-icons">
                                        <input type="radio" name="orders_type" value="DI TEMPAT" class="selectgroup-input" required>
                                        <span class="selectgroup-button"><i class="flaticon-store"></i> DI TEMPAT</span>
                                    </label>
                                    <label class="selectgroup-item nav flex-column nav-pills nav-default nav-pills-no-bd nav-pills-icons">
                                        <input type="radio" name="orders_type" value="BUNGKUS" class="selectgroup-input" required>
                                        <span class="selectgroup-button"><i class="flaticon-box-2"></i> BUNGKUS</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="modal_paymenttype" style="display: none;">
                            <div class="form-group">
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item nav flex-column nav-pills nav-default nav-pills-no-bd nav-pills-icons">
                                        <input type="radio" id="orders_payment" name="orders_payment" value="TUNAI" class="selectgroup-input" onclick="cash()">
                                        <span class="selectgroup-button"><i class="flaticon-coins"></i> TUNAI</span>
                                    </label>
                                    <label class="selectgroup-item nav flex-column nav-pills nav-default nav-pills-no-bd nav-pills-icons">
                                        <input type="radio" id="orders_payment" name="orders_payment" value="TRANSFER" class="selectgroup-input" onclick="transfer()">
                                        <span class="selectgroup-button"><i class="flaticon-credit-card"></i> TRANSFER</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="modal_paymentqr" style="display: none;">
                            <CENTER>
                                <img src="http://localhost/kedai/uploads/menu/301909200101.png" alt="Grocery Ecommerce Template" class="mb-3 img-fluid">
                            </CENTER>
                            <!-- <div class="form-group"> -->
                            <!-- <label class="selectgroup-item nav flex-column nav-pills nav-default nav-pills-no-bd nav-pills-icons">
                                    <span class="selectgroup-button"><i class="fas fa-qrcode"></i> </span>
                                </label> -->
                            <!-- </div> -->
                        </div>

                        <div class="col-md-12" id="modal_payment" style="display: none;">
                            <h1 class="text-center">Pembayaran</h1>
                            <div class="form-group form-show-validation">
                                <label>Nominal Uang<small style="color:red">*</small></label>
                                <input type="text" name="payment_cash" id="payment_cash" class="form-control" onchange="payment_calc()">
                            </div>
                            <div class="invoice-item" style="max-height: 200px;overflow-y: auto;">
                                <table style="font-family: arial, sans-serif;border-collapse: collapse;width: 100%;">
                                    <thead>
                                        <tr>
                                            <td class="text-right" style="font-size:14px"><strong>Subtotal</strong></td>
                                            <td class="text-right col-xs-1 subtotal" style="font-size:14px"><strong></strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right" style="font-size:14px"><strong>Bayar</strong></td>
                                            <td class="text-right col-xs-1 bayar" style="font-size:14px"><strong></strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right" style="font-size:14px"><strong>Kembalian</strong></td>
                                            <td class="text-right col-xs-1 kembalian" style="font-size:14px"><strong></strong></td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <br>
                        </div>

                        <input type="hidden" name="orders_quantity" id="orders_quantity" value="">
                        <input type="hidden" name="orders_subtotal" value="">
                        <input type="hidden" name="orders_change" value="">
                        <input type="hidden" name="orders_id" value="<?= $orders->row()->orders_id ?>">
                        <input type="hidden" name="orders_number" value="<?= $orders->row()->orders_number ?>">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-block btn-primary" id="btnBayar">Bayar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let url = "<?= site_url('order/') ?>"

    function orderdetail_updatenote(id) {
        $.ajax({
            url: url + 'orderdetail_updatenote',
            dataType: "JSON",
            type: "POST",
            data: {
                id: id,
                note: $('[name="orderdetail_note[' + id + ']"]').val(),
            },
            success: function(data) {
                $('[name="orderdetail_note[' + id + ']"]').val(data.note);
                $('.border-status').html('has-success');
                document.getElementById('orderdetail_note[' + id + ']').style.borderColor = "green";
                $.notify({
                    icon: 'flaticon-alarm-1',
                    title: 'Berhasil',
                    message: data.message
                }, {
                    type: 'info',
                    placement: {
                        from: "top",
                        align: "center"
                    },
                    time: 1000,
                });
            }
        });
    }


    $(document).ready(function() {
        $.ajax({
            url: url + 'orderdetail',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_id: $('[name="orders_id"]').val(),
                orders_number: $('[name="orders_number"]').val(),
            },
            success: function(data) {
                var html = data.html;
                $('.orderdetail').html(html);
                $('.ordertotal').html(data.total);
                $('.subtotal').html(data.subtotal);
                $('[name="orders_quantity"]').val(data.quantity);
            }
        });

    });

    // function order_total() {
    //     $.ajax({
    //         url: url + 'ordertotal',
    //         dataType: "JSON",
    //         type: "POST",
    //         data: {
    //             orders_id: $('[name="orders_id"]').val(),
    //             orders_number: $('[name="orders_number"]').val(),
    //         },
    //         success: function(data) {
    //             order_total();
    //             var html = data.html;
    //             $('.ordertotal').html(html);
    //         }

    //     });
    // }

    function plus(i) {
        var orderdetail_id = i;
        $.ajax({
            url: url + 'orderdetail_plus',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_id: $('[name="orders_id"]').val(),
                orders_number: $('[name="orders_number"]').val(),
                orderdetail_id: orderdetail_id,
            },
            success: function(data) {
                var html = data.html;
                $('.orderdetail').html(html);
                $('.ordertotal').html(data.total);
                $('[name="orders_quantity"]').val(data.quantity);
            }
        });
    }

    function minus(i) {
        var orderdetail_id = i;
        $.ajax({
            url: url + 'orderdetail_min',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_id: $('[name="orders_id"]').val(),
                orders_number: $('[name="orders_number"]').val(),
                orderdetail_id: orderdetail_id,
            },
            success: function(data) {
                var html = data.html;
                $('.orderdetail').html(html);
                $('.ordertotal').html(data.total);
                $('[name="orders_quantity"]').val(data.quantity);
            }

        });
    }

    function hapus_list(i) {
        var orderdetail_id = i;
        $.ajax({
            url: url + 'orderdetail_delete',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_id: $('[name="orders_id"]').val(),
                orders_number: $('[name="orders_number"]').val(),
                orderdetail_id: orderdetail_id,
            },
            success: function(data) {
                var html = data.html;
                $('.orderdetail').html(html);
                $('.ordertotal').html(data.total);
                $('[name="orders_quantity"]').val(data.quantity);
            }

        });
    }

    function sekarang() {
        var x = document.getElementById("modal_paymenttype");
        if (x.style.display != "none") {
            x.style.display = "none";
            document.getElementById("payment_cash").required = false;
            document.getElementById("orders_payment").required = false;
        } else {
            x.style.display = "block";
            document.getElementById("payment_cash").required = true;
            document.getElementById("orders_payment").required = true;

        }
    }

    function nanti() {
        var x = document.getElementById("modal_paymenttype");
        x.style.display = "none";
        document.getElementById("btnBayar").disabled = false;
        document.getElementById("payment_cash").required = false;

    }

    function cash() {
        var x = document.getElementById("modal_payment");
        var y = document.getElementById("modal_paymentqr");
        if (x.style.display != "none") {
            x.style.display = "none";
            y.style.display = "block";
        } else {
            x.style.display = "block";
            y.style.display = "none";
            document.getElementById("payment_cash").required = true;
        }
    }

    function transfer() {
        var x = document.getElementById("modal_payment");
        var y = document.getElementById("modal_paymentqr");
        x.style.display = "none";
        y.style.display = "block";
        document.getElementById("btnBayar").disabled = false;
        document.getElementById("payment_cash").required = false;

    }

    var payment_cash = document.getElementById('payment_cash');
    payment_cash.addEventListener('keyup', function(e) {
        payment_cash.value = formatprice(this.value);

        $.ajax({
            url: url + 'orderpayment',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_id: $('[name="orders_id"]').val(),
                orders_number: $('[name="orders_number"]').val(),
                payment_cash: $('[name="payment_cash"]').val(),
            },
            success: function(data) {
                var html = data.html;
                $('.kembalian').html(data.kembalian);
                $('.subtotal').html(data.subtotal);
                $('.bayar').html(data.bayar);
                $('[name="orders_subtotal"]').val(data.subtotal);
                $('[name="orders_change"]').val(data.kembalian);
                $('[name="payment_cash"]').val(data.bayar);
                $('[name="orders_quantity"]').val(data.quantity);
                if (data.status == 1) {
                    document.getElementById("btnBayar").disabled = false;
                } else {
                    document.getElementById("btnBayar").disabled = true;
                }
            }

        });


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
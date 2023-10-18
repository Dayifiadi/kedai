<div class="page-inner">
    <div class="row">
        <input type="hidden" name="orders_name" value="">
        <input type="hidden" name="orders_phone" value="">
        <input type="hidden" name="orders_number" value="">
        <div class="col-md-9" style="max-height: 800px;overflow-y: auto;">
            <div class="row">
                <?php foreach ($menu->result() as $row) { ?>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <h2 class="fs-6"><?= $row->menu_name ?></h2>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <?= $row->menu_description ?>
                                            <!-- <br>
                                            <h4>Varian :</h4> -->
                                            <?php
                                            $variant = $this->order_model->get_variant($row->menu_id);
                                            foreach ($variant->result() as $row_variant) {
                                                echo "<br>" . $row_variant->variant_name;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="text-center position-relative">
                                            <!-- badge -->
                                            <div class=" position-absolute top-0 start-0">
                                                <span class="badge bg-success">New</span>
                                            </div>
                                            <a href="#!" tabindex="-1"><img src="<?= $row->menu_image ?>" alt="Grocery Ecommerce Template" class="mb-3 img-fluid"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-8">
                                        <span class="text-dark">Rp. <?= number_format($row->menu_selling_price, 0) ?></span>
                                        <span class="text-decoration-line-through text-muted"><del>Rp. <?= number_format($row->menu_selling_price, 0) ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <?php
                                        if ($variant->num_rows()) { ?>
                                            <a href="#myModalTambah" class="btn btn-block btn-warning" data-toggle="modal" data-id="<?= $row->menu_id ?>" data-toggle="tooltip" data-placement="top" title="Tambah">Tambah</a>
                                        <?php } else { ?>
                                            <button class="btn btn-block btn-warning" onclick="add_order('<?= $row->menu_sku ?>')">Tambah</button>
                                        <?php }
                                        ?>
                                        <!-- <div class="row">
                                            <div class="col-md-6">
                                                <button class="btn btn-warning btn-block" onclick="add_order('<?= $row->menu_sku ?>')">
                                                    <span class="btn-label">
                                                        <i class="fa fa-plus"></i>
                                                    </span>
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button class="btn btn-danger btn-block" onclick="delete_order('<?= $row->menu_sku ?>')">
                                                    <span class="btn-label">
                                                        <i class="fas fa-minus-square"></i>
                                                    </span>
                                                </button>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>



        <div class="col-md-3">
            <div class="view_order_list"></div>
            <?php
            $this->db->where('orders_status', 'KASIR');
            $this->db->group_by('orders_number');
            $orders = $this->db->get('orders'); ?>
            <?php if ($orders->num_rows() > 0) { ?>
                <div class="card">
                    <div class="card-body">
                        <div class="card-title fw-mediumbold text-center">Pesanan belum selesai</div>
                        <div class="card-list">
                            <?php
                            foreach ($orders->result() as $row) {
                            ?>
                                <div class="item-list">
                                    <div class="info-user ml-3">
                                        <div class="username"><?= $row->orders_number ?></div>
                                        <div class="status"><?= $row->orders_name ?></div>
                                    </div>
                                    <a class="btn btn-icon btn-link btn-primary" onclick="edit_order('<?= $row->orders_number ?>')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a class="btn btn-icon btn-link btn-danger" href="<?= base_url('order/delete_orderdata/') . $row->orders_number ?>">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                <div class="separator-dashed"></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModalTambah" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Varian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('order/ordervariant_add') ?>" method="POST">
                <div class="modal-body">
                    <div class="ModalTambah"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $('.qty1').html('100');
    let url = "<?= site_url('order/') ?>"

    function add_order(i) {
        var menu_sku = i;
        $.ajax({
            url: url + 'add_order',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_name: $('[name="orders_name"]').val(),
                orders_phone: $('[name="orders_phone"]').val(),
                orders_number: $('[name="orders_number"]').val(),
                menu_sku: menu_sku,
            },
            success: function(data) {
                var html = data.html;
                var deleted = '<button class="btn btn-warning btn-rounded btn-sm btn-block">Kurangi</button>';
                $('.view_order_list').html(html);
                $('[name = "orders_number"]').val(data.orders_number);
            }

        });
    }

    function delete_order(i) {
        var menu_sku = i;
        $.ajax({
            url: url + 'delete_order',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_name: $('[name="orders_name"]').val(),
                orders_phone: $('[name="orders_phone"]').val(),
                orders_number: $('[name="orders_number"]').val(),
                menu_sku: menu_sku,
            },
            success: function(data) {
                var html = data.html;
                if (data.status != "delete") {
                    $('.view_order_list').html(html);
                } else {
                    $('.view_order_list').html('');
                    $('[name = "orders_number"]').val('');
                }
                if (data.status == 'failed') {
                    $.notify({
                        icon: 'flaticon-alarm-1',
                        title: 'Gagal',
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

            }

        });
    }
    <?php if ($this->session->flashdata('orders_number') != "") { ?>
        edit_order('<?= $this->session->flashdata('orders_number') ?>');
    <?php } ?>

    function edit_order(i) {
        $.ajax({
            url: url + 'edit_order',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_number: i,
            },
            success: function(data) {
                var html = data.html;
                $('.view_order_list').html(html);
                $('[name = "orders_number"]').val(data.orders_number);

                if (data.status == 'success') {
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
            }

        });
    }

    function delete_orderdata(i) {
        var menu_sku = i;
        $.ajax({
            url: url + 'delete_orderdata',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_number: i,
            },
            success: function(data) {
                var html = data.html;
                $('.view_order_list').html('');
                $('[name = "orders_number"]').val('');

                if (data.status == 'success') {
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

            }

        });
    }
</script>


<script type="text/javascript">
    var table;
    $(document).ready(function() {

        $('#myModalTambah').on('show.bs.modal', function(e) {
            var menu_id = $(e.relatedTarget).data('id');
            //menggunakan fungsi ajax untuk pengambilan data
            $.ajax({
                type: 'post',
                url: '<?= base_url('order/modal_variant') ?>',
                data: {
                    menu_id: menu_id,
                    orders_number: $('[name="orders_number"]').val(),
                },
                success: function(data) {
                    $('.ModalTambah').html(data); //menampilkan data ke dalam modal
                }
            });
        });
    });
</script>
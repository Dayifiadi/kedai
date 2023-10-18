<div class="page-inner">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dapur" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="font-size: 12px">Pilih</th>
                                    <th class="text-center" style="font-size: 12px">Nama</th>
                                    <th class="text-center" style="font-size: 12px">Jumlah</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="ordermenu"></div>
        </div>
    </div>
    <!-- end class row -->
</div>


<script type="text/javascript">
    let url = "<?= site_url('kitchen/') ?>"
    var table;
    $(document).ready(function() {
        $('#basic-datatables').DataTable({});

        //datatables
        table = $('#dapur').DataTable({
            "bFilter": false,
            "bInfo": false,
            "lengthChange": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('kitchen/getdatatables_kitchen') ?>",
                "type": "POST",
                "data": function(data) {
                    // data.region = $('[name="region"]').val();
                }
            },
            "columnDefs": [{
                    "targets": [0, 1, 2],
                    "orderable": false
                },
                {
                    "targets": [0, 1, 2],
                    "className": 'text-center'
                }
            ]
        });

        //datatables
        table2 = $('#dapur-disiapkan').DataTable({
            "bFilter": false,
            "bInfo": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('kitchen/getdatatables_disiapkan') ?>",
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

    list();
    // $(document).ready(function() {
    function list() {
        $.ajax({
            url: url + 'list_ordermenu',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_id: $('[name="orders_id"]').val(),
                orders_number: $('[name="orders_number"]').val(),
            },
            success: function(data) {
                var html = data.html;
                $('.ordermenu').html(html);
            }
        });
    }
    // });

    function checklist(i) {
        var orders_id = i;
        $.ajax({
            url: url + 'order_checklist',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_id: orders_id,
            },
            success: function(data) {
                table.ajax.reload();
                table2.ajax.reload();
                // a.ajax.reload();
                list();
                // window.location.reload();
                // var html = data.html;
                // $('.orderdetail').html(html);
                // $('.ordertotal').html(data.total);
                // $('[name="orders_quantity"]').val(data.quantity);
            }
        });
    }
</script>
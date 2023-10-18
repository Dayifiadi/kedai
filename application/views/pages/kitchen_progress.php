<div class="page-inner">
    <div class="row">
        <div class="col-md-6">
            <div class="listdapur"></div>
        </div>
        <div class="col-md-6">
            <div class="listselesai"></div>
        </div>
    </div>
    <!-- end class row -->
</div>

<script type="text/javascript">
    let url = "<?= site_url('kitchen/') ?>"

    list_dapur();
    list_selesai();

    function list_dapur() {
        $.ajax({
            url: url + 'list_dapur',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_id: $('[name="orders_id"]').val(),
                orders_number: $('[name="orders_number"]').val(),
            },
            success: function(data) {
                var html = data.html;
                $('.listdapur').html(html);
            }
        });
    }

    function list_selesai() {
        $.ajax({
            url: url + 'list_selesai',
            dataType: "JSON",
            type: "POST",
            data: {
                orders_id: $('[name="orders_id"]').val(),
                orders_number: $('[name="orders_number"]').val(),
            },
            success: function(data) {
                var html = data.html;
                $('.listselesai').html(html);
            }
        });
    }

    function selesai(i, j) {
        var orderdetail_id = i;
        var orders_id = j;
        $.ajax({
            url: url + 'action_orderselesai',
            dataType: "JSON",
            type: "POST",
            data: {
                orderdetail_id: orderdetail_id,
                orders_id: orders_id,
            },
            success: function(data) {
                list_dapur();
                list_selesai();
                console.log(data.total);
                if (data.status == "SELESAI") {
                    $('.ModalTambah').html(data); //menampilkan data ke dalam modal
                }
            }
        });
    }

    function dapur(i) {
        var orderdetail_id = i;
        $.ajax({
            url: url + 'action_ordersdapur',
            dataType: "JSON",
            type: "POST",
            data: {
                orderdetail_id: orderdetail_id,
            },
            success: function(data) {
                list_dapur();
                list_selesai();
            }
        });
    }
</script>
<div class="col-md-12">
    <div class="card card-plain">
        <div class="card-body">
            <h3 class="card-title"><?= $page_title ?></h3>
            <br />
            <div class="table-responsive">
                <table class="table table-shopping">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th class="text-center">Harga</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Total</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sum_total = 0;
                        $sum_quantity = 0;
                        foreach ($orderdetail->result() as $row) {
                            $sum_total += $row->orderdetail_total;
                            $sum_quantity += $row->orderdetail_quantity;
                            $menu = $this->db->get_where('menu', ['menu_sku' => $row->menu_sku])->row();
                        ?>
                            <tr>
                                <td class="td-name">
                                    <a href="#jacket"><?= $menu->menu_name ?></a>
                                    <br />
                                    <small><?= $row->orderdetail_note ?></small>
                                </td>
                                <td class="td-number text-center">
                                    Rp.<?= number_format($row->orderdetail_price, 0) ?>
                                </td>
                                <td class="td-number text-center">
                                    <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <?= $row->orderdetail_quantity ?>

                                    <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </td>
                                <td class="td-number text-center">
                                    Rp.<?= number_format($row->orderdetail_total, 0) ?>
                                </td>
                                <td class="td-actions text-center">
                                    <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                        <i class="fas fa-trash" style="color:red"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="2" class="text-center">TOTAL</td>
                            <td class="td-total text-center">
                                <?= number_format($sum_quantity, 0) ?>
                            </td>
                            <td colspan="1" class="td-price text-center">
                                <small>Rp.<?= number_format($sum_total, 0) ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-round">Pembayaran</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
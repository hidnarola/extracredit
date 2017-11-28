<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-calculator3"></i> <span class="text-semibold"><?php echo ($account['action_matters_campaign'] != '') ? $account['action_matters_campaign'] : $account['vendor_name'] ?> Transactions</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('accounts'); ?>"><i class="icon-calculator3 position-left"></i> Accounts</a></li>
            <li class="active">Transactions</li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($this->session->flashdata('success')) {
                ?>
                <div class="alert alert-success hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->flashdata('success') ?></strong>
                </div>
            <?php } elseif ($this->session->flashdata('error')) {
                ?>
                <div class="alert alert-danger hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>                    
                    <strong><?php echo $this->session->flashdata('error') ?></strong>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="panel panel-flat">
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Post Date</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Paymt Method</th>
                    <th>Paymt No.</th>
                    <th>Notes</th>
                    <th>Debit Amt</th>
                    <th>Credit Amt</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($transactions as $key => $val) {
                    $class=''
;                    if ($val['is_refund'] == 1) {
                        $class = 'danger';
                    } else if ($val['is_refund'] == -1) {
                        $class = 'warning';
                    } else if ($val['is_refund'] == -2) {
                        $class = 'success';
                    }
                    ?>
                <tr class="<?php echo $class;?>">
                        <td><?php echo ($val['date'] != '') ? date('m/d/Y', strtotime($val['date'])) : ''; ?></td>
                        <td><?php echo($val['post_date'] != '') ? date('m/d/Y', strtotime($val['post_date'])) : ''; ?></td>
                        <td>
                            <?php
                            if ($val['is_refund'] == 1) {
                                echo "<h6 style='font-size: 13px;margin-top: 0px;margin-bottom: 0px;'><span style='color:red'>Refund </span>- ";
                            } else if ($val['is_refund'] == -1) {
                                echo "<h6 style='font-size: 13px;margin-top: 0px;margin-bottom: 0px;'><span style='color:#F57C00'>Transfer to </span>- ";
                            } else if ($val['is_refund'] == -2) {
                                echo "<h6 style='font-size: 13px;margin-top: 0px;margin-bottom: 0px;'><span style='color:#388E3C'>Transfer From </span>- ";
                            }
                            echo $val['firstname'] . '</h6>';
                            ?>
                        </td>
                        <td><?php echo $val['lastname'] ?></td>
                        <td><?php echo $val['payment_method'] ?></td>
                        <td><?php echo $val['payment_number'] ?></td>
                        <td><?php echo $val['memo'] ?></td>
                        <td><?php
                            if ($val['is_refund'] == 1) {
                                echo "-$" . $val['credit_amt'];
                            } else {
                                echo ($val['debit_amt'] != '') ? '-$' . $val['debit_amt'] : '';
                            }
                            ?></td>
                        <td><?php
                            if ($val['is_refund'] == 1) {
                                echo '';
                            } else {
                                echo ($val['credit_amt'] != '') ? '$' . $val['credit_amt'] : '';
                            }
                            ?></td>
                        <?php
                        if ($val['is_refund'] == 1) {
                            $total -= $val['credit_amt'];
                        } else {
                            if ($val['credit_amt'] != '') {
                                $total += $val['credit_amt'];
                            } elseif ($val['debit_amt'] != '') {
                                $total -= $val['debit_amt'];
                            }
                        }
                        ?>
                        <!--<td><?php // echo ($val['balance'] != '') ? '$' . $val['balance'] : ''                                     ?></td>-->
                        <td><?php
                            if ($total < 0) {
                                $t = substr($total, 1);
                                echo '-$' . $t;
                            } else {
                                echo '$' . $total;
                            }
                            ?></td>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    $(function () {
        var table = $('.datatable-basic').dataTable({
            scrollX: true,
            "aaSorting": [],
            autoWidth: false,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        });
        $('.datatable-basic').on('draw.dt', function () {
            total = 0;
            table.api().rows({filter: 'applied'}).every(function (rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                debit_amt = data[7].substr(2);
                credit_amt = data[8].substr(1);
                if (debit_amt != '') {
                    debit_amt = parseFloat(debit_amt);
                    total = total - debit_amt;
                }
                if (credit_amt != '') {
                    credit_amt = parseFloat(credit_amt);
                    total = total + credit_amt;
                }

                if (total > 0) {
                    data[9] = '$' + total.toFixed(2);
                } else {
                    var r = '$' + total.toFixed(2);
                    t = r.substr(2);
//                    console.log(r);
//                    console.log('-$' + t);
                    data[9] = '-$' + t;
                }
                this.data(data);
            });
        });
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    });
</script>
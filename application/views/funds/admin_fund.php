<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-cash3"></i> <span class="text-semibold">Funds</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li>Funds</li>
            <li class="active">Admin Fund</li>
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
                    <th>#</th>
                    <th>Date</th>
                    <th>Post Date</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Fund Type</th>
                    <th>Sub Category</th>
                    <th>Paymt Method</th>
                    <th>Paymt No.</th>
                    <th>Notes</th>
                    <th>Debit Amt</th>
                    <th>Credit Amt</th>
                    <th>Balance</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    $(function () {
        $('.datatable-basic').dataTable({
            scrollX: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
//            order: [[7, "desc"]],
            ajax: site_url + 'funds/get_adminfund',
            columns: [
                {
                    data: "sr_no",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "date",
                    visible: true,
                },
                {
                    data: "post_date",
                    visible: true,
                },
                {
                    data: "firstname",
                    visible: true,
                },
                {
                    data: "lastname",
                    visible: true,
                },
                {
                    data: "fund_type",
                    visible: true,
                },
                {
                    data: "sub_category",
                    visible: true,
                },
                {
                    data: "payment_method",
                    visible: true,
                },
                {
                    data: "payment_number",
                    visible: true,
                },
                {
                    data: "memo",
                    visible: true
                },
                {
                    data: "debit_amt",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (data != '') {
                            return '-$' + data;
                        } else
                            return '';
                    }
                },
                {
                    data: "credit_amt",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (data != '') {
                            return '$' + data;
                        } else
                            return '';
                    }
                },
                {
                    data: "balance",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (data != '') {
                            return '$' + data;
                        } else
                            return '';
                    }
                },
            ]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    });
</script>
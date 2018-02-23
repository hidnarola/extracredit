<!--<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>-->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/dt/jq-2.1.4,jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/r/dt/jq-2.1.4,jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.js"></script>-->
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/ui/moment/moment.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/anytime.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.time.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/legacy.js"></script>
<script type="text/javascript" src="assets/js/pages/picker_date.js"></script>
<style>
    .dt-button {
        display: block;
        width: 60px;
        height: 35px;
        background: #26A69A;
        padding: 6px;
        text-align: center;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        margin-right: 4px;
        transition: all 0.3s ease-in-out;
        -webkit-transition: all 0.3s ease-in-out;
        -moz-transition: all 0.3s ease-in-out;
        text-decoration:none;
        border: 2px solid #fff;
    }

    .dt-buttons a:hover,.dt-buttons a:focus {
        color: #26A69A !important;
        background: #fff !important;
        border: 2px solid #26A69A;
        text-decoration:none;
    }
     .custom_perpage_dropdown .dataTables_length {margin: 0 18px 20px 20px;}
    .dataTables_info {padding: 8px 22px;margin-bottom: 10px;}
    .dataTables_paginate {margin: 10px 20px 20px 20px;}
</style>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-cash4"></i> <span class="text-semibold">Ultimate Program Report</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Ultimate Program Report</li>
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
    <div class="panel panel-flat custom_perpage_dropdown">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <label>Post date filter: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                        <?php
                        $date_filter = date('m/01/Y') . ' - ' . date('m/t/Y'); // hard-coded '01' for first day
                        ?>
                        <input type="text" name="post_date_filter" id="post_date_filter" class="form-control daterange-basic" value="<?php echo $date_filter; ?>"> 
                    </div>
                </div>
            </div>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Active</th>
                    <th>Program Name</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zip</th>
                    <th>Contact Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Tax ID</th>
                    <th>Type</th>                    
                    <th>Status</th> 
                    <th>Income</th>
                    <th>No of Payments</th>                   
                    <th>Payment Amount</th>                   
                    <th>Balance Amount</th>                   
                    <th>Last Deposit Date</th>                   
                    <th>Last Payment Date</th>                   
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    var data_table = '';
    var post_date_filter = $('#post_date_filter').val();

    $(function () {
        bind();
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    });

    function bind() {
        data_table = $('.datatable-basic').dataTable({
            scrollX: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
//            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
//            order: [[7, "desc"]],
            dom: 'lBfrtipx',
            buttons: [
//                'copy',
                'excel',
                'csv',
//                'pdf',
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'TABLOID'
                }
            ],
            "ajax": {
                "url": site_url + 'reports/get_amc_balance_report',
                "type": "POST"
            },
//            ajax: site_url + 'reports/get_amc_balance_report',
            columns: [
                {
                    data: "is_active",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var status = '<span class="label bg-success">Active</span>';
                        if (full.is_active == 0) {
                            status = '<span class="label bg-warning">Not Active</span>';
                        }
                        return status;
                    }
                },
                {
                    data: "sub_category",
                    visible: true,
//                    render: function (data, type, full, meta) {
//                        if (full.type == 1) {
//                            return full.vendor_name;
//                        } else {
//                            return data
//                        }
//                    }
                },
                {
                    data: "address",
                    visible: true,
                },
                {
                    data: "city",
                    visible: true
                },
                {
                    data: "state",
                    visible: true
                },
                {
                    data: "zip",
                    visible: true
                },
                {
                    data: "contact_name",
                    visible: true
                },
                {
                    data: "email",
                    visible: true
                },
                {
                    data: "phone",
                    visible: true
                },
                {
                    data: "tax_id",
                    visible: true
                },
                {
                    data: "program_type",
                    visible: true
                },
                {
                    data: "status",
                    visible: true
                },
                {
                    data: "income",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.income == null) {
                            return '';
                        } else
                            return '$' + full.income;
                    }
                },
                {
                    data: "no_of_payments",
                    visible: true,
                },
                {
                    data: "payment_amount",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.payment_amount == null) {
                            return '';
                        } else
                            return '$' + full.payment_amount;
                    }
                },
                {
                    data: "balance_amount",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.balance_amount == null) {
                            return '';
                        } else
                            return '$' + full.balance_amount;
                    }
                },
                {
                    data: "post_date",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.post_date == '01/01/1970') {
                            return ' ';
                        } else {
                            return full.post_date;
                        }
                    }
                },
                {
                    data: "check_date",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.check_date == '01/01/1970') {
                            return ' ';
                        } else {
                            return full.check_date;
                        }
                    }
                },
            ]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    }
    //--- daterange change event and call bind function
    $('#post_date_filter').on('apply.daterangepicker', function (ev, picker) {
        post_date_filter = $(this).val();
        data_table.fnDestroy();
        bind();
    });
</script>
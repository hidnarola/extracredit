<style>
    .dt-button {display: block;width: 60px;height: 35px;background: #26A69A;padding: 6px;text-align: center;border-radius: 5px;color: white;font-weight: bold;margin-right: 4px;transition: all 0.3s ease-in-out;-webkit-transition: all 0.3s ease-in-out;-moz-transition: all 0.3s ease-in-out;text-decoration:none;border: 2px solid #fff;}
    .dt-buttons a:hover,.dt-buttons a:focus {color: #26A69A !important;background: #fff !important;border: 2px solid #26A69A;text-decoration:none;}
    .custom_perpage_dropdown .dataTables_length {margin: 0 18px 20px 20px;}
    .dataTables_info {padding: 8px 22px;margin-bottom: 10px;}
    .dataTables_paginate {margin: 10px 20px 20px 20px;}
</style>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/ui/moment/moment.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/anytime.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.time.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/legacy.js"></script>
<script type="text/javascript" src="assets/js/pages/picker_date.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-coins"></i> <span class="text-semibold">Ultimate Donor Report</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Ultimate Donor Report</li>
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
                    <th>Fund Type</th>
                    <th>Subcategory</th>
                    <th>Date</th>
                    <th>Post Date</th>
                    <th>Donar ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Address</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Zip</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Refund</th>
                    <th>Pmt Method</th>
                    <th>Pmt Number</th>
                    <th>Memo</th>                    
                    <th>UBI</th>                    
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
            "bPaginate": true,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            order: [[7, "desc"]],
            dom: 'lBfrtipx',
            buttons: [
//                'copy',
                'excel',
                'csv',
//                'pdf',
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                }
            ],
            ajax: {
                url: site_url + 'reports/get_donors_reports',
                data: {
                    post_date_filter: post_date_filter
                },
                type: 'post',
            },
            columns: [
                {
                    data: "fund_type",
                    visible: true,
                },
                {
                    data: "action_matters_campaign",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.type == 1) {
                            return full.vendor_name;
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data: "date",
                    visible: true
                },
                {
                    data: "post_date",
                    visible: true
                },
                {
                    data: "id",
                    visible: true
                },
                {
                    data: "firstname",
                    visible: true
                },
                {
                    data: "lastname",
                    visible: true
                },
                {
                    data: "address",
                    visible: true
                },
                {
                    data: "state",
                    visible: true
                },
                {
                    data: "city",
                    visible: true
                },
                {
                    data: "zip",
                    visible: true
                },
                {
                    data: "email",
                    visible: true
                },
                {
                    data: "amount",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.amount == null) {
                            return '';
                        } else
                            return '$' + full.amount;
                    }
                },
                {
                    data: "refund",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (data == 1) {
                            return 'Yes';
                        } else
                            return 'No';
                    }
                },
                {
                    data: "payment_type",
                    visible: true
                },
                {
                    data: "payment_number",
                    visible: true
                },
                {
                    data: "memo",
                    visible: true,
                },
                {
                    data: "ubi",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (data == 1) {
                            ubi_str = 'Yes';
                        } else {
                            ubi_str = 'No';
                        }
                        return ubi_str;
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
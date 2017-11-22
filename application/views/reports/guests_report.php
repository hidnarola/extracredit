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
    .dataTables_info {padding: 8px 22px;}
    .dataTables_paginate {margin: 0 20px 20px 20px;}
</style>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-people"></i> <span class="text-semibold">Ultimate Guest Report</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Ultimate Guest Report</li>
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
                    <label>Guest date filter: </label>
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
                    <th>Guest ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Company Name</th>
                    <th>Invite Date</th>
                    <th>Guest Date</th>
                    <th>AIR Date</th>
                    <th>AMC Created</th>
                    <th>Campaign</th>
                    <th>Address</th>
                    <!--<th>City</th>-->
                    <th>State</th>
                    <th>Zip</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Assistant</th>
                    <th>Assistant Phone</th>
                    <th>Assistant Email</th>                                      
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
            order: [[0, "desc"]],
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
            ajax: {
                url: site_url + 'reports/get_guests_reports',
                data: {
                    post_date_filter: post_date_filter
                },
                type: 'post',
            },
            columns: [
                {
                    data: "id",
                    visible: true,
                    sortable: false,
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
                    data: "companyname",
                    visible: true
                },
                {
                    data: "invite_date",
                    visible: true
                },
                {
                    data: "guest_date",
                    visible: true
                },
                {
                    data: "AIR_date",
                    visible: true
                },
                {
                    data: "AMC_created",
                    visible: true
                },
                {
                    data: "action_matters_campaign",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.type == 1) {
                            return full.vendor_name;
                        } else {
                            return data
                        }
                    }
                },
                {
                    data: "address",
                    visible: true
                },
                {
                    data: "state",
                    visible: true
                },
//                {
//                    data: "city",
//                    visible: true
//                },
                {
                    data: "zip",
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
                    data: "assistant",
                    visible: true
                },
                {
                    data: "assistant_phone",
                    visible: true
                },
                {
                    data: "assistant_email",
                    visible: true
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
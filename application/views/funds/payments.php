<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
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
            <h4><i class="icon-cash3"></i> <span class="text-semibold">Funds</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li>Funds</li>
            <li class="active">Payments</li>
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
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <label>Check Date filter: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                        <input type="text" name="date_filter" id="date_filter" class="form-control daterange-basic" value="<?php echo date('m/01/Y') . ' - ' . date('m/t/Y'); ?>"> 
                    </div>
                </div>
            </div>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Fund Type</th>
                    <th>Sub Category</th>
                    <th>Check Date</th>
                    <th>Check Number</th>
                    <th>Amount</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    var data_table = '';
    var date_filter = $('#date_filter').val();

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
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[2, "asc"]],
            ajax: {
                url: site_url + 'funds/get_payment',
                data: {
                    date_filter: date_filter
                },
            },
            columns: [
                {
                    data: "fund_type",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.payer == 'vendor') {
                            return 'Vendor';
                        } else {
                            return data
                        }
                    }
                },
                {
                    data: "action_matters_campaign",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.payer == 'vendor') {
                            return full.vendor_name;
                        } else {
                            return data
                        }
                    }
                },
                {
                    data: "check_date",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.date == '01/01/1970') {
                            return '-';
                        } else {
                            return full.check_date;
                        }
                    }
                },

                {
                    data: "check_number",
                    visible: true,
                },
                {
                    data: "amount",
                    visible: true,
                    render: function (data, type, full, meta) {
                        return '$' + full.amount;
                    }
                },
            ]
        });
    }
    //--- daterange change event and call bind function
    $('#date_filter').on('apply.daterangepicker', function (ev, picker) {
        date_filter = $(this).val();
        data_table.fnDestroy();
        bind();
    });
</script>
<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
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
            <h4><i class="icon-credit-card"></i> <span class="text-semibold">Payments</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
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
                    <label>Check date filter: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                        <?php
                        $date_filter = date('m/01/Y') . ' - ' . date('m/t/Y'); // hard-coded '01' for first day
                        ?>
                        <input type="text" name="post_date_filter" id="post_date_filter" class="form-control daterange-basic" value="<?php echo $date_filter; ?>"> 
                    </div>
                </div>
                <div class="text-right">
                    <a href="<?php echo site_url('payments/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Make Payment</a>
                </div>
            </div>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fund Type</th>
                    <th>Program/Vendor</th>
                    <th>Check Date</th>
                    <th>Check Number</th>
                    <th>Amount</th>
                    <th>Added Date</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    var permissions = <?php echo json_encode($perArr); ?>;
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
            autoWidth: false,
            processing: true,
            serverSide: true,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[6, "desc"]],
            ajax: {
                url: site_url + 'payments/get_payments',
                data: {
                    post_date_filter: post_date_filter
                },
            },
            columns: [
                {
                    data: "id",
                    visible: true,
                    sortable: false,
                },
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
                            return data
                        }
                    }
                },
                {
                    data: "check_date",
                    visible: true
                },
                {
                    data: "check_number",
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
                    data: "created",
                    visible: true,
                },
                {
                    data: "is_delete",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var action = '';
                        if ($.inArray('edit', permissions) !== -1) {
                            action += '<a href="' + site_url + 'payments/edit/' + btoa(full.id) + '" class="btn border-primary text-primary-600 btn-flat btn-icon btn-rounded btn-xs" title="Edit Payment"><i class="icon-pencil3"></i></a>';
                        }
                        if ($.inArray('delete', permissions) !== -1) {
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'payments/delete/' + btoa(full.id) + '" class="btn border-danger text-danger-600 btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Payment"><i class="icon-trash"></i></a>'
                        }
                        return action;
                    }
                }
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

    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this payment!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, delete it!"
        },
                function (isConfirm) {
                    if (isConfirm) {
                        window.location.href = $(e).attr('href');
                        return true;
                    } else {
                        return false;
                    }
                });
        return false;
    }
</script>
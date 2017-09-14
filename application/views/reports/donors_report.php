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
            <h4><i class="icon-coins"></i> <span class="text-semibold">Donors</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Donors</li>
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
                    <label>Post date filter: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                        <input type="text" name="post_date_filter" id="post_date_filter" class="form-control daterange-basic" value="<?php echo date('Y-m-d'); ?>"> 
                    </div>
                </div>
            </div>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <!--<th>#</th>-->
                    <th>Fund Type</th>
                    <th>Subcategory</th>
                    <th>Date</th>
                    <th>Post Date</th>
                    <th>Donar ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zip</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Refund</th>
                    <th>Pmt Method</th>
                    <th>Pmt Number</th>
                    <th>Memo</th>                    
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    var profile_img_url = '<?php echo base_url() . USER_IMAGES ?>';
    var data_table = post_date_filter = '';
    $(function () {
        bind();
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
        // $('.created_date').val('');
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
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[7, "desc"]],
//            ajax: site_url + 'reports/get_donors_reports',
            ajax: {
                url: site_url + 'reports/get_donors_reports',
                data: {
                    post_date_filter: post_date_filter
                },
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
                    visible: true,
                },
                {
                    data: "post_date",
                    visible: true,
                },
                {
                    data: "id",
                    visible: true,
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
                    visible: true
                },
                {
                    data: "refund",
                    visible: true
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
//        console.log(post_date_filter);
//        $(".datatable-basic").dataTable().fnDestroy();
        data_table.fnDestroy();
        bind();

    });
</script>
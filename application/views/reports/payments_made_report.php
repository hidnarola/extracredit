<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-cash4"></i> <span class="text-semibold">Payments Made Report</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Payments Made Report</li>
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
                    <th>Program Name</th>
                    <th>Payment</th>
                    <th>Check Date</th>                   
                    <th>Payment Number</th>                   
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    var permissions = <?php echo json_encode($perArr); ?>;
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
            ajax: site_url + 'reports/get_payments_made_report',
            columns: [
                {
                    data: "action_matters_campaign",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.is_vendor == 1) {
                            return full.vendor_name;
                        } else {
                            return data
                        }
                    }
                },
                {
                    data: "amount",
                    visible: true
                },
                {
                    data: "check_date",
                    visible: true,
                },
                {
                    data: "check_number",
                    visible: true
                },
            ]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    });
</script>
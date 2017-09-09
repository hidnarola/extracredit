<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-calculator3"></i> <span class="text-semibold">Awards 90%</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Awards 90%</li>
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
                    <th>Check Date</th>
                    <th>Check Number</th>
                    <th>ID</th>                   
                    <th>AMC</th>                   
                    <th>Address</th>                   
                    <th>City</th>
                    <th>State</th>
                    <th>Zip</th>                    
                    <th>Amount</th>                    
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    var permissions = <?php echo json_encode($perArr); ?>;
    var profile_img_url = '<?php echo base_url() . USER_IMAGES ?>';
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
            order: [[7, "desc"]],
            ajax: site_url + 'reports/get_vendor_admin_report',
            columns: [
                {
                    data: "check_date",
                    visible: true
                },
                {
                    data: "check_number",
                    visible: true                   
                },
                {
                    data: "id",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "vendor_name",
                    visible: true
                },
                {
                    data: "address",
                    visible: true
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
                    data: "amount",
                    visible: true
                }                
            ]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    });
</script>
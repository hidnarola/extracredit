<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-calculator3"></i> <span class="text-semibold">Accounts</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Accounts</li>
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
        <div class="panel-heading text-right">
            <a href="<?php echo site_url('accounts/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Account</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fund Type</th>
                    <th>AMC/Vendor</th>
                    <th>Email</th>
                    <th>Contact Name</th>
                    <th>City</th>
                    <th>Total Fund</th>
                    <th>Added Date</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    var profile_img_url = '<?php echo base_url() . USER_IMAGES ?>';
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
            order: [[7, "desc"]],
            ajax: site_url + 'accounts/get_accounts',
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
                    data: "email",
                    visible: true
                },
                {
                    data: "contact_name",
                    visible: true
                },
                {
                    data: "city",
                    visible: true
                },
                {
                    data: "total_fund",
                    visible: true,
                    render: function (data, type, full, meta) {
                        return '$' + full.total_fund;
                    }
                },
                {
                    data: "created",
                    visible: true,
                },
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
                    data: "is_delete",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var action = '';
                        if ($.inArray('edit', permissions) !== -1) {
                            action += '<a href="' + site_url + 'accounts/edit/' + btoa(full.id) + '" class="btn border-primary text-primary-600 btn-flat btn-icon btn-rounded btn-xs" title="Edit Account"><i class="icon-pencil3"></i></a>';
                        }
                        if ($.inArray('view', permissions) !== -1) {
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'accounts/transactions/' + btoa(full.id) + '" class="btn border-teal text-teal-600 btn-flat btn-icon btn-rounded btn-xs" title="View Transactions"><i class="icon-coins"></i></a>';
                        }
                        if ($.inArray('delete', permissions) !== -1) {
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'accounts/delete/' + btoa(full.id) + '" class="btn border-danger text-danger-600 btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Account"><i class="icon-trash"></i></a>'
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
    });

    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this account!",
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
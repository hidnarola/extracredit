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
            <a href="#" data-target="#import_modal" data-toggle="modal" class="btn bg-pink-400 btn-labeled"><b><i class="icon-file-upload2"></i></b> Import Account</a>
            <a href="<?php echo site_url('accounts/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Account</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Fund Type</th>
                    <th>Program/AMC</th>
                    <th>Contact Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Total Fund</th>
                    <th>Active</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<!-- Import Account modal -->
<div id="import_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo site_url('accounts/import_account') ?>" class="form-horizontal form-validate-jquery" id="import_donor_form" method="post" enctype="multipart/form-data">
                <div class="modal-header bg-teal">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h6 class="modal-title">Import Account</h6>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="control-label">Upload File</label>
                            <div class="media no-margin-top">
                                <div class="media-body">
                                    <input type="file" name="import_account" id="import_account" class="file-styled">
                                    <span class="help-block">Accepted formats: CSV. Max file size 2Mb</span>
                                    <span class="help-block"><code>File should be in this format </code><a href="<?php echo base_url(DEMO_CSV . 'account_demo.csv') ?>">Download Demo File</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-teal">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(".file-styled").uniform({
        fileButtonClass: 'action btn bg-blue'
    });
    var profile_img_url = '<?php echo base_url() . USER_IMAGES ?>';
    var permissions = <?php echo json_encode($perArr); ?>;
    $(function () {
        $('.datatable-basic').dataTable({
//            scrollX: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [],
            ajax: site_url + 'accounts/get_accounts',
            columns: [
                {
                    data: "is_delete",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var action = '';
                        action += '<ul class="icons-list">';
                        action += '<li class="dropdown">';
                        action += '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
                        action += '<i class="icon-menu9"></i>';
                        action += '</a>';
                        action += '<ul class="dropdown-menu dropdown-menu-right">';
                        action += '<li>';
                        if ($.inArray('edit', permissions) !== -1) {
                            action += '<a href="' + site_url + 'accounts/edit/' + btoa(full.id) + '" title="Edit Account"><i class="icon-pencil3"></i> Edit Account</a>';
                        }
                        if ($.inArray('view', permissions) !== -1) {
                            action += '<a href="' + site_url + 'accounts/transactions/' + btoa(full.id) + '" title="View Transactions"><i class="icon-coins"></i> View Transactions</a>';
                        }
                        if ($.inArray('view', permissions) !== -1) {
                            action += '<a href="' + site_url + 'accounts/transfer_account/' + btoa(full.id) + '" title="Transfer Money"><i class=" icon-arrow-right16"></i> Transfer Money</a>';
                        }
                        if ($.inArray('view', permissions) !== -1) {
                            action += '<a href="' + site_url + 'accounts/communication/' + btoa(full.id) + '" title="View Communication"><i class="icon-comment-discussion"></i> View Communication</a>';
                        }
                        if ($.inArray('delete', permissions) !== -1) {
                            action += '<a href="' + site_url + 'accounts/delete/' + btoa(full.id) + '" onclick="return confirm_alert(this)" title="Delete Account"><i class="icon-trash"></i> Delete Account</a>'
                        }
                        action += '</li>';
                        action += '</ul>';
                        action += '</li>';
                        action += '</ul>';
                        return action;
                    }
                },
                {
                    data: "fund_type",
                    visible: true,
                },
                {
                    data: "program_name",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.type == 1) {
                            return full.vendor_name;
                        } else if (data != '') {
                            return data
                        } else {
                            return full.action_matters_campaign;
                        }
                    }
                },
                {
                    data: "contact_names",
                    visible: true
                },
                {
                    data: "contact_emails",
                    visible: true
                },
                {
                    data: "phone",
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
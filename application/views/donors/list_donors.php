<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
<style>
    .btn-icon.btn-xs, .input-group-xs > .input-group-btn > .btn.btn-icon {padding-right: 6px;}
    .refund_row {background-color: rgba(253, 82, 82, 0.14);}    
</style>
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
            <div class="col-md-7">
                <code>Highlighted</code> rows represent refunded donations
            </div>
            <div class="col-md-5 text-right">
                <a href="#" data-target="#import_modal" data-toggle="modal" class="btn bg-pink-400 btn-labeled"><b><i class="icon-file-upload2"></i></b> Import Donor</a>
                <a href="<?php echo site_url('donors/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Donor</a>
            </div>
            <br/>
            <br/>
        </div>
        <div class="table-responsive">
            <table class="table datatable-basic">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Amount</th>
                        <th>Added Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<div id="import_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo site_url('donors/import_donor') ?>" class="form-horizontal form-validate-jquery" id="import_donor_form" method="post" enctype="multipart/form-data">
                <div class="modal-header bg-teal">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h6 class="modal-title">Import Donor</h6>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="control-label">Upload File</label>
                            <div class="media no-margin-top">
                                <div class="media-body">
                                    <input type="file" name="import_donor" id="import_donor" class="file-styled">
                                    <span class="help-block">Accepted formats: CSV. Max file size 2Mb</span>
                                    <span class="help-block"><code>File should be in this format <a href="<?php echo base_url(DEMO_CSV . 'donor_demo.csv') ?>">Download Demo File</a></code></span>
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

<!-- Guest View Modal -->
<div id="donor_view_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-teal-400 custom_modal_header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title text-center">Guest's Details</h6>
            </div>
            <div class="modal-body panel-body custom_scrollbar" id="donor_view_body" style="height: 600px;overflow-y: auto;">
            </div>
        </div>
    </div>
</div>
<script>
    $(".file-styled").uniform({
        fileButtonClass: 'action btn bg-blue'
    });
    var permissions = <?php echo json_encode($perArr); ?>;
    var compermissions = <?php echo json_encode($comperArr); ?>;
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
            order: [[6, "desc"]],
            ajax: site_url + 'donors/get_donors',
            columns: [
                {
                    data: "id",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "firstname",
                    visible: true,
                },
                {
                    data: "lastname",
                    visible: true,
                },
                {
                    data: "email",
                    visible: true
                },
                {
                    data: "city",
                    visible: true
                },
                {
                    data: "amount",
                    visible: true,
                    render: function (data, type, full, meta) {
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
                        if (full.refund == 0) {
                            if ($.inArray('edit', permissions) !== -1) {
                                action += '<a href="' + site_url + 'donors/edit/' + btoa(full.id) + '" class="btn border-primary text-primary-600 btn-flat btn-icon btn-rounded btn-xs" title="Edit Donor"><i class="icon-pencil3"></i></a>';
                            }
                            if ($.inArray('edit', permissions) !== -1) {
                                action += '&nbsp;&nbsp;<a href="' + site_url + 'donors/donations/' + btoa(full.id) + '" class="btn border-warning text-warning-600 btn-flat btn-icon btn-rounded btn-xs" title="View Donations"><i class="icon-credit-card2"></i></a>';
                            }
                            if ($.inArray('edit', permissions) !== -1) {
                                action += '&nbsp;&nbsp;<a href="javascript:void(0)" class="btn border-pink text-pink-600 btn-flat btn-icon btn-rounded btn-xs" title="Refund" data-id="' + btoa(full.id) + '" onclick="return refund_alert(this)"><i class="icon-share2"></i></a>';
                            }
                        }
                        if ($.inArray('view', permissions) !== -1) {
                            action += '&nbsp;&nbsp;<a href="javascript:void(0)" class="btn border-purple text-purple-600 btn-flat btn-icon btn-rounded btn-xs donor_view_btn" id=' + btoa(full.id) + ' title="View Details"><i class="icon-eye"></i></a>';
                        }
                        if ($.inArray('view', compermissions) !== -1) {
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'donors/communication/' + btoa(full.id) + '" class="btn border-info text-info-600 btn-flat btn-icon btn-rounded btn-xs" title="View Communication"><i class="icon-comment-discussion"></i></a>'
                        }
                        if ($.inArray('delete', permissions) !== -1) {
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'donors/delete/' + btoa(full.id) + '" class="btn border-danger text-danger-600 btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Donor"><i class="icon-trash"></i></a>'
                        }
                        return action;
                    }
                }
            ],
            createdRow: function (row, data, index) {
                if (data.refund == 1) {
                    $(row).addClass('refund_row');
                }
//                console.dir(data);
//                console.dir(row);
            }
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    });

    function refund_alert(e) {
        id = $(e).attr('data-id');
        $.ajax({
            url: site_url + 'donors/refund/' + id,
            data: {id: id},
            type: "POST",
            dataType: 'json',
            success: function (data) {
                if (data.type == 1) {
                    window.location.href = site_url + 'donors';
                } else {
                    swal({
                        title: "Refund Alert",
                        text: "There isn't sufficient funds available to process the refund",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#FF7043",
                        confirmButtonText: "Ok!"
                    });
                }
            }
        });
    }
    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this donor!",
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

    $(document).on('click', '.donor_view_btn', function () {
        $.ajax({
            url: site_url + 'donors/view_donor',
            type: "POST",
            data: {id: this.id},
            success: function (response) {
                $('#donor_view_body').html(response);
                $('#donor_view_modal').modal('show');
            }
        });
    });
</script>
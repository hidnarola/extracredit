<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
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
        <div class="panel-heading text-right">
            <a href="#" data-target="#import_modal" data-toggle="modal" class="btn bg-pink-400 btn-labeled"><b><i class="icon-file-upload2"></i></b> Import Donor</a>
            <a href="<?php echo site_url('donors/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Donor</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Program/AMC</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Added Date</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
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
                    <!--                    <h6 class="text-semibold">Upload File</h6>-->
                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="control-label">Upload File</label>
                            <div class="media no-margin-top">
                                <div class="media-body">
                                    <input type="file" name="import_donor" id="import_donor" class="file-styled">
                                    <span class="help-block">Accepted formats: CSV. Max file size 2Mb</span>
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
    var permissions = <?php echo json_encode($perArr); ?>;
    var compermissions = <?php echo json_encode($comperArr); ?>;
    console.log(permissions);
    var profile_img_url = '<?php echo base_url() . USER_IMAGES ?>';
    $(function () {
        $('.datatable-basic').dataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[8, "desc"]],
            ajax: site_url + 'donors/get_donors',
            columns: [
                {
                    data: "id",
                    visible: true,
                    sortable: false,
                },
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
                    data: "payment_type",
                    visible: true
                },
                {
                    data: "amount",
                    visible: true
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
                            action += '<a href="' + site_url + 'donors/edit/' + btoa(full.id) + '" class="btn border-primary text-primary-600 btn-flat btn-icon btn-rounded btn-xs" title="Edit Donor"><i class="icon-pencil3"></i></a>';
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
</script>
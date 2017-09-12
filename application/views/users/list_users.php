<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
<link rel="stylesheet" type="text/css" href="assets/css/jquery.fancybox.css?v=2.1.5" media="screen" />
<script type="text/javascript" src="assets/js/jquery.fancybox.js?v=2.1.5"></script>
<script type="text/javascript">
    $(function () {
        $('.fancybox').fancybox();
    });
</script>
<style>.fancybox-close:after {display: none;}
    .fancybox-nav span:after {display: none;}
</style>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-users4"></i> <span class="text-semibold">Users</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Users</li>
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
            <a href="<?php echo site_url('users/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-user-plus"></i></b> Add User</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Profile Picture</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Added Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<!-- View modal -->
<div id="user_permission_modal" class="modal fade" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="overflow-y: initial !important">
        <div class="modal-content">
            <div class="modal-header bg-teal-400 custom_modal_header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title text-center">User's Details</h6>
            </div>
            <div class="modal-body panel-body custom_scrollbar" id="user_permissions_view_body" style="height: 600px;overflow-y: auto;">
            </div>
        </div>
    </div>
</div>
<script>
    var permissions = <?php echo json_encode($perArr); ?>;
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
            order: [[5, "desc"]],
            ajax: site_url + 'users/get_users',
            columns: [
                {
                    data: "sr_no",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "profile_image",
                    visible: true,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var profile_img = '';
                        if (data != null) {
                            profile_img = '<a class="fancybox" href="' + profile_img_url + data + '"><img src="' + profile_img_url + data + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="' + full.firstname + '" class="img-circle"/></a>';
                        } else {
                            profile_img = '<a class="fancybox" href="assets/images/placeholder.jpg" data-fancybox-group="gallery" ><img src="assets/images/placeholder.jpg" height="55px" width="55px" alt="' + full.firstname + '" class="img-circle"/></a>';
                        }
                        return profile_img;
                    }
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
                    data: "email",
                    visible: true
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
                            status = '<span class="label bg-warning">Blocked</span>';
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
                        if (full.is_active == 1) {
                            if ($.inArray('edit', permissions) !== -1) {
                                action += '<a href="' + site_url + 'users/edit/' + btoa(full.id) + '" class="btn border-primary text-primary-600 btn-flat btn-icon btn-rounded btn-xs" title="Edit User"><i class="icon-pencil3"></i></a>';
                                action += '&nbsp;&nbsp;<a href="javascript:void(0)"  class="btn border-purple text-purple-600 btn-flat btn-icon btn-rounded btn-xs privilege_view_btn" id="' + btoa(full.id) + '" data-id=' + btoa(full.id) + ' title="View User"><i class="icon-eye"></i></a>';

                            }
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'users/block/' + btoa(full.id) + '" class="btn border-slate text-slate-600 btn-flat btn-icon btn-rounded btn-xs" onclick="return block_alert(this,\'block\')" title="Block User"><i class="icon-user-block"></i></a>'
                        } else {
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'users/block/' + btoa(full.id) + '" class="btn border-success text-success-600 btn-flat btn-icon btn-rounded btn-xs" title="Unblock User" onclick="return block_alert(this,\'unblock\')" ><i class="icon-user-check"></i></a>'
                        }
                        if ($.inArray('delete', permissions) !== -1) {
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'users/delete/' + btoa(full.id) + '" class="btn border-danger text-danger-600 btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete User"><i class="icon-trash"></i></a>'
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
            text: "You will not be able to recover this user!",
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
    function block_alert(e, type) {
        swal({
            title: "Are you sure?",
            text: "The user will be " + type + "ed!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, " + type + " it!"
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


    $(document).on('click', '.privilege_view_btn', function () {
        $.ajax({
            url: site_url + 'users/view_user',
            type: "POST",
            data: {id: this.id},
            success: function (response) {
                $('#user_permissions_view_body').html(response);
                $('#user_permission_modal').modal('show');
            }
        });
    });
</script>
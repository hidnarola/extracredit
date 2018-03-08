<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-notebook"></i> <span class="text-semibold">Contacts</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Contacts</li>
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
            <a href="#" data-target="#import_modal" data-toggle="modal" class="btn bg-pink-400 btn-labeled"><b><i class="icon-file-upload2"></i></b> Import Contact</a>
            <a href="<?php echo site_url('contacts/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Contact</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Website</th>
                    <th>Created</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<!-- Import Contact modal -->
<div id="import_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo site_url('contacts/import_contact') ?>" class="form-horizontal form-validate-jquery" id="import_donor_form" method="post" enctype="multipart/form-data">
                <div class="modal-header bg-teal">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h6 class="modal-title">Import Contact</h6>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="control-label">Upload File</label>
                            <div class="media no-margin-top">
                                <div class="media-body">
                                    <input type="file" name="import_contact" id="import_contact" class="file-styled">
                                    <span class="help-block">Accepted formats: CSV. Max file size 2Mb</span>
                                    <span class="help-block"><code>File should be in this format </code><a href="<?php echo base_url(DEMO_CSV . 'contact_demo.csv') ?>">Download Demo File</a></span>
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
<!-- Contact View Modal -->
<div id="contact_view_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-teal-400 custom_modal_header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title text-center">Contact's Details</h6>
            </div>
            <div class="modal-body panel-body custom_scrollbar" id="contact_view_body" style="height: 600px;overflow-y: auto;"></div>
        </div>
    </div>
</div>
<script>
    var permissions = <?php echo json_encode($perArr); ?>;
    $(function () {
        $(".file-styled").uniform({
            fileButtonClass: 'action btn bg-blue'
        });
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
            order: [],
            ajax: site_url + 'contacts/get_contacts',
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
                            action += '<a href="' + site_url + 'contacts/edit/' + btoa(full.id) + '" title="Edit Contact"><i class="icon-pencil3"></i> Edit Contact</a>';
                        }
                        if ($.inArray('view', permissions) !== -1) {
                            action += '<a href="javascript:void(0)" class="contact_view_btn" id=' + btoa(full.id) + ' title="View Contact"><i class="icon-eye"></i> View Contact</a>';
                        }
                        if ($.inArray('view', permissions) !== -1) {
                            action += '<a href="' + site_url + 'contacts/communication/' + btoa(full.id) + '" title="View Communication"><i class="icon-comment-discussion"></i> View Communication</a>';
                        }
                        if ($.inArray('delete', permissions) !== -1) {
                            action += '<a href="' + site_url + 'contacts/delete/' + btoa(full.id) + '" onclick="return confirm_alert(this)" title="Delete Contact"><i class="icon-trash"></i> Delete Contact</a>'
                        }
                        action += '</li>';
                        action += '</ul>';
                        action += '</li>';
                        action += '</ul>';
                        return action;
                    }
                },
                {
                    data: "name",
                    visible: true,
                },
                {
                    data: "email",
                    visible: true
                },
                {
                    data: "phone",
                    visible: true
                },
                {
                    data: "website",
                    visible: true,
                },
                {
                    data: "created",
                    visible: true,
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
            text: "You will not be able to recover this contact!",
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
    $(document).on('click', '.contact_view_btn', function () {
        $.ajax({
            url: site_url + 'contacts/view',
            type: "POST",
            data: {id: this.id},
            success: function (response) {
                $('#contact_view_body').html(response);
                $('#contact_view_modal').modal('show');
            }
        });
    });
</script>
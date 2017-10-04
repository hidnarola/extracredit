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
            <h4><i class="icon-people"></i> <span class="text-semibold">Guests</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Guests</li>
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
            <a href="#" data-target="#import_modal" data-toggle="modal" class="btn bg-pink-400 btn-labeled"><b><i class="icon-file-upload2"></i></b> Import Guest</a>
            <a href="<?php echo site_url('guests/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Guest</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Company</th>
                    <th>Email</th>
                    <th>Phone number</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>

<!-- Guest View Modal -->
<div id="guest_view_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-teal-400 custom_modal_header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title text-center">Guest's Details</h6>
            </div>
            <div class="modal-body panel-body custom_scrollbar" id="guest_view_body" style="height: 600px;overflow-y: auto;">
            </div>
        </div>
    </div>
</div>

<div id="import_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo site_url('guests/import_guest') ?>" class="form-horizontal form-validate-jquery" id="import_guest_form" method="post" enctype="multipart/form-data">
                <div class="modal-header bg-teal">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h6 class="modal-title">Import Guest</h6>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="control-label">Upload File</label>
                            <div class="media no-margin-top">
                                <div class="media-body">
                                    <input type="file" name="import_guest" id="import_guest" class="file-styled">
                                    <span class="help-block">Accepted formats: CSV. Max file size 2Mb</span>
                                    <span class="help-block"><code>File should be in this format <a href="<?php echo base_url(DEMO_CSV . 'guest_demo.csv') ?>">Download Demo File</a></code></span>
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
    var logo_img_url = '<?php echo base_url() . GUEST_IMAGES ?>';
    $(function () {
        $('.datatable-basic').dataTable({
            scrollX: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            "bPaginate": true,
            "aaSorting": [],
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            ajax: site_url + 'guests/get_guests',
            columns: [
                {
                    data: "logo",
                    visible: true,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var logo = '';
                        if (data != null) {
                            logo = '<a class="fancybox" href="' + logo_img_url + data + '"><img src="' + logo_img_url + data + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="' + full.firstname + '" class="img-circle"/></a>';
                        } else {
                            logo = '<a class="fancybox" href="assets/images/placeholder.jpg" data-fancybox-group="gallery" ><img src="assets/images/placeholder.jpg" height="55px" width="55px" alt="' + full.firstname + '" class="img-circle"/></a>';
                        }
                        return logo;
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
                    data: "companyname",
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
                            action += '<a href="' + site_url + 'guests/edit/' + btoa(full.id) + '" title="Edit Guest"><i class="icon-pencil3"></i> Edit Guest</a>';
                        }
                        if ($.inArray('view', permissions) !== -1) {
                            action += '<a href="javascript:void(0)" class="guest_view_btn" id=' + btoa(full.id) + ' title="View Details"><i class="icon-eye"></i> View Details</a>';
                        }
                        if ($.inArray('view', compermissions) !== -1) {
                            action += '<a href="' + site_url + 'guests/communication/' + btoa(full.id) + '" title="View Communication"><i class="icon-comment-discussion"></i> View Communication</a>'
                        }
                        if ($.inArray('delete', permissions) !== -1) {
                            action += '<a href="' + site_url + 'guests/delete/' + btoa(full.id) + '" onclick="return confirm_alert(this)" title="Delete Guest"><i class="icon-trash"></i> Delete Guest</a>'
                        }
                        action += '</li>';
                        action += '</ul>';
                        action += '</li>';
                        action += '</ul>';
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
            text: "You will not be able to recover this guest!",
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
    $(document).on('click', '.guest_view_btn', function () {
        $.ajax({
            url: site_url + 'guests/view_guest',
            type: "POST",
            data: {id: this.id},
            success: function (response) {
                $('#guest_view_body').html(response);
                $('#guest_view_modal').modal('show');
            }
        });
    });
</script>
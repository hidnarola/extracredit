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
            <h4><i class="icon-comment-discussion"></i> <span class="text-semibold">Donors Communication</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('donors'); ?>"><i class="icon-people position-left"></i> Donors</a></li>
            <li class="active">Donors Conversation</li>
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
            <a href="<?php echo site_url('donors/add_communication' . '/' . $id); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Conversation</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Media</th>  
                    <th>Subject</th>                   
                    <th>Communication Date</th>                   
                    <th>Follow Up Date</th>
                    <th>Note</th>                   
                    <th>Added Date</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<div id="modalviewConversation" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-teal">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title">View Communication</h6>
            </div>

            <div class="modal-body">
                <table class="table table-borderless table-xs content-group-sm">
                    <tbody>
                        <tr>
                            <td><i class="icon-address-book3 position-left"></i> <b>Subject:</b></td>
                            <td class="text-right"><span class="pull-right subject_value">Singular app</span></td>
                        </tr>
                        <tr>
                            <td><i class="icon-alarm-add position-left"></i> <b>Communication date:</b></td>
                            <td class="text-right communication_date">12 May, 2015</td>
                        </tr>
                        <tr>
                            <td><i class="icon-alarm-check position-left"></i> <b>Follow Up date:</b></td>
                            <td class="text-right follow_up_date">25 Feb, 2015</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <h6 class="text-semibold">Note:</h6>
                <p class="note"></p>
                <hr>
                <div class="attached_media" style="display: none;">
                    <h4>Attached Media</h4>
                    <div class="media-logo"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn bg-teal" data-dismiss="modal">Close</button>
                <!--<button type="button" class="btn btn-info">Save changes</button>-->
            </div>
        </div>
    </div>
</div>
<script>
    var permissions = <?php echo json_encode($perArr); ?>;
    var logo_img_url = '<?php echo base_url() . COMMUNICATION_IMAGES ?>';
    var guest_id = '<?php echo $id; ?>';
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
            order: [[6, "desc"]],
            ajax: site_url + 'donors/get_donors_communication/' + guest_id,
            columns: [
                {
                    data: "id",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "media",
                    visible: true,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var logo = '';
                        var valid_extensions = /(\.jpg|\.jpeg|\.png)$/i;
                        if (data != null) {
                            if (valid_extensions.test(data)) {
                                logo = '<a class="fancybox" href="' + logo_img_url + data + '"><img src="' + logo_img_url + data + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="' + full.firstname + '" class="img-circle"/></a>';
                            } else {
                                logo = '<a class="fancybox" target="_blank" href="' + logo_img_url + data + '" data-fancybox-group="gallery" ><img src="assets/images/default_file.png" height="55px" width="55px" alt="' + full.firstname + '" class="img-circle"/></a>';
                            }
                        } else {
                            logo = '<a class="fancybox" href="assets/images/placeholder.jpg" data-fancybox-group="gallery" ><img src="assets/images/placeholder.jpg" height="55px" width="55px" alt="' + full.firstname + '" class="img-circle"/></a>';
                        }
                        return logo;
                    }
                },
                {
                    data: "subject",
                    visible: true
                },
                {
                    data: "communication_date",
                    visible: true
                },
                {
                    data: "follow_up_date",
                    visible: true
                },
                {
                    data: "note",
                    visible: true,
                    render: function (data, type, full, meta) {
                        var note = '';
                        if (full.note.length > 20) {
                            if ($.inArray('view', permissions) !== -1) {
                                note = '<a href="javascript:void(0)"  data-toggle="modal" data-target="#modalviewConversation" data-id=' + btoa(full.id) + ' onclick="return view_communication(this)" title="View Conversation">' + full.note.substr(0, 20) + '...</a>';
                            } else {
                                note = '<a href="javascript:void(0)" data-id=' + btoa(full.id) + 'title="View Conversation">' + full.note.substr(0, 20) + '...</a>';
                            }
                        } else {
                            note = full.note;
                        }
                        return note;
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
                        if ($.inArray('edit', permissions) !== -1) {
                            action += '<a href="' + site_url + 'donors/add_communication/' + btoa(full.donor_id) + '/' + btoa(full.id) + '" class="btn border-primary text-primary-600 btn-flat btn-icon btn-rounded btn-xs" title="Edit Donor Communication"><i class="icon-pencil3"></i></a>';
                        }
                        if ($.inArray('view', permissions) !== -1) {
                            action += '&nbsp;&nbsp;<a href="javascript:void(0)"  data-toggle="modal" data-target="#modalviewConversation" class="btn border-purple text-purple-600 btn-flat btn-icon btn-rounded btn-xs" data-id=' + btoa(full.id) + ' onclick="return view_communication(this)" title="View Conversation"><i class="icon-eye"></i></a>'
                        }
                        if ($.inArray('delete', permissions) !== -1) {
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'donors/delete_communication/' + btoa(full.id) + '" class="btn border-danger text-danger-600 btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Donors Communication"><i class="icon-trash"></i></a>'
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

    function view_communication(e) {
        var url = site_url + 'donors/get_communication_by_id';
        var id = $(e).attr('data-id');
        $.ajax({
            type: 'POST',
            url: url,
            async: false,
            dataType: 'JSON',
            data: {id: id},
            success: function (data) {
                console.log(data);
                $('.note').html(data.note);
                $('.subject_value').html(data.subject);
                $('.communication_date').html(data.communication_date);
                $('.follow_up_date').html(data.follow_up_date);
                var valid_extensions = /(\.jpg|\.jpeg|\.png)$/i;
                if (data.media != null) {
                    if (valid_extensions.test(data.media)) {
                        logo = '<a class="fancybox" href="' + logo_img_url + data.media + '"><img src="' + logo_img_url + data.media + '" style="width: 58px; height: 58px; border-radius: 2px;" class="img-circle"/></a>';
                    } else {
                        logo = '<a class="fancybox" target="_blank" href="' + logo_img_url + data.media + '" data-fancybox-group="gallery" ><img src="assets/images/default_file.png" height="55px" width="55px" class="img-circle"/></a>';
                    }
                    $('.attached_media').css('display', 'block');
                    $('.media-logo').html(logo);
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
</script>
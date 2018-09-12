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
            <h4><i class="icon-bubbles9"></i> <span class="text-semibold">Communication Manager</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Communication Manager</li>
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
                    <th>Name</th>
                    <th>Category</th>
                    <th>Follow Up Date</th>                    
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
                            <td class="text-right"><span class="pull-right subject_value"></span></td>
                        </tr>
                        <tr>
                            <td><i class="icon-alarm-add position-left"></i> <b>Communication date:</b></td>
                            <td class="text-right communication_date"></td>
                        </tr>
                        <tr>
                            <td><i class="icon-alarm-check position-left"></i> <b>Follow Up date:</b></td>
                            <td class="text-right follow_up_date"></td>
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
                <a href="" id="check_com_link" onclick="return confirm_alert(this)" class="btn border-warning text-warning-600 btn-flat btn-icon btn-rounded btn-xs" title="Check communication"><i class="icon-checkmark4"></i></a>
                <a href="" id="add_com_link" class="btn btn-primary">Add notes</a>
                <button type="button" class="btn bg-teal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
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
            order: [[2, "asc"]],
            ajax: site_url + 'communication_manager/get_communication_manager',
            columns: [
                {
                    data: "firstlast",
                    visible: true
                },
                {
                    data: "category",
                    visible: true
                },
                {
                    data: "follow_up_date",
                    visible: true
                },
                {
                    data: "is_delete",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var str = full.category;
                        var res = str.toLowerCase() + 's';
//                        return '<a href="' + site_url + 'communication_manager/check_communication/' + btoa(full.id) + '" onclick="return confirm_alert(this)" class="btn border-warning text-warning-600 btn-flat btn-icon btn-rounded btn-xs" title="Check communication"><i class="icon-checkmark4"></i></a>';
                        return '<a href="javascript:void(0)" data-toggle="modal" data-category="' + res + '" data-target="#modalviewConversation" data-id="' + btoa(full.id) + '" data-commid="' + btoa(full.communication_id) + '" class="btn border-warning text-warning-600 btn-flat btn-icon btn-rounded btn-xs" onclick="return view_communication(this)" title="Check communication"><i class="icon-checkmark4"></i></a>';
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
            text: "You will not be able to recover this communication!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, check it!"
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

    function view_communication(e) {
        var url = site_url + 'communication_manager/get_communication_by_id';
        var id = $(e).attr('data-id');
        var commid = $(e).attr('data-commid');
        var category = $(e).attr('data-category');
        $('.note').html('');
        $('.subject_value').html('');
        $('.communication_date').html('');
        $('.follow_up_date').html('');
        $('.media-logo').html('');
        $('#check_com_link').attr('href', '');
        $('#add_com_link').attr('href', '');
        $.ajax({
            type: 'POST',
            url: url,
            async: false,
            dataType: 'JSON',
            data: {id: commid},
            success: function (data) {
                console.log(data);
                $('#check_com_link').attr('href', site_url + 'communication_manager/check_communication/' + id);
                $('#add_com_link').attr('href', site_url + category + '/add_communication/' + btoa(data.type_id) + '/' + btoa(data.id));
                $('.note').html(data.note);
                $('.subject_value').html(data.subject);
                $('.communication_date').html(data.communication_date);
                $('.follow_up_date').html(data.follow_up_date);
                $('.type_value').html(data.follow_up_date);
                var valid_extensions = /(\.jpg|\.jpeg|\.png)$/i;

                if (data.media != null) {
                    if (valid_extensions.test(data.media)) {
                        logo = '<a class="fancybox" href="' + logo_img_url + data.media + '"><img src="' + logo_img_url + data.media + '" style="width: 58px; height: 58px; border-radius: 2px;" class="img-circle"/></a>';
                    } else {
                        logo = '<a class="fancybox" target="_blank" href="' + logo_img_url + data.media + '" data-fancybox-group="gallery" ><img src="assets/images/default_file.png" height="55px" width="55px" class="img-circle"/></a>';
                    }
                    $('.media-logo').html(logo);
                }
            }
        });
    }
</script>
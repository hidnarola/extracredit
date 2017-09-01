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
            <a href="<?php echo site_url('guests/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Guest</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Logo</th>
                    <th>Program/AMC</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Companyname</th>
                    <th>Email</th>
                    <th>City</th>
                    <!--<th>Invite Date</th>-->
                    <th>Added Date</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    var logo_img_url = '<?php echo base_url() . GUEST_IMAGES ?>';
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
            order: [[7, "desc"]],
            ajax: site_url + 'guests/get_guests',
            columns: [
                {
                    data: "id",
                    visible: true,
                    sortable: false,
                },
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
                    data: "companyname",
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
//                {
//                    data: "invite_date",
//                    visible: true
//                },
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
                        action += '<a href="' + site_url + 'guests/edit/' + btoa(full.id) + '" class="btn border-primary text-primary-600 btn-flat btn-icon btn-rounded btn-xs" title="Edit Donor"><i class="icon-pencil3"></i></a>';
                        action += '&nbsp;&nbsp;<a href="' + site_url + 'guests/conversation/' + btoa(full.id) + '" class="btn border-info text-info-600 btn-flat btn-icon btn-rounded btn-xs" title="View Conversation"><i class="icon-comment-discussion"></i></a>'
                        action += '&nbsp;&nbsp;<a href="' + site_url + 'guests/delete/' + btoa(full.id) + '" class="btn border-danger text-danger-600 btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Donor"><i class="icon-trash"></i></a>'
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
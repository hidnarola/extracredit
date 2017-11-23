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
            <h4><i class="icon-coins"></i> <span class="text-semibold"><?php echo $donor['firstname'] . '\'s Donations'; ?></span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('donors'); ?>"><i class="icon-coins position-left"></i> Donors</a></li>
            <li class="active">Donations</li>
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
            <a href="<?php echo site_url('donors/add_donation/' . base64_encode($donor['id'])); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Donation</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Program</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Post Date</th>
                    <th>Payment Number</th>
                    <th>Payment Type</th>
                    <th>memo</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
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
            order: [[2, "ASC"]],
            ajax: site_url + 'donors/get_donations/<?php echo base64_encode($donor['id']) ?>',
            columns: [
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
                    data: "amount",
                    visible: true,
                    render: function (data, type, full, meta) {
                        return '$' + full.amount;
                    }
                },
                {
                    data: "date",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.date == '01/01/1970') {
                            return '-';
                        } else {
                            return full.date;
                        }
                    }
                },
                {
                    data: "post_date",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.date == '01/01/1970') {
                            return '-';
                        } else {
                            return full.post_date;
                        }
                    }
                },
                {
                    data: "payment_number",
                    visible: true
                },
                {
                    data: "payment_type",
                    visible: true,
                },
                {
                    data: "memo",
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
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'donors/edit_donation/<?php echo base64_encode($donor['id']) ?>/' + btoa(full.id) + '" class="btn border-primary text-primary-600 btn-flat btn-icon btn-rounded btn-xs" title="Edit Donation"><i class="icon-pencil3"></i></a>'
                        }
                        if (full.is_refund == 0) {
                            if ($.inArray('edit', permissions) !== -1) {
                                action += '&nbsp;&nbsp;<a href="javascript:void(0)" title="Refund" data-account-id="' + btoa(full.account_id) + '" data-id="' + btoa(full.id) + '" onclick="return refund_alert(this)" class="btn border-warning text-warning-600 btn-flat btn-icon btn-rounded btn-xs"><i class="icon-share2"></i></a>';
                            }
                        }
                        return action;
                    }
                }
            ],
            createdRow: function (row, data, index) {
                if (data.is_refund == 1) {
                    $(row).addClass('refund_row');
                }
                console.dir(data);
//                console.dir(row);
            }
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    });
    function refund_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this refund!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, refund it!"
        },
                function (isConfirm) {
                    if (isConfirm) {
                        id = $(e).attr('data-id');
                        account_id = $(e).attr('data-account-id');
                        $.ajax({
                            url: site_url + 'donors/donation_refund/' + id,
                            data: {id: id, account_id: account_id},
                            type: "POST",
                            dataType: 'json',
                            success: function (data) {
                                if (data.type == 1) {
                                    window.location.href = site_url + 'donors/donations/<?php echo base64_encode($donor['id']) ?>';
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
                });
    }
</script>
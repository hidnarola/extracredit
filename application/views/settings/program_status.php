<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Program/AMC Status</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home') ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('settings') ?>"><i class="icon-gear position-left"></i> Settings</a></li>
            <li class="active">Program Status</li>
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
                    <button Status="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->flashdata('success') ?></strong>
                </div>
            <?php } ?>
            <?php if ($this->session->flashdata('error')) {
                ?>
                <div class="alert alert-danger hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->flashdata('error') ?></strong>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4" id="program_status_row">
            <form method="POST" class="form-validate-jquery" id="add_programstatus_form" name="add_programstatus_form">
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">Add/Update Program Status</h5>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-material has-feedback">
                                    <label class="required">Program Status </label>
                                    <input type="text" class="form-control" name="program_status" id="program_status" required="required">
                                    <?php
                                    echo '<label id="program_status-error" class="validation-error-label" for="program_status">' . form_error('program_status') . '</label>';
                                    ?>
                                    <input type="hidden" name="program_status_id" id="program_status_id">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success custom_save_button" id="programstatus_submit_btn">Save</button>
                                    <button type="button" class="btn btn-default custom_cancel_button" onclick="cancel_click()">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-8">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">Program/AMC Status List</h5>
                </div>
                <table class="table datatable-basic">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Status</th>
                            <th>Added Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($program_status as $key => $val) { ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td><?php echo $val['status']; ?></td>
                                <td><?php echo date('d,M Y', strtotime($val['created'])); ?></td>
                                <td>
                                    <a id="edit_<?php echo base64_encode($val['id']) ?>" class="btn border-primary text-primary-600 btn-flat btn-icon btn-rounded btn-xs edit" title="Edit Program Status"><i class="icon-pencil3"></i></a>
                                    &nbsp;&nbsp;<a href="<?php echo site_url('settings/delete_programstatus/' . base64_encode($val['id'])) ?>" class="btn border-danger text-danger-600 btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Program Status"><i class="icon-trash"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script type="text/javascript">
    $(function () {
        $('.datatable-basic').dataTable({
            autoWidth: false,
            processing: true,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'},
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        });
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    });
    //-- Validate Program status form
    $("#add_programstatus_form").validate({
        ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
        errorClass: 'validation-error-label',
        successClass: 'validation-valid-label',
        highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        },
        unhighlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        },
        // Different components require proper error label placement
        errorPlacement: function (error, element) {

            // Styled checkboxes, radios, bootstrap switch
            if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container')) {
                if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                    error.appendTo(element.parent().parent().parent().parent());
                }
                else {
                    error.appendTo(element.parent().parent().parent().parent().parent());
                }
            }

            // Unstyled checkboxes, radios
            else if (element.parents('div').hasClass('checkbox') || element.parents('div').hasClass('radio')) {
                error.appendTo(element.parent().parent().parent());
            }

            // Input with icons and Select2
            else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                error.appendTo(element.parent());
            }

            // Inline checkboxes, radios
            else if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                error.appendTo(element.parent().parent());
            }

            // Input group, styled file input
            else if (element.parent().hasClass('uploader') || element.parents().hasClass('input-group')) {
                error.appendTo(element.parent().parent());
            }

            else {
                error.insertAfter(element);
            }
        },
        validClass: "validation-valid-label",
        success: function (label) {
            label.addClass("validation-valid-label")
        },
        rules: {
            program_status: {
                required: true,
                remote: site_url + "settings/check_program_status/",
            },
        },
        messages: {
            program_status: {
                remote: $.validator.format("This program status already exist!")
            }
        },
        submitHandler: function (form) {
            $('#programstatus_submit_btn').attr('disabled', true);
            form.submit();
        }
    });
    //-- This function is used to edit particular records
    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id').replace('edit_', '');
        var url = site_url + 'settings/get_program_status_by_id';
        $('#custom_loading').removeClass('hide');
        $('#custom_loading img').addClass('hide');
//        $('#program_status_row').css('z-index', '999999');
        $.ajax({
            type: 'POST',
            url: url,
            async: false,
            dataType: 'JSON',
            data: {id: id},
            success: function (data) {
                $('#program_status').val(data.status);
                $('#program_status_id').val(data.id);
                $("#program_status").rules("add", {
                    remote: site_url + "settings/check_program_status/" + data.id,
                    messages: {
                        remote: $.validator.format("This program status already exist!")
                    }
                });
                $("#add_programstatus_form").validate().resetForm();
                $('html, body').animate({scrollTop: 0}, 500);
                setTimeout(function () {
                    $('body').css('overflow', 'hidden');
                }, 500);
            }
        });
    });

    function cancel_click() {
        $('#custom_loading').addClass('hide');
        $('#custom_loading img').removeClass('hide');
//        $('#program_status_row').css('z-index', '0');
        $('#program_status').val('');
        $('#program_status_id').val('');
        $("#program_status").rules("add", {
            remote: site_url + "settings/check_program_status/",
            messages: {
                remote: $.validator.format("This program status already exist!")
            }
        });
        $('#program_status').valid();
        $("#add_programstatus_form").validate().resetForm();
        $('body').css('overflow', 'auto');
    }
    //-- Confirmation alert for delete program status
    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this Program Status!",
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
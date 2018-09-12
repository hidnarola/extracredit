<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Program Types</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home') ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('settings') ?>"><i class="icon-gear position-left"></i> Settings</a></li>
            <li class="active">Program Types</li>
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
        <div class="col-md-4" id="program_type_row">
            <form method="POST" class="form-validate-jquery" id="add_programtype_form" name="add_programtype_form">
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">Add/Update Program Type</h5>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-material has-feedback">
                                    <label class="required">Program Type </label>
                                    <input type="text" class="form-control" name="program_type" id="program_type" required="required">
                                    <?php
                                    echo '<label id="program_type-error" class="validation-error-label" for="program_type">' . form_error('program_type') . '</label>';
                                    ?>
                                    <input type="hidden" name="program_type_id" id="program_type_id">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <button type="submit" name="save" class="btn bg-teal custom_save_button" id="programtype_submit_btn">Save<i class="icon-arrow-right14 position-right"></i></button>
                                    <button type="button" class="btn border-slate btn-flat cancel-btn custom_cancel_button" onclick="cancel_click()">Cancel</button>
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
                    <h5 class="panel-title">Program Types List</h5>
                </div>
                <table class="table datatable-basic">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Added Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($program_types as $key => $val) { ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td><?php echo $val['type']; ?></td>
                                <td><?php echo date('m/d/Y', strtotime($val['created'])); ?></td>
                                <td>
                                    <?php if (in_array('edit', $perArr)) { ?>
                                        <a id="edit_<?php echo base64_encode($val['id']) ?>" class="btn border-primary text-primary-600 btn-flat btn-icon btn-rounded btn-xs edit" title="Edit Program Type"><i class="icon-pencil3"></i></a>
                                    <?php } ?>
                                    <?php if (in_array('delete', $perArr)) { ?>
                                        &nbsp;&nbsp;<a href="<?php echo site_url('settings/delete_programtype/' . base64_encode($val['id'])) ?>" class="btn border-danger text-danger-600 btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Program Type"><i class="icon-trash"></i></a>
                                        <?php } ?>
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
    //-- Validate Program type form
    $("#add_programtype_form").validate({
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
                } else {
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
            } else {
                error.insertAfter(element);
            }
        },
        validClass: "validation-valid-label",
        success: function (label) {
            label.addClass("validation-valid-label")
        },
        rules: {
            program_type: {
                required: true,
                remote: site_url + "settings/check_program_type/",
            },
        },
        messages: {
            program_type: {
                remote: $.validator.format("This program type already exist!")
            }
        },
        submitHandler: function (form) {
            $('#programtype_submit_btn').attr('disabled', true);
            form.submit();
        }
    });
    //-- This function is used to edit particular records
    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id').replace('edit_', '');
        var url = site_url + 'settings/get_program_type_by_id';
        $('#custom_loading').removeClass('hide');
        $('#custom_loading img').addClass('hide');
//        $('#program_type_row').css('z-index', '999999');
        $.ajax({
            type: 'POST',
            url: url,
            async: false,
            dataType: 'JSON',
            data: {id: id},
            success: function (data) {
                $('#program_type').val(data.type);
                $('#program_type_id').val(data.id);
                $("#program_type").rules("add", {
                    remote: site_url + "settings/check_program_type/" + data.id,
                    messages: {
                        remote: $.validator.format("This program type already exist!")
                    }
                });
                $("#add_programtype_form").validate().resetForm();
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
//        $('#program_type_row').css('z-index', '0');
        $('#program_type').val('');
        $('#program_type_id').val('');
        $("#program_type").rules("add", {
            remote: site_url + "settings/check_program_type/",
            messages: {
                remote: $.validator.format("This program type already exist!")
            }
        });
        $('#program_type').valid();
        $("#add_programtype_form").validate().resetForm();
        $('body').css('overflow', 'auto');
    }
    //-- Confirmation alert for delete program type
    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this Program Type!",
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
//save reminder when user navigate away from a record that user have been working on
var form_changes = false;
$(document).ready(function () {
	$("form").on("change", ":input, select", function () {
        form_changes = true;
    });
    $('form').submit(function () {
        form_changes = false;
    });
});

window.onbeforeunload = function () {
    if (form_changes) {
        return true; // you can make this dynamic, ofcourse...
    } else {
        return undefined;
    }
};
</script>
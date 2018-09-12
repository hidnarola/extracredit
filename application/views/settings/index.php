<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Settings</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home') ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Settings</li>
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
    <div class="panel panel-flat">
        <div class="panel-body">
            <form class="form-horizontal form-validate-jquery" action="" id="settings_form" method="post" enctype="multipart/form-data">
                <fieldset class="content-group">
                    <legend class="text-bold">Donation Split Settings</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Admin Donation(Percentage)<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <input type="number" name="admin-donation-percent" id="admin-donation-percent" placeholder="Enter Admin Donation(%)" class="form-control" value="<?php echo ($settings) ? $settings['admin-donation-percent'] : set_value('admin-donation-percent'); ?>" required="required"/>
                            <?php
                            echo '<label id="admin-donation-percent-error" class="validation-error-label" for="admin-donation-percent">' . form_error('admin-donation-percent') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Program/campaign Donation(Percentage)<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <input type="number" name="program-donation-percent" id="program-donation-percent" placeholder="Enter Program Donation(%)" class="form-control" value="<?php echo ($settings) ? $settings['program-donation-percent'] : set_value('program-donation-percent'); ?>" required="required" readonly/>
                            <?php
                            echo '<label id="program-donation-percent-error" class="validation-error-label" for="program-donation-percent">' . form_error('program-donation-percent') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group">
                    <div class="col-lg-12">
                        <button type="submit" name="save" class="btn bg-teal custom_save_button" id="settings_submit_btn">Save<i class="icon-arrow-right14 position-right"></i></button>
                        <button type="button" class="btn border-slate btn-flat cancel-btn custom_cancel_button" onclick="window.history.back()">Cancel</button>
                    </div>
                </div>  
<!--                <div class="form-group">
                    <div class="col-lg-12">
                        <button class="btn btn-success" type="submit" id="settings_submit_btn">Save <i class="icon-arrow-right14 position-right"></i></button>
                    </div>
                </div>-->
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script type="text/javascript">

    //-- Admin donation & Program donation key change event
    $("#admin-donation-percent").on("keyup keydown change", function (event) {
        if ($(this).val() != '' && Number($(this).val()) >= 0 && Number($(this).val()) <= 100) {
            /*
             var num = parseFloat($(this).val());
             var cleanNum = num.toFixed(2);
             $(this).val(cleanNum); */

            var p_amount = 100 - $(this).val();
            $('#program-donation-percent').val(p_amount);
        }
    });
    /*
     $('#program-donation-percent').focusout(function () {
     if ($(this).val() != '' && Number($(this).val()) >= 0 && Number($(this).val()) <= 100) {
     var num = parseFloat($(this).val());
     var cleanNum = num.toFixed(2);
     $(this).val(cleanNum);
     
     var p_amount = 100 - cleanNum;
     $('#admin-donation-percent').val(p_amount);
     }
     });
     */
    $("#settings_form").validate({
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
            'admin-donation-percent': {
                required: true,
                positiveNumber: true,
                max: 100
            },
            'program-donation-percent': {
                required: true,
                positiveNumber: true,
                max: 100
            },
        },
        submitHandler: function (form) {
            var total_donation = parseFloat($('#admin-donation-percent').val()) + parseFloat($('#program-donation-percent').val());
            console.log('total', total_donation);
            if (total_donation == 100) {
                $('#settings_submit_btn').attr('disabled', true);
                form.submit();
            } else {
                swal({
                    title: "Oops...",
                    text: "You have entered invalid donation amount!",
                    confirmButtonColor: "#EF5350",
                    type: "error"
                });
                return false;
            }
        }
    });

    /*Validator method for positive number*/
    $.validator.addMethod('positiveNumber', function (value) {
        return Number(value) >= 0;
    }, 'Please enter positive number');

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
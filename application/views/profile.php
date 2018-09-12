<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>

<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <span class="text-semibold">Profile</span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Profile</li>
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
                <?php
            } else if ($this->session->flashdata('error')) {
                ?>
                <div class="alert alert-danger hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->flashdata('error') ?></strong>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <form class="form-horizontal form-validate-jquery" action="<?php echo site_url('home/profile') ?>" id="profile_form" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-lg-3">Profile Image</label>
                            <div class="col-lg-9">
                                <div class="media no-margin-top">
                                    <div class="media-left" id="image_preview_div">
                                        <?php if ($this->session->userdata('extracredit_user')['profile_image'] != '') { ?>
                                            <img src="<?php echo USER_IMAGES . $this->session->userdata('extracredit_user')['profile_image']; ?>" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                        <?php } else {
                                            ?>
                                            <img src="assets/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                        <?php } ?>
                                    </div>

                                    <div class="media-body">
                                        <input type="file" name="profile_image" id="profile_image" class="file-styled" onchange="readURL(this);ValidateSingleInput(this);">
                                        <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>
                                    </div>
                                </div>
                                <?php
                                if (isset($profile_image_validation))
                                    echo '<label id="profile_image-error" class="validation-error-label" for="profile_image">' . $profile_image_validation . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">First Name <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="firstname" id="firstname" placeholder="Enter First Name" class="form-control" required="required" value="<?php echo $this->session->userdata('extracredit_user')['firstname']; ?>">
                                <?php
                                echo '<label id="firstname-error" class="validation-error-label" for="firstname">' . form_error('firstname') . '</label>';
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Last Name </label>
                            <div class="col-lg-9">
                                <input type="text" name="lastname" id="lastname" placeholder="Enter Last Name" class="form-control" value="<?php echo $this->session->userdata('extracredit_user')['lastname']; ?>">
                                <?php
                                echo '<label id="lastname-error" class="validation-error-label" for="lastname">' . form_error('lastname') . '</label>';
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Email <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" disabled value="<?php echo $this->session->userdata('extracredit_user')['email']; ?>">
                            </div>
                        </div>
                        <div class="text-right col-lg-12">
                            <button class="btn btn-success" type="submit" id="update_profile">Save <i class="icon-arrow-right14 position-right"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <form class="form-horizontal form-validate-jquery" action="<?php echo site_url('home/update_password') ?>" id="change_password_form" method="post">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Old Password <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="password" name="old_password" id="old_password" placeholder="Enter Password" class="form-control" required="required">
                                <?php
                                echo '<label id="old_password-error" class="validation-error-label" for="old_password">' . form_error('old_password') . '</label>';
                                ?>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Password <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="password" name="password" id="password" placeholder="Enter Password" class="form-control" required="required">
                                <?php
                                echo '<label id="password-error" class="validation-error-label" for="password">' . form_error('password') . '</label>';
                                ?>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Enter Confirm Password" class="form-control" required="required">

                                <?php
                                echo '<label id="confirm_password-error" class="validation-error-label" for="confirm_password">' . form_error('confirm_password') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="text-right col-lg-12">
                            <button class="btn btn-success" id="update_password" type="submit">Update Password <i class="icon-arrow-right14 position-right"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<div id="validation_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-teal-400">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title"></h6>
            </div>
            <div class="modal-body panel-body validation_alert">
                <label></label>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // Styled file input
    $(".file-styled").uniform({
        fileButtonClass: 'action btn bg-blue'
    });
    $("#profile_form").validate({
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
            firstname: {
                required: true,
            },
        },
        submitHandler: function (form) {
            $('#update_profile').attr('disabled', true);
            // do other things for a valid form
            form.submit();
        }
    });
    $('#profile_image').change(function () {
        $(this).rules("add", {
            extension: "jpg|png|jpeg",
            maxFileSize: {
                "unit": "MB",
                "size": 2
            }
        });
    });
    $("#change_password_form").validate({
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
            old_password: {
                required: true,
                minlength: 5
            },
            password: {
                required: true,
                minlength: 5
            },
            confirm_password: {
                required: true,
                minlength: 5,
                equalTo: "#password"
            },
        },
        messages: {
            password: {
                required: "Please enter a password",
                minlength: "Your password must be at least 5 characters long"
            },
            confirm_password: {
                required: "Please enter a password",
                minlength: "Your password must be at least 5 characters long",
                equalTo: "Please enter the same password as above"
            },
        },
        submitHandler: function (form) {
            $('#update_password').attr('disabled', true);
            // do other things for a valid form
            form.submit();
        }
    });
    // Display the preview of image on image upload
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var valid_extensions = /(\.jpg|\.jpeg|\.png)$/i;
                if (typeof (input.files[0]) != 'undefined') {
                    if (valid_extensions.test(input.files[0].name)) {

                        var html = '<img src="' + e.target.result + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                    } else {
                        var html = '<img src="assets/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                    }
                } else {
                    var html = '<img src="assets/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                }
                $('#image_preview_div').html(html);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }


    var _validFileExtensions = [".jpg", ".jpeg", ".png"];
//    var _validFileExtensions_Video = [".mp4", ".webm", ".ogv", ".png",".MPG",".MPEG" ,".OGG",".ogg",".mpeg"];    
    function ValidateSingleInput(oInput) {
        if (oInput.type == "file") {
            var sFileName = oInput.value;
            if (sFileName.length > 0) {
                var blnValid = false;
                for (var j = 0; j < _validFileExtensions.length; j++) {
                    var sCurExtension = _validFileExtensions[j];
                    if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                        blnValid = true;
                        break;
                    }
                }

                if (!blnValid) {
                    $(".validation_alert label").text("Sorry, invalid file, allowed extensions are: " + _validFileExtensions.join(", "));
                    $("#validation_modal").modal();
                    oInput.value = "";
                    return false;
                }
            }
        }
        return true;
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
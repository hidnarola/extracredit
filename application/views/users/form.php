<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<?php
$edit = 0;
if (isset($user)) {
    $edit = 1;
}
?>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($user))
                    echo '<i class="icon-pencil3"></i>';
                else
                    echo '<i class="icon-user-plus"></i>';
                ?>
                <span class="text-semibold"><?php echo $heading; ?></span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('users'); ?>"><i class="icon-users4 position-left"></i> Users</a></li>
            <li class="active"><?php echo $heading; ?></li>
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
        <div class="col-md-12">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <form class="form-horizontal form-validate-jquery" action="" id="add_user_form" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="control-label col-lg-2">Profile Image</label>
                            <div class="col-lg-6">
                                <div class="media no-margin-top">
                                    <div class="media-left" id="image_preview_div">
                                        <?php if (isset($user) && $user['profile_image'] != '') { ?>
                                            <img src="<?php echo USER_IMAGES . $user['profile_image']; ?>" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                        <?php } else {
                                            ?>
                                            <img src="assets/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                        <?php } ?>
                                    </div>
                                    <div class="media-body">
                                        <input type="file" name="profile_image" id="profile_image" class="file-styled" onchange="readURL(this);">
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
                            <label class="col-lg-2 control-label">First Name <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="firstname" id="firstname" placeholder="Enter First Name" class="form-control" required="required" value="<?php echo (isset($user) && $user['firstname']) ? $user['firstname'] : set_value('firstname'); ?>">
                                <?php
                                echo '<label id="firstname-error" class="validation-error-label" for="firstname">' . form_error('firstname') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Last Name </label>
                            <div class="col-lg-6">
                                <input type="text" name="lastname" id="lastname" placeholder="Enter Last Name" class="form-control" value="<?php echo (isset($user) && $user['lastname']) ? $user['lastname'] : set_value('lastname'); ?>">
                                <?php
                                echo '<label id="lastname-error" class="validation-error-label" for="lastname">' . form_error('lastname') . '</label>';
                                ?>
                            </div>
                        </div>
                        <?php
                        $disabled = '';
                        if (isset($user)) {
                            $disabled = 'disabled';
                        }
                        ?>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Email <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="<?php echo (isset($user) && $user['email']) ? $user['email'] : set_value('email'); ?>" <?php echo $disabled ?>>
                                <?php
                                echo '<label id="email-error" class="validation-error-label" for="email">' . form_error('email') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button class="btn btn-success" type="submit" id="update_profile">Save <i class="icon-arrow-right14 position-right"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script type="text/javascript">
    var edit = <?php echo $edit ?>;
    $(".file-styled").uniform({
        fileButtonClass: 'action btn bg-pink'
    });
    $("#add_user_form").validate({
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
    if (edit == 0) {
        $('#email').rules("add", {
            required: true,
            email: true,
            remote: site_url + "users/checkUniqueEmail",
            messages: {
                remote: $.validator.format("Email address is already in use!")
            }
        });
    }
    // Display the preview of image on image upload
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var html = '<img src="' + e.target.result + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                $('#image_preview_div').html(html);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
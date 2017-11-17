<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<style>
    .disable_checkbox{ opacity: 0.3;color: darkred !important;border: 2px solid darkred !important;}
    .custom_scrollbar::-webkit-scrollbar { width: 0.4em; }
    .custom_scrollbar::-webkit-scrollbar-track { -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); }
    .custom_scrollbar::-webkit-scrollbar-thumb { background-color: #26A69A; outline: 1px solid slategrey; }
    @media (min-width:801px) {.user_permissions_div{margin-left: 45px;}}
</style>
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
                    <div class="row">
                        <form class="form-horizontal form-validate-jquery" action="" id="add_user_form" method="post" enctype="multipart/form-data">
                            <div class="col-md-4">
                                <fieldset>
                                    <legend class="text-semibold"><i class="icon-user-tie position-left"></i> User's details</legend>
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">User Role <span class="text-danger">*</span></label>
                                                <select name="role" id="role" class="select2" required="required" data-placeholder="Select User Role">
                                                    <option value=""></option>
                                                    <option value="staff" <?php
                                                    if (isset($user) && $user['role'] == 'staff')
                                                        echo 'selected';
                                                    else
                                                        echo '';
                                                    ?>>Staff</option>
                                                    <option value="financier" <?php
                                                    if (isset($user) && $user['role'] == 'financier')
                                                        echo 'selected';
                                                    else
                                                        echo '';
                                                    ?>>Financier</option>
                                                </select>
                                                <?php
                                                echo '<label id="role-error" class="validation-error-label" for="role">' . form_error('role') . '</label>';
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class=control-label">First Name <span class="text-danger">*</span></label>
                                                <input type="text" name="firstname" id="firstname" placeholder="Enter First Name" class="form-control" required="required" value="<?php echo (isset($user) && $user['firstname']) ? $user['firstname'] : set_value('firstname'); ?>">
                                                <?php
                                                echo '<label id="firstname-error" class="validation-error-label" for="firstname">' . form_error('firstname') . '</label>';
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Last Name </label>
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
                                            <div class="col-md-12">
                                                <label class="control-label">Email <span class="text-danger">*</span></label>
                                                <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="<?php echo (isset($user) && $user['email']) ? $user['email'] : set_value('email'); ?>" <?php echo $disabled ?>>
                                                <?php
                                                echo '<label id="email-error" class="validation-error-label" for="email">' . form_error('email') . '</label>';
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Profile Image</label>
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
                                            <div class="col-lg-12">
                                                <button type="submit" name="save" class="btn bg-teal custom_save_button" id="update_profile">Save<i class="icon-arrow-right14 position-right"></i></button>
                                                <button type="button" class="btn border-slate btn-flat cancel-btn custom_cancel_button" onclick="window.history.back()">Cancel</button>
                                            </div>           
                                        </div>  
                                        <!--                                        <div class="form-group">
                                                                                    <div class="col-lg-12">
                                                                                        <button class="btn btn-success" type="submit" id="update_profile">Save <i class="icon-arrow-right14 position-right"></i></button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>-->

                                </fieldset>
                            </div>
                            <div class="col-md-7 user_permissions_div">
                                <fieldset>
                                    <legend class="text-semibold"><i class="icon-shield-check position-left"></i> User's Permissions details</legend>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive custom_scrollbar" style="max-height:406px">
                                                <table class="table table-bordered table-hover table-fixed">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:5%">#</th>
                                                            <th style="width: 40%;text-align: center">Page Name</th>
                                                            <?php foreach ($actions as $k => $v) { ?>
                                                                <th style="width: 10%;text-align: center"><?= $v ?></th>
                                                            <?php } ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td style="padding:5px 15px;text-align: center">#
                                                            </td>
                                                            <td style="padding:5px 20px">SELECT ALL</td>
                                                            <td style="padding:5px 20px;padding-left:4%">
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" class="styled checkbox select_all_checkbox" id="view_checkbox">
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td style="padding:5px 20px;padding-left:4%">
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" class="styled checkbox select_all_checkbox" id="add_checkbox">
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td style="padding:5px 20px;padding-left:4%">
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" class="styled checkbox select_all_checkbox" id="edit_checkbox">
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td style="padding:5px 20px;padding-left:4%">
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" class="styled checkbox select_all_checkbox" id="delete_checkbox">
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php foreach ($pageArr as $k => $v) { ?>
                                                            <tr>
                                                                <td style="padding:5px 20px"><?= $k + 1 ?></td>
                                                                <td style="padding:5px 20px;"><?= strtoupper(str_replace('_', ' ', $v->page_name)) ?></td>
                                                                <?php
                                                                if (array_key_exists($v->id, $priv_action)) {
                                                                    foreach ($actions as $k1 => $v1) {
                                                                        $class = strtolower($v1) . '_checkbox';
                                                                        ?>
                                                                        <td style="padding:5px 20px;padding-left:4%">
                                                                            <div class="checkbox">
                                                                                <label>
                                                                                    <input type="checkbox" class="styled checkbox priv_checkbox <?php echo $class ?>" name="<?php echo $v->page_name . '[]'; ?>" value="<?php echo $k1 ?>" data-priv="<?php echo $class; ?>" <?php
                                                                                    if (isset($user)) {
                                                                                        if ($priv_action[$v->id]['pg_' . strtolower($v1)] == 1) {
                                                                                            echo 'checked';
                                                                                        }
                                                                                    }
                                                                                    ?>>
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                        <?php
                                                                    }
                                                                } else {
                                                                    foreach ($actions as $k1 => $v1) {
                                                                        $class = strtolower($v1) . '_checkbox';
                                                                        ?>
                                                                        <td style="padding:5px 20px;padding-left:4%">
                                                                            <div class="checkbox">
                                                                                <label>
                                                                                    <input type="checkbox" class="styled checkbox priv_checkbox <?php echo $class ?>" name="<?php echo $v->page_name . '[]'; ?>" value="<?php echo $k1 ?>" data-priv="<?php echo $class; ?>">
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <label id="priv_error" class="validation-error-label" style="display:none"></label>
                            </div>
                        </form>
                    </div>
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
    $('.select2').select2(); //-- Initialize select 2
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
//        submitHandler: function (form) {
//            $('#update_profile').attr('disabled', true);
//            // do other things for a valid form
//            form.submit();
//        }
        submitHandler: function (form) {
            if ($('.priv_checkbox:checked').length < 1) {
                $('#priv_error').css('display', 'block');
                $('#priv_error').html('Please select atleast one permission.');
                return false;
            } else {
                $('#priv_error').css('display', 'none');
                $('.view_checkbox').removeAttr('disabled');
                form.submit();
                $('.custom_save_button').prop('disabled', true);
            }

        },

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

    function check_checkboxes_selection(id) {
        if ($('.' + id).length == $('.' + id + ':checked').length) {
            $("#" + id).attr("checked", "checked");
            $("#" + id).parent().addClass("checked");
        } else {
            $("#" + id).removeAttr("checked");
            $("#" + id).parent().removeClass("checked");
        }
    }
//-- Add ownere privileges page
    $(document).ready(function () {
        main_checkboxes = ['view_checkbox', 'add_checkbox', 'edit_checkbox', 'delete_checkbox'];
        //-- Make selection of all checkboxes if all checkboxes are checked of particular column [In Edit Restaurant Admin Privilege]
        for (i = 0; i < main_checkboxes.length; i++) {
            if ($('.' + main_checkboxes[i]).length == $('.' + main_checkboxes[i] + ':checked').length) {
                $('#' + main_checkboxes[i]).trigger('click');
            }
        }
        $('.priv_checkbox').each(function () {
            if (!$(this).hasClass('view_checkbox')) {
                c_boxes = $(this).closest('tr').find(".priv_checkbox:checked").not('.view_checkbox');
                view_cbox = $(this).closest('tr').find(".view_checkbox");
                //-- Disable view checkbox if any one of checkbox(add,edit,delete) is checked
                if (c_boxes.length > 0) {
                    view_cbox.attr('disabled', true);
                    view_cbox.parent('span').addClass('disable_checkbox');
                } else {
                    view_cbox.removeAttr('disabled', false);
                    view_cbox.parent('span').removeClass('disable_checkbox');
                }
            }
        });
        $(".select_all_checkbox").click(function () {
            select_all_cbox = $('.' + this.id);
            if (!$('.' + this.id).is(':checked') && (select_all_cbox.length != $('.' + this.id + ':checked').length)) {
                select_all_cbox.trigger('click');
            } else {
                if (select_all_cbox.length != $('.' + this.id + ':checked').length) {
                    select_all_cbox.prop("checked", false).trigger('click');
                } else {
                    select_all_cbox.trigger('click');
                }
            }
            check_checkboxes_selection(this.id);
        });
        $("#select_all_priv").click(function () {
            select_all_priv = $('.select_all_checkbox');
            select_all_priv.trigger('click');
            $('.priv_checkbox').attr('checked', this.checked);
        });
    });
//- Select view checkbox on add/edit/delete checkbox event
    $('.priv_checkbox').change(function () {
        if (!$(this).hasClass('view_checkbox')) {
            c_boxes = $(this).closest('tr').find(".priv_checkbox:checked").not('.view_checkbox');
            view_cbox = $(this).closest('tr').find(".view_checkbox");
            if (!view_cbox.is(':checked')) {
                view_cbox.trigger('click');
            }
            //-- Disable view checkbox if any one of checkbox(add,edit,delete) is checked
            if (c_boxes.length > 0) {
                view_cbox.parent('span').addClass('disable_checkbox');
                view_cbox.attr('disabled', true);
            } else {
                view_cbox.parent('span').removeClass('disable_checkbox');
                view_cbox.attr('disabled', false);
            }
        }
        check_checkboxes_selection($(this).attr('data-priv'));
    });

    $(".styled, .multiselect-container input").uniform({
        radioClass: 'choice'
    });
</script>
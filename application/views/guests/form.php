<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<?php
$edit = 0;
if (isset($guest)) {
    $edit = 1;
}
?>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($guest))
                    echo '<i class="icon-pencil3"></i>';
                else
                    echo '<i class="icon-plus-circle2"></i>';
                ?>
                <span class="text-semibold"><?php echo $heading; ?></span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('guests'); ?>"><i class="icon-people position-left"></i> Guests</a></li>
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
                    <form class="form-horizontal form-validate-jquery" action="" id="add_guest_form" method="post" enctype="multipart/form-data">                        
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Fund Type <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select name="fund_type_id" id="fund_type_id" class="select2" required="required" data-placeholder="Select Fund Type">
                                    <option value=""></option>
                                    <?php
                                    foreach ($fund_types as $type) {
                                        $selected = '';
                                        if (isset($guest) && $guest['fund_type_id'] == $type['id'])
                                            $selected = 'selected';
                                        ?>
                                        <option value="<?php echo $type['id']; ?>" <?php echo $selected ?>><?php echo $type['type'] ?></option>
                                    <?php } ?>
                                </select>
                                <?php
                                echo '<label id="fund_type_id-error" class="validation-error-label" for="fund_type_id">' . form_error('fund_type_id') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Program/AMC <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select name="account_id" id="account_id" class="select2" required="required" data-placeholder="Select account">
                                    <option value=""></option>
                                    <?php
                                    foreach ($accounts as $account) {
                                        $selected = '';
                                        if (isset($guest) && $guest['account_id'] == $account['id'])
                                            $selected = 'selected';
                                        ?>
                                        <option value="<?php echo $account['id']; ?>" <?php echo $selected ?>><?php echo ($account['action_matters_campaign'] != '') ? $account['action_matters_campaign'] : $account['vendor_name'] ?></option>
                                    <?php } ?>
                                </select>
                                <?php
                                echo '<label id="account_id-error" class="validation-error-label" for="account_id">' . form_error('account_id') . '</label>';
                                ?>
                            </div>
                        </div>
                        <fieldset class="content-group">
                            <legend class="text-bold">Basic Guest Details</legend>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">First Name <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="firstname" id="firstname" placeholder="Enter First Name" class="form-control text-capitalize" required="required" value="<?php echo (isset($guest)) ? $guest['firstname'] : set_value('firstname'); ?>">
                                    <?php
                                    echo '<label id="firstname-error" class="validation-error-label" for="firstname">' . form_error('firstname') . '</label>';
                                    ?>
                                </div>

                                <label class="col-lg-1 control-label">Last Name <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="lastname" id="lastname" placeholder="Enter Last Name" class="form-control text-capitalize" required="required" value="<?php echo (isset($guest)) ? $guest['lastname'] : set_value('lastname'); ?>">
                                    <?php
                                    echo '<label id="lastname-error" class="validation-error-label" for="lastname">' . form_error('lastname') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Address <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <textarea name="address" id="address" placeholder="Enter Address" class="form-control text-capitalize" required="required"><?php echo (isset($guest)) ? $guest['address'] : set_value('address'); ?></textarea>
                                    <?php
                                    echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Email <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="<?php echo (isset($guest) && $guest['email']) ? $guest['email'] : set_value('email'); ?>">
                                    <?php
                                    echo '<label id="email-error" class="validation-error-label" for="email">' . form_error('email') . '</label>';
                                    ?>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">State <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <select name="state_id" id="state_id" class="select2" required="required" data-placeholder="Select State">
                                        <option value=""></option>
                                        <?php
                                        foreach ($states as $state) {
                                            $selected = '';
                                            if (isset($guest) && $guest['state_id'] == $state['id'])
                                                $selected = 'selected';
                                            ?>
                                            <option value="<?php echo $state['id']; ?>" <?php echo $selected ?>><?php echo $state['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php
                                    echo '<label id="state_id-error" class="validation-error-label" for="state_id">' . form_error('state_id') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">City <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <select name="city_id" id="city_id" class="select2" required="required" data-placeholder="Select City">
                                        <option value=""></option>
                                        <?php
                                        foreach ($cities as $city) {
                                            $selected = '';
                                            if (isset($guest) && $guest['city_id'] == $city['id'])
                                                $selected = 'selected';
                                            ?>
                                            <option value="<?php echo $city['id']; ?>" <?php echo $selected ?>><?php echo $city['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php
                                    echo '<label id="city_id-error" class="validation-error-label" for="city_id">' . form_error('city_id') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Zip <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="zip" id="zip" placeholder="Enter Zip" class="form-control" required="required" value="<?php echo (isset($guest) && $guest['zip']) ? $guest['zip'] : set_value('zip'); ?>">
                                    <?php
                                    echo '<label id="zip-error" class="validation-error-label" for="zip">' . form_error('zip') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Phone <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="phone" id="phone" placeholder="Enter Phone" class="form-control" required="required" value="<?php echo (isset($guest) && $guest['phone']) ? $guest['phone'] : set_value('phone'); ?>">
                                    <?php
                                    echo '<label id="phone-error" class="validation-error-label" for="phone">' . form_error('phone') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Company Name <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="companyname" id="companyname" placeholder="Enter Company Name" class="form-control text-capitalize" required="required" value="<?php echo (isset($guest)) ? $guest['companyname'] : set_value('companyname'); ?>">
                                    <?php
                                    echo '<label id="companyname-error" class="validation-error-label" for="companyname">' . form_error('companyname') . '</label>';
                                    ?>
                                </div>

                                <label class="control-label col-lg-1">Logo<span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <div class="media no-margin-top">
                                        <div class="media-left" id="image_preview_div">
                                            <?php
                                            $required = 'required';
                                            if (isset($guest) && $guest['logo'] != '') {
                                                $required = '';
                                                ?>
                                                <img src="<?php echo GUEST_IMAGES . $guest['logo']; ?>" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                            <?php } else {
                                                ?>
                                                <img src="assets/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                            <?php } ?>
                                        </div>

                                        <div class="media-body">
                                            <input type="file" name="logo" id="logo" class="file-styled" onchange="readURL(this);" <?php echo $required; ?>>
                                            <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>
                                        </div>
                                    </div>
                                    <?php
                                    if (isset($logo_validation))
                                        echo '<label id="logo-error" class="validation-error-label" for="logo">' . $logo_validation . '</label>';
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="content-group">
                            <legend class="text-bold">Extra Guest Details</legend>                          
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Invite Date <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                        <input type="text" name="invite_date" id="invite_date" placeholder="Enter Invite Date" class="form-control pickadate" value="<?php echo (isset($guest)) ? date('d F, Y', strtotime($guest['invite_date'])) : set_value('invite_date'); ?>" required="required">
                                    </div>
                                    <?php
                                    echo '<label id="invite_date-error" class="validation-error-label" for="invite_date">' . form_error('invite_date') . '</label>';
                                    ?>
                                </div>

                                <label class="col-lg-1 control-label">Guest Date <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                        <input type="text" name="guest_date" id="guest_date" placeholder="Enter Guest Date" class="form-control pickadate" value="<?php echo (isset($guest)) ? date('d F, Y', strtotime($guest['guest_date'])) : set_value('guest_date'); ?>" required="required">
                                    </div>
                                    <?php
                                    echo '<label id="guest_date-error" class="validation-error-label" for="guest_date">' . form_error('guest_date') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">AIR Date <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                        <input type="text" name="AIR_date" id="post_date" placeholder="Enter AIR Date" class="form-control pickadate" value="<?php echo (isset($guest)) ? date('d F, Y', strtotime($guest['AIR_date'])) : set_value('AIR_date'); ?>" required="required">
                                    </div>
                                    <?php
                                    echo '<label id="AIR_date-error" class="validation-error-label" for="AIR_date">' . form_error('AIR_date') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">AMC created? <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <div class="checkbox checkbox-switch">
                                        <label>
                                            <input type="checkbox" name="AMC_created" id="AMC_created" data-off-color="danger" data-on-text="Yes" data-off-text="No" class="switch" <?php
                                            if (isset($guest) && $guest['AMC_created'] == 'NO')
                                                echo '';
                                            else
                                                echo 'checked="checked"';
                                            ?> value="1">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Assistant <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="assistant" id="assistant" placeholder="Enter Assistant" class="form-control" required="required" value="<?php echo (isset($guest) && $guest['assistant']) ? $guest['assistant'] : set_value('assistant'); ?>">
                                    <?php
                                    echo '<label id="assistant-error" class="validation-error-label" for="assistant">' . form_error('assistant') . '</label>';
                                    ?>
                                </div>

                                <label class="col-lg-1 control-label">Assistant Phone <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="assistant_phone" id="assistant_phone" placeholder="Enter Assistant Phone" class="form-control" required="required" value="<?php echo (isset($guest) && $guest['assistant_phone']) ? $guest['assistant_phone'] : set_value('assistant_phone'); ?>">
                                    <?php
                                    echo '<label id="assistant_phone-error" class="validation-error-label" for="assistant_phone">' . form_error('assistant_phone') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Assistant Email <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="assistant_email" id="phone" placeholder="Enter Assistant Email" class="form-control" required="required" value="<?php echo (isset($guest) && $guest['assistant_email']) ? $guest['assistant_email'] : set_value('assistant_email'); ?>">
                                    <?php
                                    echo '<label id="assistant_email-error" class="validation-error-label" for="assistant_email">' . form_error('assistant_email') . '</label>';
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button type="submit" name="save" class="btn bg-teal custom_save_button" id="guest_btn_submit">Save<i class="icon-arrow-right14 position-right"></i></button>
                                <button type="button" class="btn border-slate btn-flat cancel-btn custom_cancel_button" onclick="window.history.back()">Cancel</button>
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
    // Styled file input
    $(".file-styled").uniform({
        fileButtonClass: 'action btn bg-blue'
    });
    $('.pickadate').pickadate({
        max: new Date()
    });

    var edit = <?php echo $edit ?>;
    $('.select2').select2(); //-- Initialize select 2
    $(".switch").bootstrapSwitch(); //-- Initialize switch
    //-- fund type change event
    $('#fund_type_id').change(function () {
        $.ajax({
            url: '<?php echo site_url('donors/get_accounts') ?>',
            data: {id: btoa($(this).val())},
            type: "POST",
            dataType: 'json',
            success: function (data) {
                var options = "<option value=''></option>";
                for (var i = 0; i < data.length; i++) {
                    options += '<option value="' + data[i]['id'] + '">';
                    if (data[i]['action_matters_campaign'] != null) {
                        options += data[i]['action_matters_campaign'];
                    } else {
                        options += data[i]['vendor_name'];
                    }
                    options += '</option>';
                }
                $('#account_id').empty().append(options);
                $("#account_id").select2("val", '');
            }
        });
    });
    $('#state_id').change(function () {
        $.ajax({
            url: '<?php echo site_url('donors/get_cities') ?>',
            data: {id: btoa($(this).val())},
            type: "POST",
            dataType: 'json',
            success: function (data) {
                var options = "<option value=''></option>";
                for (var i = 0; i < data.length; i++) {
                    options += '<option value="' + data[i]['id'] + '">' + data[i]['name'] + '</option>';
                }
                $('#city_id').empty().append(options);
                $("#city_id").select2("val", '');
            }
        });
    });

    $('#logo').change(function () {
        $(this).rules("add", {
            extension: "jpg|png|jpeg",
            maxFileSize: {
                "unit": "MB",
                "size": 2
            }
        });
    });
    $("#add_guest_form").validate({
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
            email: {
                required: true,
                email: true,
            },
            assistant_email: {
                required: true,
                email: true,
            },
            phone: {
                required: true,
                phoneno: true
            },
            assistant_phone: {
                required: true,
                phoneno: true
            },
            zip: {
                required: true,
                digits: true
            },
        },
        submitHandler: function (form) {
            $('#guest_btn_submit').attr('disabled', true);
            form.submit();
        }
    });

    jQuery.validator.addMethod("phoneno", function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
                phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
    }, "Please specify a valid phone number");
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
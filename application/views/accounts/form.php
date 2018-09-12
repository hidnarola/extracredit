<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAdyDSU074CCHVR2oygIqTLO9_ZOZEVrWE"  type="text/javascript"></script>
<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/core/libraries/jasny_bootstrap.min.js"></script>
<?php
$edit = 0;
if (isset($account)) {
    $edit = 1;
}
?>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($account))
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
            <li><a href="<?php echo site_url('accounts'); ?>"><i class="icon-calculator3 position-left"></i> <!--Accounts-->Award Recipients</a></li>
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
                    <form class="form-horizontal form-validate-jquery" action="" id="add_account_form" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Fund Types <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select name="fund_type_id" id="fund_type_id" class="select2" required="required" data-placeholder="Select Fund Type">
                                    <option value=""></option>
                                    <?php
                                    foreach ($fund_types as $type) {
                                        $selected = '';
                                        if (isset($account) && $account['fund_type_id'] == $type['id'])
                                            $selected = 'selected';
                                        ?>
                                        <option value="<?php echo $type['id']; ?>" <?php echo $selected ?>><?php echo $type['name'] ?></option>
                                    <?php } ?>
                                </select>
                                <?php
                                echo '<label id="fund_type_id-error" class="validation-error-label" for="fund_type_id">' . form_error('fund_type_id') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group program_div">
                            <label class="col-lg-2 control-label">Program Name <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="program_name" id="program_name" placeholder="Enter Program Name" class="form-control text-capitalize" value="<?php echo (isset($account)) ? $account['program_name'] : set_value('program_name'); ?>" required="required">
                                <?php
                                echo '<label id="program_name-error" class="validation-error-label" for="program_name">' . form_error('program_name') . '</label>';
                                ?>
                            </div>
                        </div>
                        <?php
                        $program_div_style = '';
                        $vendor_div_style = 'style="display:none"';
                        $program_required = '';
                        $vendor_required = '';
                        if (isset($account) && $account['type'] == 1) {
                            $program_div_style = 'style="display:none"';
                            $vendor_div_style = '';
                            $program_required = '';
                            $vendor_required = 'required="required"';
                        }
                        ?>
                        <div class="form-group program_div" <?php echo $program_div_style ?>>
                            <label class="col-lg-2 control-label">Action Matters Campaign</label>
                            <div class="col-lg-6">
                                <input type="text" name="action_matters_campaign" id="action_matters_campaign" placeholder="Enter Action Matters Campaign" class="form-control text-capitalize" value="<?php echo (isset($account)) ? $account['action_matters_campaign'] : set_value('action_matters_campaign'); ?>" <?php echo $program_required ?>>
                            </div>
                        </div>
                        <div class="form-group vendor_div" <?php echo $vendor_div_style ?>>
                            <label class="col-lg-2 control-label">Vendor Name <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" name="vendor_name" id="vendor_name" placeholder="Enter Vendor Name" class="form-control text-capitalize" value="<?php echo (isset($account)) ? $account['vendor_name'] : set_value('vendor_name'); ?>"  <?php echo $vendor_required ?>>
                                <?php
                                echo '<label id="vendor_name-error" class="validation-error-label" for="vendor_name">' . form_error('vendor_name') . '</label>';
                                ?>
                            </div>
                        </div>
                        <fieldset class="content-group">
                            <legend class="text-bold">Basic <!--Account-->Award Recipient Details</legend>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Address </label>
                                <div class="col-lg-4">
                                    <textarea name="address" id="address" placeholder="Enter Address" class="form-control text-capitalize" ><?php echo (isset($account)) ? $account['address'] : set_value('address'); ?></textarea>
                                    <?php
                                    echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Zip </label>
                                <div class="col-lg-4">
                                    <input type="text" name="zip" id="zip" placeholder="Enter Zip" class="form-control" value="<?php echo (isset($account) && $account['zip']) ? $account['zip'] : set_value('zip'); ?>">
                                    <?php
                                    echo '<label id="zip-error" class="validation-error-label" for="zip">' . form_error('zip') . '</label>';
                                    ?>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">City </label>
                                <div class="col-lg-4" id="city_wrap">
                                    <input type="text" name="city_id" id="city_id" readonly="" class="form-control" value="<?php echo (isset($account)) ? $account['city'] : set_value('city_id'); ?>">
                                    <?php
                                    echo '<label id="city_id-error" class="validation-error-label" for="city_id">' . form_error('city_id') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">State </label>
                                <div class="col-lg-4">
                                    <input type="text" name="state_id" id="state_id" readonly="" class="form-control"  value="<?php echo (isset($account)) ? $account['state'] : set_value('state_id'); ?>">

                                    <?php
                                    echo '<label id="state_id-error" class="validation-error-label" for="state_id">' . form_error('state_id') . '</label>';
                                    ?>
                                </div>
                                <input type="hidden" name="state_short" id="state_short" value="<?php echo (isset($account)) ? $account['state_short'] : set_value('state_short'); ?>"/>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Phone </label>
                                <div class="col-lg-4">
                                    <input type="text" name="phone" id="phone" placeholder="Enter Phone" class="form-control" value="<?php echo (isset($account) && $account['phone']) ? $account['phone'] : set_value('phone'); ?>" data-mask="999-999-9999">
                                    <?php
                                    echo '<label id="phone-error" class="validation-error-label" for="phone">' . form_error('phone') . '</label>';
                                    ?>
                                </div>

                                <div class="program_div" <?php echo $program_div_style ?>>
                                    <label class="col-lg-1 control-label">Tax ID </label>
                                    <div class="col-lg-4">
                                        <input type="text" name="tax_id" id="tax_id" placeholder="Enter Tax ID" class="form-control" value="<?php echo (isset($account) && $account['tax_id']) ? $account['tax_id'] : set_value('tax_id'); ?>" data-mask="99-9999999">
                                        <?php
                                        echo '<label id="tax_id-error" class="validation-error-label" for="tax_id">' . form_error('tax_id') . '</label>';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="content-group">
                            <legend class="text-bold">Extra <!--Account-->Award Recipient Details</legend>
                            <div class="form-group program_div" <?php echo $program_div_style ?>>
                                <label class="col-lg-2 control-label">Program Types </label>
                                <div class="col-lg-4">
                                    <select name="program_type_id" id="program_type_id" class="select2" data-placeholder="Select Program Type" <?php echo '' ?>>
                                        <option value=""></option>
                                        <?php
                                        foreach ($program_types as $program_type) {
                                            $selected = '';
                                            if (isset($account) && $account['program_type_id'] == $program_type['id'])
                                                $selected = 'selected';
                                            ?>
                                            <option value="<?php echo $program_type['id']; ?>" <?php echo $selected ?>><?php echo $program_type['type'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php
                                    echo '<label id="program_type_id-error" class="validation-error-label" for="program_type_id">' . form_error('program_type_id') . '</label>';
                                    ?>
                                </div>
                                <div class=" program_div" <?php echo $program_div_style ?>>
                                    <label class="col-lg-1 control-label">Program Status </label>
                                    <div class="col-lg-4">
                                        <select name="program_status_id" id="program_status_id" class="select2" data-placeholder="Select Program Status"  <?php echo '' ?>>
                                            <option value=""></option>
                                            <?php
                                            foreach ($program_status as $status) {
                                                $selected = '';
                                                if (isset($account) && $account['program_status_id'] == $status['id'])
                                                    $selected = 'selected';
                                                ?>
                                                <option value="<?php echo $status['id']; ?>" <?php echo $selected ?>><?php echo $status['status'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php
                                        echo '<label id="program_status_id-error" class="validation-error-label" for="program_status_id">' . form_error('program_status_id') . '</label>';
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Website </label>
                                <div class="col-lg-4">
                                    <input type="text" name="website" id="website" placeholder="Enter website" class="form-control" value="<?php echo (isset($account) && $account['website']) ? $account['website'] : set_value('website'); ?>" >
                                    <?php
                                    echo '<label id="website-error" class="validation-error-label" for="website">' . form_error('website') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">AMC is Active? </label>
                                <div class="col-lg-4">
                                    <div class="checkbox checkbox-switch">
                                        <label>
                                            <input type="checkbox" name="is_active" id="is_active" data-off-color="danger" data-on-text="Yes" data-off-text="No" class="switch" <?php
                                            if (isset($account) && $account['is_active'] == 0)
                                                echo '';
                                            else
                                                echo 'checked="checked"';
                                            ?> value="1">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="content-group">
                            <legend class="text-bold">Contact Details</legend>
                            <?php if (!empty($contacts)) { ?>
                                <div class="contact-div">
                                    <?php
                                    $counter = 1;
                                    $total_contacts = count($contacts);
                                    foreach ($contacts as $contact) {
                                        ?>
                                        <div class="form-group">
                                            <label class="col-lg-1 control-label">Name <span class="text-danger">*</span></label>
                                            <div class="col-lg-3">
                                                <input type="text" name="contact_name[]" id="contact_name_<?php echo $counter ?>" placeholder="Enter Contact Name" class="form-control text-capitalize" required="required" value="<?php echo $contact['name']; ?>">
                                            </div>
                                            <label class="col-lg-1 control-label">Email </label>
                                            <div class="col-lg-3">
                                                <input type="email" name="contact_email[]" id="contact_email_<?php echo $counter ?>" placeholder="Enter Email" class="form-control" value="<?php echo $contact['email']; ?>">
                                            </div>
                                            <label class="col-lg-1 control-label">Phone </label>
                                            <div class="col-lg-2">
                                                <input type="text" name="contact_phone[]" id="contact_phone_<?php echo $counter ?>" placeholder="Enter Phone" class="form-control" value="<?php echo $contact['phone']; ?>" data-mask="999-999-9999">
                                            </div>
                                            <div class="col-lg-1">
                                                <?php if ($total_contacts == $counter) { ?>
                                                    <a class="add_contact_btn btn btn-primary"><i class="icon-plus-circle2"></i></a>
                                                <?php } else { ?>
                                                    <a class="remove_contact_btn btn btn-danger"><i class="icon-trash"></i></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php
                                        $counter++;
                                    }
                                    ?>
                                </div>
                            <?php } else {
                                ?> 
                                <div class="contact-div">
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label">Name <span class="text-danger">*</span></label>
                                        <div class="col-lg-3">
                                            <input type="text" name="contact_name[]" id="contact_name_1" placeholder="Enter Name" class="form-control text-capitalize" required="required">
                                        </div>
                                        <label class="col-lg-1 control-label">Email </label>
                                        <div class="col-lg-3">
                                            <input type="email" name="contact_email[]" id="contact_email_1" placeholder="Enter Email" class="form-control">
                                        </div>
                                        <label class="col-lg-1 control-label">Phone </label>
                                        <div class="col-lg-2">
                                            <input type="text" name="contact_phone[]" id="contact_phone_1" placeholder="Enter Phone" class="form-control" data-mask="999-999-9999">
                                        </div>
                                        <div class="col-lg-1">
                                            <a class="add_contact_btn btn btn-primary"><i class="icon-plus-circle2"></i></a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <label class="col-lg-1 control-label">Is Subscribed? </label>
                                <div class="col-lg-4">
                                
                                    <div class="checkbox checkbox-switch">
                                        <label>
                                            <input type="checkbox" name="is_subscribed" id="is_subscribed" data-off-color="danger" data-on-text="Yes" data-off-text="No" class="switch" <?php
                                                if (isset($account) && $account['is_subscribed'] == 0)
                                                    echo '';
                                                else
                                                    echo 'checked="checked"';
                                                ?> value="1">
                                        </label>
                                    </div>
                                </div>
                        </fieldset>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button type="submit" name="save" class="btn bg-teal custom_save_button" id="account_btn_submit">Save<i class="icon-arrow-right14 position-right"></i></button>
                                <?php if(!isset($account))
                                { ?>
                                    <button type="submit" name="save_add_another" style="width: 172px;display: inline-block;float: left;margin-right: 15px;border: none;padding: 8px 12px;" class="btn bg-teal" id="account_btn_submit1">Save and Add Another<i class="icon-arrow-right14 position-right"></i></button>
                                <?php   
                                } ?>
                                <a href="<?php echo base_url('accounts') ?>" style="color:black" class="btn border-slate btn-flat cancel-btn custom_cancel_button">Cancel</a>
                                <!-- <button type="button" class="btn border-slate btn-flat cancel-btn custom_cancel_button" onclick="window.history.back()">Cancel</button> -->
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
    var email_url = site_url + 'accounts/checkUniqueEmail/';
    var vendor_url = site_url + 'accounts/checkUniqueVendor/';
    var program_url = site_url + 'accounts/checkUniqueProgram/';
    var amc_url = site_url + 'accounts/checkUniqueAMC/';
    if (edit == 1) {
        var append_id = <?php echo (isset($account)) ? $account['id'] : 0 ?>;
        var fund_type = <?php echo (isset($account)) ? $account['fund_type_id'] : 0 ?>;
        email_url += btoa(append_id);
        vendor_url += btoa(append_id);
        amc_url += btoa(append_id);
        program_url += fund_type + '/' + btoa(append_id);
    }
    $('.select2').select2(); //-- Initialize select 2
    $(".switch").bootstrapSwitch(); //-- Initialize switch
    //-- fund type change event
    $('#fund_type_id').change(function () {
        fund_type_val = $(this).val();
        program_url = site_url + 'accounts/checkUniqueProgram/' + fund_type_val;
        if (edit == 1) {
            var append_id = <?php echo (isset($account)) ? $account['id'] : 0 ?>;
            program_url = site_url + 'accounts/checkUniqueProgram/' + fund_type_val + '/' + btoa(append_id);
        }
        $('#program_name').rules('add', {
            remote: program_url,
            messages: {
                remote: "This program name is already added",
            }
        });

        $.ajax({
            url: '<?php echo site_url('accounts/get_fund_type') ?>',
            data: {id: btoa($(this).val())},
            type: "POST",
            dataType: 'json',
            success: function (data) {
                if (data.type == 1) {
                    $('.vendor_div').show();
                    $('.program_div').hide();
                    $("#vendor_name").rules("add", "required");
                } else {
                    $('.vendor_div').hide();
                    $('.program_div').show();
                    $("#vendor_name").rules("remove", "required");
                }
            }
        });
    });
    $('#state_id').change(function () {
        $.ajax({
            url: '<?php echo site_url('accounts/get_cities') ?>',
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

    $("#add_account_form").validate({
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
            program_name: {
                remote: program_url,
            },
            action_matters_campaign: {
                remote: amc_url,
            },
            vendor_name: {
                remote: vendor_url,
            },
            tax_id: {
                taxUS: true,
            },
            website: {
                validUrl: true,
            },
            zip: {
                zipcodeUS: true
            }
        },
        messages: {
            program_name: {
                remote: $.validator.format("Program name is already added!")
            },
            vendor_name: {
                remote: $.validator.format("Vendor name is already added!")
            },
            action_matters_campaign: {
                remote: $.validator.format("AMC name is already added!")
            }
        },
        submitHandler: function (form) {
            if ($('input[name="contact_name[]"]').valid() && $('input[name="contact_email[]"]').valid()) {
                $('#account_btn_submit').attr('disabled', true);
                $('#account_btn_submit1').attr('disabled', true);
                form.submit();
            }
        }
    });
    jQuery.validator.addMethod("taxUS", function (value, element) {
        return this.optional(element) || /^\d{2}-\d{7}$/.test(value);
    }, "You have entere invalid Tax. Tax format should be 00-0000000");

    $(document).ready(function () {
        var geocoder = new google.maps.Geocoder();
        //when the user clicks off of the zip field:
        $("#zip").on("keyup keydown change", function () {
            if ($(this).val().length == 5) {
                var zip = $(this).val();
                var city = '';
                var state = '';
                var state_short = '';
                //make a request to the google geocode api
                $.getJSON('https://maps.googleapis.com/maps/api/geocode/json?address=' + zip)
                        .success(function (response) {
                            //find the city and state
                            var address_components = response.results[0].address_components;
                            $.each(address_components, function (index, component) {
                                var types = component.types;
                                $.each(types, function (index, type) {
                                    if (type == 'locality') {
                                        city = component.long_name;
                                    }
                                    if (type == 'administrative_area_level_1') {
                                        state_short = component.short_name;
                                        state = component.long_name;
                                    }
                                });
                            });
                            //pre-fill the city and state
                            var cities = response.results[0].postcode_localities;
                            if (cities) {
                                //turn city into a dropdown if necessary
                                var $select = $(document.createElement('select'));
                                $select.addClass('select2');
                                $select.attr('id', 'city_id');
                                $select.attr('name', 'city_id');
                                $.each(cities, function (index, locality) {
                                    var $option = $(document.createElement('option'));
                                    $option.html(locality);
                                    $option.attr('value', locality);
                                    if (city == locality) {
                                        $option.attr('selected', 'selected');
                                    }
                                    $select.append($option);
                                });
                                $('#city_wrap').html($select);
                                $('#city_id').select2();

                            } else {
                                var txtbox = '<input type="text" name="city_id" id="city_id" placeholder="City" class="form-control" required="required" value="' + city + '" readonly>'
                                $('#city_wrap').html(txtbox);
                                $('#city_id').val(city);
                            }
                            $('#state_id').val(state);
                            $('#state_short').val(state_short);
                        });
            }
        });
    });
    /*Validator method for US Zipcode*/
    $.validator.addMethod("zipcodeUS", function (value, element) {
        return this.optional(element) || /^\d{5}-\d{4}$|^\d{5}$/.test(value);
    }, "The specified US ZIP Code is invalid");

    /*Validator method for valid URL*/
    $.validator.addMethod('validUrl', function (value, element) {
        value = $.trim(value);
        var url = $.validator.methods.url.bind(this);
        return url(value, element) || url('http://' + value, element);
    }, 'Please enter a valid URL');

    // Add contact button
    $(document).on('click', '.add_contact_btn', function () {
        if ($('input[name="contact_name[]"]').valid() && $('input[name="contact_email[]"]').valid()) {
            contact_div = $(this).parent('.col-lg-1').parent('.form-group').clone();

            field_count = contact_div.find('input[name="contact_name[]"]').attr('id');
            field_count = field_count.split('_');
            field_count = field_count[2];
            field_count = parseInt(field_count) + 1;

            contact_div.find('input[name="contact_name[]"]').val('');
            contact_div.find('input[name="contact_name[]"]').attr('id', 'contact_name_' + field_count);
            contact_div.find('input[name="contact_email[]"]').val('');
            contact_div.find('input[name="contact_email[]"]').attr('id', 'contact_email_' + field_count);
            contact_div.find('input[name="contact_phone[]"]').val('');
            contact_div.find('input[name="contact_phone[]"]').attr('id', 'contact_phone_' + field_count);
            contact_div.find('.validation-error-label').remove();

            $('.contact-div').append(contact_div);
            $(this).html('<i class="icon-trash"></i>');
            $(this).removeClass('add_contact_btn');
            $(this).removeClass('btn-primary');
            $(this).addClass('remove_contact_btn');
            $(this).addClass('btn-danger');
        }
    });
    // Remove Add contact button
    $(document).on('click', '.remove_contact_btn', function () {
        $(this).parent('.col-lg-1').parent('.form-group').remove();
    });

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
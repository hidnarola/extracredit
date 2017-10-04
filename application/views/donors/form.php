<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="assets/js/core/libraries/jasny_bootstrap.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAdyDSU074CCHVR2oygIqTLO9_ZOZEVrWE"  type="text/javascript"></script>
<?php
$edit = 0;
$account_disabled = '';
if (isset($donor)) {
    $edit = 1;
    $account_disabled = 'disabled';
}
?>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($donor))
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
            <li><a href="<?php echo site_url('donors'); ?>"><i class="icon-coins position-left"></i> Donors</a></li>
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
                    <form class="form-horizontal form-validate-jquery" action="" id="add_donor_form" method="post" enctype="multipart/form-data">
                        <fieldset class="content-group">
                            <legend class="text-bold">Basic Donor Details</legend>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">First Name <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="firstname" id="firstname" placeholder="Enter First Name" class="form-control text-capitalize" required="required" value="<?php echo (isset($donor)) ? $donor['firstname'] : set_value('firstname'); ?>">
                                    <?php
                                    echo '<label id="firstname-error" class="validation-error-label" for="firstname">' . form_error('firstname') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Last Name</label>
                                <div class="col-lg-4">
                                    <input type="text" name="lastname" id="lastname" placeholder="Enter Last Name" class="form-control text-capitalize" value="<?php echo (isset($donor)) ? $donor['lastname'] : set_value('lastname'); ?>">
                                    <?php
                                    echo '<label id="lastname-error" class="validation-error-label" for="lastname">' . form_error('lastname') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Address</label>
                                <div class="col-lg-4">
                                    <textarea name="address" id="address" placeholder="Enter Address" class="form-control text-capitalize"><?php echo (isset($donor)) ? $donor['address'] : set_value('address'); ?></textarea>
                                    <?php
                                    echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Email</label>
                                <div class="col-lg-4">
                                    <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="<?php echo (isset($donor) && $donor['email']) ? $donor['email'] : set_value('email'); ?>">
                                    <?php
                                    echo '<label id="email-error" class="validation-error-label" for="email">' . form_error('email') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Phone </label>
                                <div class="col-lg-4">
                                    <input type="text" name="phone" id="phone" placeholder="Enter Phone" class="form-control" value="<?php echo (isset($donor) && $donor['phone']) ? $donor['phone'] : set_value('phone'); ?>" data-mask="999-999-9999">
                                    <?php
                                    echo '<label id="phone-error" class="validation-error-label" for="phone">' . form_error('phone') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Zip</label>
                                <div class="col-lg-4">
                                    <input type="text" name="zip" id="zip" placeholder="Enter Zip" class="form-control" value="<?php echo (isset($donor) && $donor['zip']) ? $donor['zip'] : set_value('zip'); ?>">
                                    <?php
                                    echo '<label id="zip-error" class="validation-error-label" for="zip">' . form_error('zip') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">State</label>
                                <div class="col-lg-4">
                                    <input type="text" name="state" id="state" placeholder="State" class="form-control" value="<?php echo (isset($donor) && $donor['state']) ? $donor['state'] : set_value('state'); ?>" readonly>
                                    <?php
                                    echo '<label id="state-error" class="validation-error-label" for="state">' . form_error('state') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">City</label>
                                <div class="col-lg-4" id="city_wrap">
                                    <input type="text" name="city" id="city" placeholder="City" class="form-control" value="<?php echo (isset($donor) && $donor['city']) ? $donor['city'] : set_value('city'); ?>" readonly>
                                    <?php
                                    echo '<label id="city-error" class="validation-error-label" for="city">' . form_error('city') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <input type="hidden" name="state_short" id="state_short" value="<?php echo (isset($donor)) ? $donor['state_short'] : set_value('state_short'); ?>"/>
                        </fieldset>
                        <?php if (!isset($donor)) { ?>
                            <fieldset class="content-group">
                                <legend class="text-bold">Payment Details</legend>
                                <div class="form-group">
                                    <label class="col-lg-1 control-label">Fund Type</label>
                                    <div class="col-lg-4">
                                        <select name="fund_type_id" id="fund_type_id" class="select2" required="" data-placeholder="Select Fund Type" <?php echo $account_disabled ?>>
                                            <option value=""></option>
                                            <?php
                                            foreach ($fund_types as $type) {
                                                $selected = '';
                                                if (isset($donor) && $donor['fund_type_id'] == $type['id'])
                                                    $selected = 'selected';
                                                ?>
                                                <option value="<?php echo $type['id']; ?>" <?php echo $selected ?>><?php echo $type['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php
                                        echo '<label id="fund_type_id-error" class="validation-error-label" for="fund_type_id">' . form_error('fund_type_id') . '</label>';
                                        ?>
                                    </div>
                                    <label class="col-lg-1 control-label">Program/AMC</label>
                                    <div class="col-lg-4">
                                        <select name="account_id" id="account_id" class="select2" required="" data-placeholder="Select account" <?php echo $account_disabled ?>>
                                            <option value=""></option>
                                            <?php
                                            foreach ($accounts as $account) {
                                                $selected = '';
                                                if (isset($donor) && $donor['account_id'] == $account['id'])
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
                                <div class="form-group">
                                    <label class="col-lg-1 control-label">Date</label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                            <input type="text" name="date" id="date" class="form-control pickadate" placeholder="Select Date" value="<?php
                                            if (isset($donor)) {
                                                if ($donor['date'] != null)
                                                    echo date('d F, Y', strtotime($donor['date']));
                                                else
                                                    echo '';
                                            } else
                                                echo set_value('date');
                                            ?>">
                                        </div>
                                        <?php
                                        echo '<label id="date-error" class="validation-error-label" for="date">' . form_error('date') . '</label>';
                                        ?>
                                    </div>
                                    <label class="col-lg-1 control-label">Post Date</label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                            <input type="text" name="post_date" id="post_date" class="form-control pickadate" placeholder="Select Post Date" value="<?php
                                            if (isset($donor)) {
                                                if ($donor['post_date'] != null || $donor['post_date'] != '0000-00-00')
                                                    echo date('d F, Y', strtotime($guest['post_date']));
                                                else
                                                    echo '';
                                            } else
                                                echo set_value('post_date');
                                            ?>">
                                        </div>
                                        <?php
                                        echo '<label id="post_date-error" class="validation-error-label" for="post_date">' . form_error('post_date') . '</label>';
                                        ?>
                                    </div>
                                </div>
                                <?php
                                $split_settings_style = 'style="display: none;"';
                                $split_checkbox = '';
                                if (isset($donor) || form_error('admin_percent') != '') {
                                    $split_settings_style = '';
                                    $split_checkbox = 'checked';
                                }
                                ?>
                                <div class="form-group">
                                    <label class="col-lg-1 control-label">Amount <span class="text-danger">*</span></label>
                                    <div class="col-lg-4">
                                        <input type="number" name="amount" id="amount" placeholder="Enter Amount" class="form-control" value="<?php echo (isset($donor) && $donor['amount']) ? $donor['amount'] : set_value('amount'); ?>" required="required">
                                        <?php
                                        echo '<label id="amount-error" class="validation-error-label" for="amount">' . form_error('amount') . '</label>';
                                        ?>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="checkbox-inline checkbox-right">
                                            <input type="checkbox" class="styled" id="change_donation_split" <?php echo $split_checkbox ?>>
                                            Change Donation Split Settings
                                        </label>
                                    </div>
                                </div>
                                <div class="content-group split_div" <?php echo $split_settings_style ?>>
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label">Admin Donation(%)<span class="text-danger">*</span></label>
                                        <div class="col-lg-4">
                                            <input type="number" name="admin_percent" id="admin-donation-percent" placeholder="Enter Admin Donation(%)" class="form-control" value="<?php echo (isset($donor)) ? $donor['admin_percent'] : $settings['admin-donation-percent']; ?>" required="required"/>
                                            <?php
                                            echo '<label id="admin_percent-error" class="validation-error-label" for="admin_percent">' . form_error('admin_percent') . '</label>';
                                            ?>
                                        </div>
                                        <label class="col-lg-1 control-label">Program/AMC Donation(%)<span class="text-danger">*</span></label>
                                        <div class="col-lg-4">
                                            <input type="number" name="account_percent" id="program-donation-percent" placeholder="Enter Program Donation(%)" class="form-control" value="<?php echo (isset($donor)) ? $donor['account_percent'] : $settings['program-donation-percent']; ?>" required="required" readonly/>
                                            <?php
                                            echo '<label id="account_percent-error" class="validation-error-label" for="account_percent">' . form_error('account_percent') . '</label>';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-1 control-label">Admin Fund </label>
                                    <div class="col-lg-4">
                                        <input type="number" name="admin_fund" id="admin_fund" placeholder="Admin Fund" class="form-control" value="<?php echo (isset($donor) && $donor['amount']) ? $donor['admin_fund'] : set_value('admin_fund'); ?>" readonly>
                                    </div>
                                    <label class="col-lg-1 control-label">Account Fund </label>
                                    <div class="col-lg-4">
                                        <input type="number" name="account_fund" id="account_fund" placeholder="Account Fund" class="form-control" value="<?php echo (isset($donor) && $donor['account_fund']) ? $donor['account_fund'] : set_value('account_fund'); ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-1 control-label">Pmt Type</label>
                                    <div class="col-lg-4">
                                        <select name="payment_type_id" id="payment_type_id" class="select2" data-placeholder="Select Payment Type">
                                            <option value=""></option>
                                            <?php
                                            foreach ($payment_types as $payment_type) {
                                                $selected = '';
                                                if (isset($donor) && $donor['payment_type_id'] == $payment_type['id'])
                                                    $selected = 'selected';
                                                ?>
                                                <option value="<?php echo $payment_type['id']; ?>" <?php echo $selected ?>><?php echo $payment_type['type'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php
                                        echo '<label id="payment_type_id-error" class="validation-error-label" for="payment_type_id">' . form_error('payment_type_id') . '</label>';
                                        ?>
                                    </div>
                                    <label class="col-lg-1 control-label">Pmt Number</label>
                                    <div class="col-lg-4">
                                        <input type="text" name="payment_number" id="payment_number" placeholder="Enter Payment Number" class="form-control" value="<?php echo (isset($donor) && $donor['payment_number']) ? $donor['payment_number'] : set_value('payment_number'); ?>">
                                        <?php
                                        echo '<label id="payment_number-error" class="validation-error-label" for="payment_number">' . form_error('payment_number') . '</label>';
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-1 control-label">Memo </label>
                                    <div class="col-lg-4">
                                        <textarea name="memo" id="memo" placeholder="Enter Memo" class="form-control"><?php echo (isset($donor)) ? $donor['memo'] : set_value('memo'); ?></textarea>
                                        <?php
                                        echo '<label id="memo-error" class="validation-error-label" for="memo">' . form_error('memo') . '</label>';
                                        ?>
                                    </div>
                                </div>
                            </fieldset>
                        <?php } ?>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button type="submit" name="save" class="btn bg-teal custom_save_button" id="donor_btn_submit">Save<i class="icon-arrow-right14 position-right"></i></button>
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
    //-- Style checkbox
    $(".styled, .multiselect-container input").uniform({
        radioClass: 'choice'
    });

    //-- Initialize datepicker
    $('.pickadate').pickadate({
//        max: new Date()
    });
    var edit = <?php echo $edit ?>;
    var email_url = site_url + 'donors/checkUniqueEmail/';
    if (edit == 1) {
        var append_id = <?php echo (isset($donor)) ? $donor['id'] : 0 ?>;
        email_url += btoa(append_id);
    }
    $('.select2').select2(); //-- Initialize select 2
    $(".switch").bootstrapSwitch(); //-- Initialize switch
    $('.select2').change(function () {
        $(this).valid();
    });

    $(document).on('click', '#change_donation_split', function () {
        if ($(this).prop("checked") == true) {
            $('.split_div').css('display', 'block');
        } else if ($(this).prop("checked") == false) {
            $('.split_div').css('display', 'none');
        }
    });

    //-- Admin donation & Program donation key change event
    $("#admin-donation-percent").on("keyup keydown change", function (event) {
        if ($(this).val() != '' && Number($(this).val()) >= 0 && Number($(this).val()) <= 100) {
            var p_amount = 100 - $(this).val();
            $('#program-donation-percent').val(p_amount);

            //-- If donation percentage is changed then also change amount
            if ($('#amount').val() != '' && Number($('#amount').val()) >= 0) {
                admin_donation = $('#admin-donation-percent').val();
                admin_amt = (($('#amount').val()) * admin_donation) / 100;
                admin_amt = admin_amt.toFixed(2);
                account_amt = $('#amount').val() - admin_amt;
                $('#admin_fund').val(admin_amt);
                $('#account_fund').val(account_amt);
            }
        }
    });

    //-- Donor Amount change eevent
    $("#amount").on("keyup keydown change", function (event) {
        if ($(this).val() != '' && Number($(this).val()) >= 0) {
            admin_donation = $('#admin-donation-percent').val();
            admin_amt = (($(this).val()) * admin_donation) / 100;
            admin_amt = admin_amt.toFixed(2);
            account_amt = $(this).val() - admin_amt;
            $('#admin_fund').val(admin_amt);
            $('#account_fund').val(account_amt);
        }
    });
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
    $("#add_donor_form").validate({
        ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
        errorClass: 'validation-error-label', successClass: 'validation-valid-label',
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
        }, rules: {
            email: {
                email: true,
                remote: email_url,
            },
            amount: {
                positiveNumber: true,
            },
            zip: {
                zipcodeUS: true
            }
        },
        messages: {
            email: {
                remote: $.validator.format("Email address is already in use!")}
        },
        submitHandler: function (form) {
            $('#donor_btn_submit').attr('disabled', true);
            form.submit();
        }
    });
    //when the user clicks off of the zip field:
    $("#zip").on("keyup keydown change", function () {
        if ($(this).valid()) {
            var zip = $(this).val();
            var city = '';
            var state = '';
            var state_short = '';

            //make a request to the google geocode api
            $.getJSON('http://maps.googleapis.com/maps/api/geocode/json?address=' + zip)
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
                            $.each(cities, function (index, locality) {
                                var $option = $(document.createElement('option'));
                                $option.html(locality);
                                $option.attr('value', locality);
                                if (city == locality) {
                                    $option.attr('selected', 'selected');
                                }
                                $select.append($option);
                            });
                            $select.attr('id', 'city');
                            $select.attr('name', 'city');
                            $('#city_wrap').html($select);
                            $('#city').select2();
                        } else {
                            var txtbox = '<input type="text" name="city" id="city" placeholder="City" class="form-control" required="required" value="' + city + '" readonly>'
                            $('#city_wrap').html(txtbox);
                            $('#city').val(city);
                        }
                        $('#state').val(state);
                        $('#state_short').val(state_short);
                    });
        }
    });
    /*Validator method for positive number*/
    $.validator.addMethod('positiveNumber', function (value) {
        return Number(value) >= 0;
    }, 'Please enter positive number');
    /*Validator method for US Zipcode*/
    $.validator.addMethod("zipcodeUS", function (value, element) {
        return this.optional(element) || /^\d{5}-\d{4}$|^\d{5}$/.test(value);
    }, "The specified US ZIP Code is invalid");
</script>
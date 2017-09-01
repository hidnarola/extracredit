<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
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
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Fund Type <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select name="fund_type_id" id="fund_type_id" class="select2" required="required" data-placeholder="Select Fund Type" <?php echo $account_disabled ?>>
                                    <option value=""></option>
                                    <?php
                                    foreach ($fund_types as $type) {
                                        $selected = '';
                                        if (isset($donor) && $donor['fund_type_id'] == $type['id'])
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
                                <select name="account_id" id="account_id" class="select2" required="required" data-placeholder="Select account" <?php echo $account_disabled ?>>
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
                        <fieldset class="content-group">
                            <legend class="text-bold">Basic Donor Details</legend>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">First Name <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="firstname" id="firstname" placeholder="Enter First Name" class="form-control capitalize-text" required="required" value="<?php echo (isset($donor)) ? $donor['firstname'] : set_value('firstname'); ?>">
                                    <?php
                                    echo '<label id="firstname-error" class="validation-error-label" for="firstname">' . form_error('firstname') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Last Name <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="lastname" id="lastname" placeholder="Enter Last Name" class="form-control capitalize-text" required="required" value="<?php echo (isset($donor)) ? $donor['lastname'] : set_value('lastname'); ?>">
                                    <?php
                                    echo '<label id="lastname-error" class="validation-error-label" for="lastname">' . form_error('lastname') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Address <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <textarea name="address" id="address" placeholder="Enter Address" class="form-control capitalize-text" required="required"><?php echo (isset($donor)) ? $donor['address'] : set_value('address'); ?></textarea>
                                    <?php
                                    echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Email <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="<?php echo (isset($donor) && $donor['email']) ? $donor['email'] : set_value('email'); ?>">
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
                                            if (isset($donor) && $donor['state_id'] == $state['id'])
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
                                            if (isset($donor) && $donor['city_id'] == $city['id'])
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
                                    <input type="text" name="zip" id="zip" placeholder="Enter Zip" class="form-control" required="required" value="<?php echo (isset($donor) && $donor['zip']) ? $donor['zip'] : set_value('zip'); ?>">
                                    <?php
                                    echo '<label id="zip-error" class="validation-error-label" for="zip">' . form_error('zip') . '</label>';
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="content-group">
                            <legend class="text-bold">Payment Details</legend>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Date <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                        <input type="text" name="date" id="date" class="form-control pickadate" placeholder="Select Date" value="<?php echo (isset($donor)) ? date('d F,Y', strtotime($donor['date'])) : set_value('date'); ?>" required="required">
                                    </div>
                                    <?php
                                    echo '<label id="date-error" class="validation-error-label" for="date">' . form_error('date') . '</label>';
                                    ?>
                                </div>
                                <label class="col-lg-1 control-label">Post Date <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                        <input type="text" name="post_date" id="post_date" class="form-control pickadate" placeholder="Select Post Date" value="<?php echo (isset($donor)) ? date('d F,Y', strtotime($donor['post_date'])) : set_value('post_date'); ?>" required="required">
                                    </div>
                                    <?php
                                    echo '<label id="post_date-error" class="validation-error-label" for="post_date">' . form_error('post_date') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Amount <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="number" name="amount" id="amount" placeholder="Enter Amount" class="form-control" value="<?php echo (isset($donor) && $donor['amount']) ? $donor['amount'] : set_value('amount'); ?>" required="required">
                                    <?php
                                    echo '<label id="amount-error" class="validation-error-label" for="amount">' . form_error('amount') . '</label>';
                                    ?>
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
                                <label class="col-lg-1 control-label">Pmt Type <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <select name="payment_type_id" id="payment_type_id" class="select2" data-placeholder="Select Payment Type" required="required">
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
                                <label class="col-lg-1 control-label">Pmt Number <span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    <input type="text" name="payment_number" id="payment_number" placeholder="Enter Payment Number" class="form-control" value="<?php echo (isset($donor) && $donor['payment_number']) ? $donor['payment_number'] : set_value('payment_number'); ?>" required="required">
                                    <?php
                                    echo '<label id="payment_number-error" class="validation-error-label" for="payment_number">' . form_error('payment_number') . '</label>';
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-1 control-label">Memo </label>
                                <div class="col-lg-4">
                                    <textarea name="memo" id="memo" placeholder="Enter Memo" class="form-control capitalize-text"><?php echo (isset($donor)) ? $donor['memo'] : set_value('memo'); ?></textarea>
                                    <?php
                                    echo '<label id="memo-error" class="validation-error-label" for="memo">' . form_error('memo') . '</label>';
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button class="btn btn-success" type="submit" id="donor_btn_submit">Save <i class="icon-arrow-right14 position-right"></i></button>
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
    admin_donation = <?php echo $settings['admin-donation-percent'] ?>;
    account_donation = <?php echo $settings['program-donation-percent'] ?>;
    $('.pickadate').pickadate({
        max: new Date()
    });
    var edit = <?php echo $edit ?>;
    var email_url = site_url + 'donors/checkUniqueEmail/';
    if (edit == 1) {
        var append_id = <?php echo (isset($donor)) ? $donor['id'] : 0 ?>;
        email_url += btoa(append_id);
    }
    $('.select2').select2(); //-- Initialize select 2
    $(".switch").bootstrapSwitch(); //-- Initialize switch
    //
    //-- Donor Amount change eevent
    $("#amount").on("keyup keydown change", function (event) {
        if ($(this).val() != '' && Number($(this).val()) >= 0) {
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

    $("#add_donor_form").validate({
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
            email: {
                required: true,
                email: true,
                remote: email_url,
            },
            amount: {
                positiveNumber: true,
            }
        },
        messages: {
            email: {
                remote: $.validator.format("Email address is already in use!")
            }
        },
        submitHandler: function (form) {
            $('#donor_btn_submit').attr('disabled', true);
            form.submit();
        }
    });
    /*Validator method for positive number*/
    $.validator.addMethod('positiveNumber', function (value) {
        return Number(value) >= 0;
    }, 'Please enter positive number');
</script>
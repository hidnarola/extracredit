<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
<?php
$edit = 0;
$account_disabled = '';
if (isset($payment)) {
    $edit = 1;
    $account_disabled = 'disabled';
}
?>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>               
                <i class="icon-plus-circle2"></i>
                <span class="text-semibold"><?php echo $heading; ?></span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Account Transfer</li>
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
                    <form class="form-horizontal form-validate-jquery" action="" id="add_transfer_money_form" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Account Name<span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" placeholder="Account Name" class="form-control" name="account_id_from" id="account_id_from" value="<?php echo ($account['action_matters_campaign'] != '') ? $account['action_matters_campaign'] : $account['vendor_name'] ?>" readonly=""/>
                                <input type="hidden" name="hidden_account_id_from" value="<?php echo $account['id']; ?>">
                                <?php
                                echo '<label id="account_id_from-error" class="validation-error-label" for="account_id_from">' . form_error('account_id_from') . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label required" id="account_fund_label">Account Fund</label>
                            <div class="col-lg-6">
                                <input type="text" placeholder="Account Fund" class="form-control" name="account_fund" id="account_fund" value="<?php echo $account['total_fund'] ?>" disabled="disabled"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Fund Type <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select name="fund_type_id" id="fund_type_id" class="select2" required="required" data-placeholder="Select Fund Type" <?php echo $account_disabled ?>>
                                    <option value=""></option>
                                    <?php
                                    foreach ($fund_types as $type) {
                                        $selected = '';
                                        if (isset($payment) && $payment['fund_type_id'] == $type['id'])
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
                            <label class="col-lg-2 control-label">Program <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select name="account_id_to" id="account_id_to" class="select2" required="required" data-placeholder="Select account" <?php echo $account_disabled ?>>
                                    <option value=""></option>
                                    <?php
                                    foreach ($accounts as $account) {
                                        $selected = '';
                                        if (isset($payment) && $payment['account_id'] == $account['id'])
                                            $selected = 'selected';
                                        ?>
                                        <option value="<?php echo $account['id']; ?>" <?php echo $selected ?>><?php echo ($account['action_matters_campaign'] != '') ? $account['action_matters_campaign'] : $account['vendor_name'] ?></option>
                                    <?php } ?>
                                </select>
                                <?php
                                echo '<label id="account_id_to-error" class="validation-error-label" for="account_id_to">' . form_error('account_id_to') . '</label>';
                                ?>
                            </div>
                        </div>

<!--                        <div class="form-group" id="accoun_fund_div" <?php if (!isset($payment)) echo "style='display:none'" ?>>
                            <label class="col-lg-2 control-label required" id="account_fund_label">Account Fund</label>
                            <div class="col-lg-6">
                                <input type="text" placeholder="Account Fund" class="form-control" name="account_fund" id="account_fund" value="<?php echo $account_fund ?>" disabled="disabled"/>
                            </div>
                        </div>-->
                        <!--                        <fieldset class="content-group">
                                                    <legend class="text-bold">Payment Details</legend>
                        -->
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Amount <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="number" name="amount" id="amount" placeholder="Enter Amount" class="form-control" required="required" value="<?php echo (isset($payment) && $payment['amount']) ? $payment['amount'] : set_value('amount'); ?>">
                                <?php
                                echo '<label id="amount-error" class="validation-error-label" for="amount">' . form_error('amount') . '</label>';
                                ?>
                            </div>
                        </div>
                        <!--</fieldset>-->
                        <div class="form-group">
                            <div class="col-lg-12">
                                <button type="submit" name="save" class="btn bg-teal custom_save_button" id="transfer_btn_submit">Save<i class="icon-arrow-right14 position-right"></i></button>
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
    $('.pickadate').pickadate({
        max: new Date()
    });

    $('.select2').select2(); //-- Initialize select 2
    //-- fund type change event
    $('#fund_type_id').change(function () {
        var account_id = '<?php echo $account['id']; ?>';
        $.ajax({
            url: '<?php echo site_url('accounts/get_accounts_transfer') ?>',
            data: {id: btoa($(this).val()), account_id: btoa(account_id)},
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
                $('#account_id_to').empty().append(options);
                $("#account_id_to").select2("val", '');
            }
        });
    });
    //-- Account id change
    /* $('#account_id_to').change(function () {
     if ($(this).val() != null) {
     $.ajax({
     url: '<?php echo site_url('accounts/get_account_fund') ?>',
     data: {id: btoa($(this).val())},
     type: "POST",
     dataType: 'json',
     success: function (data) {
     $('#accoun_fund_div').show();
     if (data.type == 1) {
     $("#account_fund_label").html('Admin Fund');
     } else {
     $("#account_fund_label").html('Account Fund');
     }
     $("#account_fund").val(data.amount);
     var amt = parseFloat(data.amount);
     $('#amount').rules("add", {max: amt});
     }
     });
     }
     });*/

    $("#add_transfer_money_form").validate({
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
            label.addClass("validation-valid-label");
        },
        rules: {
            amount: {
                positiveNumber: true,
                max:<?php echo $account_fund ?>,
            }
        },
        submitHandler: function (form) {
            var amount_val = parseFloat($('#amount').val());
            var account_fund = parseFloat($('#account_fund').val());
            console.log('amount', amount_val);
            console.log('account fund', account_fund);
            if (amount_val > account_fund) {
                swal({
                    title: "Oops...",
                    text: "You can not enter amount value more than account's fund!",
                    confirmButtonColor: "#EF5350",
                    type: "error"
                });
                return false;
            } else {
                $('#transfer_btn_submit').attr('disabled', true);
                form.submit();
            }
        }
    });
    /*Validator method for positive number*/
    $.validator.addMethod('positiveNumber', function (value) {
        return Number(value) >= 0;
    }, 'Please enter positive number');
</script>
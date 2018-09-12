<style>
    .dt-button {display: block;width: 60px;height: 35px;background: #26A69A;padding: 6px;text-align: center;border-radius: 5px;color: white;font-weight: bold;margin-right: 4px;transition: all 0.3s ease-in-out;-webkit-transition: all 0.3s ease-in-out;-moz-transition: all 0.3s ease-in-out;text-decoration:none;border: 2px solid #fff;}
    .dt-buttons a:hover,.dt-buttons a:focus {color: #26A69A !important;background: #fff !important;border: 2px solid #26A69A;text-decoration:none;}
    .custom_perpage_dropdown .dataTables_length {margin: 0 18px 20px 20px;}
    .dataTables_info {padding: 8px 22px;margin-bottom: 10px;}
    .dataTables_paginate {margin: 10px 20px 20px 20px;}
</style>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/ui/moment/moment.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/anytime.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/picker.time.js"></script>
<script type="text/javascript" src="assets/js/plugins/pickers/pickadate/legacy.js"></script>
<script type="text/javascript" src="assets/js/pages/picker_date.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-coins"></i> <span class="text-semibold"><!--Accounts-->Award Recipients Report</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active"><!--Accounts-->Award Recipients Report</li>
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
            <?php } elseif ($this->session->flashdata('error')) {
                ?>
                <div class="alert alert-danger hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>                    
                    <strong><?php echo $this->session->flashdata('error') ?></strong>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="panel panel-flat custom_perpage_dropdown">
        <div class="panel-heading">
            <div class="row">
                <!-- <div class="col-md-6">
                    <label>Post date filter: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                        <?php
                        // $date_filter = date('m/01/Y') . ' - ' . date('m/t/Y'); // hard-coded '01' for first day
                        ?>
                        <input type="text" name="post_date_filter" id="post_date_filter" class="form-control daterange-basic" value="<?php echo $date_filter; ?>"> 
                    </div>
                </div> -->
            </div>
        </div>
        <table class="table"  id="donors_report">
            <thead>
                <tr>
                    <th>Program Name</th>
                    <th>Vendor Name</th>
                    <th>Email</th> 
                    <th>Phone</th> 
                    <th>Address</th>
                    <th>Website</th>              
                </tr>
            </thead>
            <tbody> 
            <?php foreach($accounts as $row) {?>
                <tr>                       
                    <td><?php if(isset($row['program_name']) and $row['program_name'] != ''){echo $row['program_name'];}else{echo '-';} ?></td>
                    <td><?php if(isset($row['vendor_name']) and $row['vendor_name'] != ''){echo $row['vendor_name'];}else{echo '-';} ?></td>
                    <td><?php if(isset($row['email']) and $row['email'] != ''){echo $row['email'];}else{ echo '-';} ?></td>
                    <td><?php if(isset($row['phone']) and $row['phone'] != ''){echo $row['phone'];}else{echo '-';} ?></td>
                    <td><?php if(isset($row['address']) and $row['address'] != ''){echo $row['address'];}else{echo '-';} ?></td>
                    <td><?php if(isset($row['website']) and $row['website'] != ''){echo $row['website'];}else{echo '-';} ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>

$(document).ready( function () {
    $(function () {
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    });
    
    $('#donors_report').DataTable({
        language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: 'lBfrtipx',
            buttons: [
                'excel',
                'csv',
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                }
            ],
    });
    
} );

</script>
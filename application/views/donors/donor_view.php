<style>
    .custom_scrollbar::-webkit-scrollbar { width: 0.4em; }
    .custom_scrollbar::-webkit-scrollbar-track { -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); }
    .custom_scrollbar::-webkit-scrollbar-thumb { background-color: #26A69A; outline: 1px solid slategrey; }
    .main_table > tbody > tr:nth-of-type(odd) {
        background-color: #E0F2F1;
    }
</style>
<table class="table table-striped table-bordered main_table" data-alert="" data-all="189">
    <tbody>
        <tr class="alpha-teal">
            <th>Fund Type</th>
            <td><?php echo $donor_details['name']; ?></td>
        </tr>
        <tr>
            <th>Program/AMC</th>
            <td><?php
                if ($donor_details['action_matters_campaign'] != '') {
                    echo $donor_details['action_matters_campaign'];
                } else {
                    echo $donor_details['vendor_name'];
                }
                ?></td>
        </tr>
        <tr class="alpha-teal">
            <th style="width:32%">Name</th>
            <td><?php echo $donor_details['firstname'] . ' ' . $donor_details['lastname']; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $donor_details['email']; ?></td>
        </tr>      
          
        <tr>
            <th>Address</th>
            <td><?php echo $donor_details['address']; ?></td>
        </tr>
        <tr>
            <th>State</th>
            <td><?php echo $donor_details['statename']; ?></td>
        </tr>  
        <tr>
            <th>City</th>
            <td><?php echo $donor_details['cityname']; ?></td>
        </tr>      
        <tr>
            <th>Zip</th>
            <td><?php echo $donor_details['zip']; ?></td>
        </tr>      

        <tr>
            <th>Date</th>
            <td><?php echo $donor_details['date']; ?></td>
        </tr>                   
        <tr>
            <th>Post Date</th>
            <td><?php echo $donor_details['post_date']; ?></td>
        </tr>                   
        <tr>
            <th>Amount</th>
            <td><?php echo $donor_details['amount']; ?></td>
        </tr>                   
        <tr>
            <th>Refund</th>
            <td><?php echo $donor_details['refund']; ?></td>
        </tr>                   
        <tr>
            <th>Payment Method</th>
            <td><?php echo $donor_details['payment_type']; ?></td>
        </tr>                   
<!--        <tr>
            <th>Refund</th>
            <td><?php echo $donor_details['refund']; ?></td>
        </tr>                   -->
        <tr>
            <th>Payment Number</th>
            <td><?php echo $donor_details['payment_number']; ?></td>
        </tr>                   
                    
       
          
    </tbody>
</table>
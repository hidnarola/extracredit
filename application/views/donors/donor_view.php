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
        <tr>
            <th>Name</th>
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
            <th>Amount</th>
            <td><?php echo '$' . $donor_details['amount']; ?></td>
        </tr>                   
    </tbody>
</table>
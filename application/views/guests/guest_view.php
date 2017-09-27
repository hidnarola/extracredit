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
            <th>Fund Type</th>
            <td><?php echo $guest_details['name']; ?></td>
        </tr>
        <tr>
            <th>Program/AMC</th>
            <td><?php
                if ($guest_details['action_matters_campaign'] != '') {
                    echo $guest_details['action_matters_campaign'];
                } else {
                    echo $guest_details['vendor_name'];
                }
                ?></td>
        </tr>
        <tr>
            <th style="width:32%">Name</th>
            <td><?php echo $guest_details['firstname'] . ' ' . $guest_details['lastname']; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $guest_details['email']; ?></td>
        </tr>      
        <tr>
            <th>Company Name</th>
            <td><?php echo $guest_details['companyname']; ?></td>
        </tr>   
        <tr>
            <th>Company Website</th>
            <td><?php echo $guest_details['company_website']; ?></td>
        </tr>   
        <tr>
            <th>Address</th>
            <td><?php echo $guest_details['address']; ?></td>
        </tr>
        <tr>
            <th>State</th>
            <td><?php echo $guest_details['statename']; ?></td>
        </tr>  
        <tr>
            <th>City</th>
            <td><?php echo $guest_details['cityname']; ?></td>
        </tr>      
        <tr>
            <th>Zip</th>
            <td><?php echo $guest_details['zip']; ?></td>
        </tr>      

        <tr>
            <th>Phone</th>
            <td><?php echo $guest_details['phone']; ?></td>
        </tr>                   
        <tr>
            <th>Invite Date </th>
            <td><?php echo $guest_details['invite_date']; ?></td>
        </tr>      
        <tr>
            <th>Guest Date </th>
            <td><?php echo $guest_details['guest_date']; ?></td>
        </tr>      
        <tr>
            <th>AIR Date </th>
            <td><?php echo $guest_details['AIR_date']; ?></td>
        </tr>      
        <tr>
            <th>AMC Created</th>
            <td><?php echo $guest_details['AMC_created']; ?></td>
        </tr>      
        <tr>
            <th>Assistant Name</th>
            <td><?php echo $guest_details['assistant']; ?></td>
        </tr>      
        <tr>
            <th>Assistant Phone </th>
            <td><?php echo $guest_details['assistant_phone']; ?></td>
        </tr>      
        <tr>
            <th>Assistant Email </th>
            <td><?php echo $guest_details['assistant_email']; ?></td>
        </tr>      
    </tbody>
</table>
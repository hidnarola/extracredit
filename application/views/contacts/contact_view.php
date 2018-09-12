<style>
    .custom_scrollbar::-webkit-scrollbar { width: 0.4em; }
    .custom_scrollbar::-webkit-scrollbar-track { -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); }
    .custom_scrollbar::-webkit-scrollbar-thumb { background-color: #26A69A; outline: 1px solid slategrey; }
    .main_table > tbody > tr:nth-of-type(odd) { background-color: #E0F2F1;}
</style>
<table class="table table-striped table-bordered main_table" data-alert="" data-all="189">
    <tbody>
        <tr>
            <th>Name</th>
            <td><?php echo $contact['name']; ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?php echo $contact['address']; ?></td>
        </tr>
        <tr>
            <th>City</th>
            <td><?php echo $contact['city']; ?></td>
        </tr>      
        <tr>
            <th>State</th>
            <td><?php echo $contact['state']; ?></td>
        </tr>   
        <tr>
            <th>Zipcode</th>
            <td><?php echo $contact['zip']; ?></td>
        </tr>   
        <tr>
            <th>Email</th>
            <td><?php echo $contact['email']; ?></td>
        </tr>
        <tr>
            <th>Contact Type</th>
            <td><?php echo $contact['contact_type']; ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?php echo $contact['phone']; ?></td>
        </tr>  
        <tr>
            <th>Website</th>
            <td><?php echo $contact['website']; ?></td>
        </tr>      
        <tr>
            <th>Created</th>
            <td><?php echo $contact['created']; ?></td>
        </tr>      
    </tbody>
</table>
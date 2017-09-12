<table class="table table-striped table-bordered" data-alert="" data-all="189">
    <tbody>
        <tr>
            <th style="width:32%">Name</th>
            <td><?php echo $user_details['firstname'] . ' ' . $user_details['lastname']; ?></td>
        </tr>
      
        <tr>
            <th>Email</th>
            <td><?php echo $user_details['email']; ?></td>
        </tr>
<!--        <tr class="alpha-teal">
            <th>Contact Number</th>
            <td><?php echo $user_details['contact_number'] ?></td>
        </tr>-->        
        <tr class="alpha-teal">
            <td colspan="2">
                <table class="table table-striped table-bordered" data-alert="" data-all="189">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Page Name</th>
                            <?php foreach ($columns as $columm) { ?>
                                <th><?php echo strtoupper(substr($columm['Field'], 3)) ?></th>
                                <?php
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $j = 1;
                        foreach ($permissions as $permission) {
                            $class = '';
                            if ($j % 2 == 0) {
                                $class = 'class="alpha-teal"';
                            }
                            ?>
                            <tr>
                                <td><?php echo $j ?></td>
                                <td><?php echo strtoupper(str_replace('_', ' ', $permission['page_name'])) ?></td>
                                <?php foreach ($columns as $columm) { ?>
                                    <td><?php
                                        if ($permission[$columm['Field']] == 1)
                                            echo '<i class="icon-checkmark4" style="color: #4caf50;"></i>';
                                        else
                                            echo '<i class="icon-cross2" style="color: #f44336;"></i>';
                                        ?></td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                            $j++;
                        }
                        ?>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

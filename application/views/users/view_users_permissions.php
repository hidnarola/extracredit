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
            <th style="width:32%">Name</th>
            <td><?php echo $user_details['firstname'] . ' ' . $user_details['lastname']; ?></td>
        </tr>

        <tr>
            <th>Email</th>
            <td><?php echo $user_details['email']; ?></td>
        </tr>      
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

{{-- custom return value --}}
<td>
	<?php
        echo $entry->{$column['function_name']}($column['key']);
    ?>
</td>
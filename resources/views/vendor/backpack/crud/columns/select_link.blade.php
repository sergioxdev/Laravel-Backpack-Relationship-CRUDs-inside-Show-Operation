{{-- single relationships (1-1, 1-n) --}}
<span>
    <?php
        $attributes = $crud->getModelAttributeFromRelation($entry, $column['entity'], $column['attribute']);
        if (count($attributes)) 
        {
        	if($column['link'])
        	{ 
                $name_link = str_replace('s_id', '', $column['name']);
                ?>
        		<a href="{!! url(config('backpack.base.route_prefix', 'admin')) !!}/{!! $name_link !!}/{!! $entry->{$name_link}->id !!}">
        			<?php echo e(implode(', ', $attributes)); ?>
                </a>
            <?php
        	}
        	else
        	{
        		echo e(implode(', ', $attributes));
        	}
            
        } else {
            echo '-';
        }
    ?>
</span>
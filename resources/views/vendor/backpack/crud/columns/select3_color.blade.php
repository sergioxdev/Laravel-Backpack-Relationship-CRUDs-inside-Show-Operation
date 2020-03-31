{{-- single relationships (1-1, 1-n) --}}

<?php
if(isset($column['uicolor']))
{
    $attributes = $crud->getModelAttributeFromRelation($entry, $column['entity'], $column['uicolor']);
    $style = 'style="color: '.implode(', ', $attributes).';font-weight:bold"';
}
else
{
    $style = '';
}
?>

<span {!!$style!!}>
    <?php
        $attributes = $crud->getModelAttributeFromRelation($entry, $column['entity'], $column['attribute']);
        if (count($attributes)) {
            echo e(implode(', ', $attributes));
        } else {
            echo '-';
        }
    ?>
</span>
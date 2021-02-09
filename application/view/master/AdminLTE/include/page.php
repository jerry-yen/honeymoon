<?php 
	if($this -> loader -> is_exists("pagination")):
		$page_info = $this -> pagination -> get_page_list(); 
		$next_page = $this -> pagination -> get_next_page();
		$prev_page = $this -> pagination -> get_previous_page();
?>
<div class="box-footer clearfix">
    <ul class="pagination pagination-sm no-margin pull-right">
    	
    	<?php if($prev_page -> has_previous): ?>
    	<li><a href="<?php echo $prev_page -> url;?>">&laquo;</a></li>
    	<?php endif; ?>
    	
    	<?php foreach($page_info as $key => $page): ?>
		<?php if($page->current): ?>
    	<li class="active"><a href="javascript:void(0);"><?php echo $key; ?></a></li>
    	<?php else: ?>
        <li><a href="<?php echo $page -> url; ?>"><?php echo $key; ?></a></li>
        <?php endif; ?>
		<?php endforeach; ?>
        
        <?php if($next_page -> has_next): ?>
        <li><a href="<?php echo $next_page -> url;?>">&raquo;</a></li>
        <?php endif; ?>
	</ul>
</div>

<?php endif; ?>
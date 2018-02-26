<?php if(isset($pager)) { ?>
    <div class="pager">
        <?php if ($pager['page'] -1 >= 1) { ?>
            <a href="<?php echo current_url(); ?>?page=<?php echo $pager['page']-1; ?><?php echo $pager['uri']; ?>" class="">&laquo; PREV</a>
        <?php } else { ?>
            <a href="<?php echo current_url(); ?>?page=<?php echo $pager['max']; ?><?php echo $pager['uri']; ?>" class="">&laquo; PREV</a>
        <?php } ?>
        <?php if ($pager['page'] -3 >= 1) { ?>
            <a href="<?php echo current_url(); ?>?page=<?php echo $pager['page']-3; ?><?php echo $pager['uri']; ?>" class=""><?php echo $pager['page']-3; ?></a>
        <?php } ?>
        <?php if ($pager['page'] -2 >= 1) { ?>
            <a href="<?php echo current_url(); ?>?page=<?php echo $pager['page']-2; ?><?php echo $pager['uri']; ?>" class=""><?php echo $pager['page']-2; ?></a>
        <?php } ?>
        <?php if ($pager['page'] -1 >= 1) { ?>
            <a href="<?php echo current_url(); ?>?page=<?php echo $pager['page']-1; ?><?php echo $pager['uri']; ?>" class=""><?php echo $pager['page']-1; ?></a>
        <?php } ?>
        <a href="<?php echo current_url(); ?>?page=<?php echo $pager['page']; ?><?php echo $pager['uri']; ?>" class="on"><?php echo $pager['page']; ?></a>
        <?php if ($pager['page'] +1 <= $pager['max']) { ?>
            <a href="<?php echo current_url(); ?>?page=<?php echo $pager['page']+1; ?><?php echo $pager['uri']; ?>" class=""><?php echo $pager['page']+1; ?></a>
        <?php } ?>
        <?php if ($pager['page'] +2 <= $pager['max']) { ?>
            <a href="<?php echo current_url(); ?>?page=<?php echo $pager['page']+2; ?><?php echo $pager['uri']; ?>" class=""><?php echo $pager['page']+2; ?></a>
        <?php } ?>
        <?php if ($pager['page'] +3 <= $pager['max']) { ?>
            <a href="<?php echo current_url(); ?>?page=<?php echo $pager['page']+3; ?><?php echo $pager['uri']; ?>" class=""><?php echo $pager['page']+3; ?></a>
        <?php } ?>
        <?php if ($pager['page'] +1 <= $pager['max']) { ?>
            <a href="<?php echo current_url(); ?>?page=<?php echo $pager['page']+1; ?><?php echo $pager['uri']; ?>" class="">NEXT &raquo;</a>
        <?php } else { ?>
            <a href="<?php echo current_url(); ?>?page=1<?php echo $pager['uri']; ?>" class="">NEXT &raquo;</a>
        <?php } ?>
    <!--共<?php echo $pager['count']; ?>条记录|每页<?php echo $pager['value']; ?>条|当前第<?php echo $pager['page']; ?>页-->
    </div>
<?php } ?>


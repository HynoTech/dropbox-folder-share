<?php

?>
<div>
    <img src="<?php echo parent::$url.'img/logo.png'; ?>" />
    <form action="options.php" method="post">
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">
                <div id="post-body-content">

                    
    <?php settings_fields(parent::_OPT_SEETINGS_); ?>
                    <?php echo parent::_OPT_SEETINGS_; ?>
                </div>
            
                    
                    
            </div>
        </div>
    </form>
</div>
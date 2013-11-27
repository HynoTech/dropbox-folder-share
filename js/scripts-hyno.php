<?php
$contentHeight = get_option("CFS_height");
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        // learn more
        $('.CFS_Content').each(function() {
            alto = $(this).height();
            max = <?php echo $contentHeight; ?>;
            if (alto > max) {
                $(this).height(max);
            }
            //alert(alto);
        })
    });
</script>
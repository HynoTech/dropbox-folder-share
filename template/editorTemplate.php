<style type="text/css">

    .shortcodeContenido {
        margin: 13px 0;
        padding: 16px;
        background-color: white;
        border: 1px solid #ccdbeb;
        border-radius: 2px;
        /*width: 100%;*/
    }

    .shortcodeContenido .title {
        font-size: 1.4em;
        /*margin: 0px 15px 0 0px;*/
        margin-left: 10px;
        margin-right: 0;
        margin-top: 0;
        margin-bottom: 0;
        /*font-weight: 600;*/
        color: #72777c;
        height: 32px;
        width: 96%;
    }

    .shortcodeContenido .content {
        margin: 1px 0 0 0px;
        padding: 0;
        color: #5799a7;
    }

    div.container5 {
        display: flex;
        align-items: center
    }

    .dbfoldershare {
        margin-left: 10px;
        color: #a1a1a1;
        font-size: 0.5em;
        /*right: 10px;
        position: absolute;
        margin-right: 10px*/
    }

</style>
<?php
//print_r( plugins_url( '../img/TinyMCE_Button.png', __FILE__ ) );
?>
<div class="boutique_banner_{{ data.type }}"></div>
<div class="shortcodeContenido" id="banner_{{ data.id }}">
    <div class="container5">
        <img src="<?php echo plugins_url( '../img/TinyMCE_Button.png', __FILE__ ); ?>" height="32px">
        <# if ( data.titulo ) { #>
            <p class="title">{{ data.titulo }}<span class="dbfoldershare">Dropbox Folder Share</span></p>
            <# } else { #>
                <p class="title"> -- <span class="dbfoldershare">Dropbox Folder Share</span></p>
                <# } #>
    </div>
    <!--<span class="dbfoldershare">Dropbox Folder Share</span>
    <hr>
    <span class="content">{{ data.link }} </span>
    <a href="{{ data.link }}" class="link dtbaker_button_light">{{ data.titulo }}</a>

    <# if ( data.link ) { #>
        <# if ( data.linkhref ) { #>
            <a href="{{ data.linkhref }}" class="link dtbaker_button_light">{{ data.link }}</a>
            <# } #>
                <# } #>
                    -->
</div>
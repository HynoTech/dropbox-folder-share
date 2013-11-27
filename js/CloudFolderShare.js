// JavaScript Document
(function() {
    tinymce.PluginManager.requireLangPack('CloudFolderShare');
    tinymce.create('tinymce.plugins.CloudFolderSharePlugin', {
        init: function(ed, url) {
            ed.addCommand('mceCloudFolderShare', function() {
                ed.windowManager.open({
                    file: url + '/CloudFolderShare_tinymce.php',
                    width: 450 + ed.getLang('CloudFolderShare.delta_width', 0),
                    height: 210 + ed.getLang('CloudFolderShare.delta_height', 0),
                    inline: 1
                }, {
                    plugin_url: url, // Plugin absolute URL
                    some_custom_arg: 'custom arg' // Custom argument
                });
            });
            ed.addButton('CloudFolderShare', {
                title: 'Cloud Folder Share by Hyno',
                image: url + '/CloudFolderShare.png',
                cmd: 'mceCloudFolderShare'
                        /*
                         onclick : function() {
                         ed.selection.setContent('[tinyplugin]' + ed.selection.getContent() + '[/tinyplugin]');
                         
                         }
                         */
            });
        },
        createControl: function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('CloudFolderShare', tinymce.plugins.CloudFolderSharePlugin);
})();
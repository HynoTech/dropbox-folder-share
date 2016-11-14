// JavaScript Document
(function() {
    tinymce.PluginManager.requireLangPack('DropBoxFolderShare');
    tinymce.create('tinymce.plugins.DropBoxFolderSharePlugin', {
        init: function(ed, url) {
            ed.addCommand('mceDropBoxFolderShare', function() {
                ed.windowManager.open({
                    file: url + '/DropBoxFolderShare_tinymce.php',
                    width: 450 + ed.getLang('DropBoxFolderShare.delta_width', 0),
                    height: 240 + ed.getLang('DropBoxFolderShare.delta_height', 0),
                    inline: 1
                }, {
                    plugin_url: url, // Plugin absolute URL
                    some_custom_arg: 'custom arg' // Custom argument
                });
            });
            ed.addButton('DropBoxFolderShare', {
                title: 'Dropbox Folder Share',
                image: url + '/DropBoxFolderShare.png',
                cmd: 'mceDropBoxFolderShare'
            });
        },
        createControl: function(n, cm) {
            return null;
        }
    });
    tinymce.PluginManager.add('DropBoxFolderShare', tinymce.plugins.DropBoxFolderSharePlugin);
})();
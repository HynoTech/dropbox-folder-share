const { registerBlockType } = wp.blocks;

( function() {

} )();
var el = wp.element.createElement;
var mb_icon = el ("img", {
    src: DFSParams.pluginUrl + "src/img/HT.svg",
    width: "50px",
    height: "50px"
});
wp.blocks.updateCategory( 'hynotech', { icon: mb_icon } );
var iconDFS = el ("img", {
    src: DFSParams.pluginUrl + 'src/img/TinyMCE_Button.png',
    width: "50px",
    height: "50px"
});

registerBlockType( 'dropbox-folder-share/block-editor', {
    title: 'Dropbox Folder Share',
    icon: iconDFS,
    category: 'hynotech',

    attributes: {
        url: {
            type: 'string',
            default: '',
        },
        view_icons: {
            type: 'boolean',
            default: true,
        },
        view_size: {
            type: 'boolean',
            default: true,
        },
        view_edited: {
            type: 'boolean',
            default: true,
        },
    },

    edit: function( props ) {
        /*
        if ( !props.attributes.url ) {
            return "FAltan Datos";
        }
        */

        function actualizarUrl(e) {
            props.setAttributes({
                url: e.target.value,
            })
        }
        function actualizarOpc(est, event) {
            props.setAttributes({
                view_icons: ('view_icons' == est) ? event.target.checked : props.attributes.view_icons,
                view_size: ('view_size' == est) ? event.target.checked : props.attributes.view_size,
                view_edited: ('view_edited' == est) ? event.target.checked : props.attributes.view_edited,
            });

        }

        return (
            <div className="blockContenidoDFS">
                <table border={0} cellPadding={10} width="100%">
                    <tbody>
                    <tr>
                        <td width="60px">
                            <img src={ DFSParams.pluginUrl + 'src/img/TinyMCE_Button.png' } height="32px" />
                        </td>
                        <td>
                            <p className="title">
                                Dropbox Folder Share
                            </p>
                            <div className="secUrl">
                                <label htmlFor="url">URL: </label>
                                <input type="text" id="url" defaultValue={props.attributes.url} onBlur={actualizarUrl}/>
                            </div>
                            <div className="secOpc">
                                <label><input type="checkbox" checked={props.attributes.view_icons} onChange={(e) => actualizarOpc('view_icons', e)} /> Ver Iconos </label>
                                <label><input type="checkbox" checked={props.attributes.view_size} onChange={(e) => actualizarOpc('view_size', e)} /> Ver Tamaño </label>
                                <label><input type="checkbox" checked={props.attributes.view_edited} onChange={(e) => actualizarOpc('view_edited', e)} /> Ver Editado </label>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>

        )
    },
    // edita: ( { className } ) => <div className={ className }>Hello Wdorld!</div>,
    // save: () => <div>Hello World!</div>,
    save: function (props) {
        console.log(props);
        return (
            <p>[DFS link="{props.attributes.url}" show_icon="{props.attributes.view_icons.toString()}" show_size="{props.attributes.view_size.toString()}" show_change="{props.attributes.view_edited.toString()}"]</p>
        )
    }
} );

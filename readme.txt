=== Dropbox Folder Share ===
Contributors: Hyno, antony_salas
Tags: Dropbox, Folder,Cloud, Folder Share, post, content, contenido, nube
Requires at least: 3.0
Tested up to: 4.8.1
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link:  http://goo.gl/EeMhVd

If you want to share a folder on your Dropbox account along with all the content, then this plugin will do it for you only with Dropbox shared URL.

== Description ==

Many times it is necessary to be able to share content of our folder in the cloud with our readers, and the necessity of this is that this plugin was born.

With this plugin you can:

*   Include content from a Dropbox shared folder.
*   Navigate between sub folders.
*   Thumbnails for preview images
*   ShortCode and button in the editor.


Read **[More](http://www.hynotech.com/wp-plugins/dropbox-folder-share/ "Dropbox Folder Share")**
    
Project in **[GitHub](https://github.com/HynoTech/dropbox-folder-share/ "Dropbox Folder Share in GitHub")**

== Installation ==

= Minimum Requirements =

*   Enabled **php_openssl** in php.ini
*   PHP version 5.3.3 or greater
*   PHP **cURL** module installed

= Manual installation =

1. Upload the contents of `dropbox-folder-share.zip` to the `/wp-content/plugins/` directory or use WordPress’ built-in plugin install tool
2. If you installed manually, enable it
3. Once installed, you can access the plugins settings page.

Read **[More](http://www.hynotech.com/wp-plugins/dropbox-folder-share/#installation "Dropbox Folder Share")**

== Frequently Asked Questions ==

= ¿Error? =

Report it to author.

** If you have any questions about this plugin, we will be happy to solve it **

Read **[More](http://www.hynotech.com/wp-plugins/dropbox-folder-share/#faq "Dropbox Folder Share")**

== Screenshots ==

1. Button in editor
2. Section in editor
3. Widget window
4. Editable section
5. Editor windows
6. Content published in subfolder
5. General settings

== Changelog ==

= 1.8.1 =
* FIX some errors
* FOPEN is deprecated, now only work with CURL

= 1.8 =
* Added thumbnails
* Added Witdget module
* Added widget in editor
* Using Dropbox native viewer

= 1.7.x =
* Solucion errores de listado de carpetas

= 1.7 =
* Cambio en Nucleo.
* Agregado ver imagenes en thickbox
* Agregado ver algunos archivos (segun lista) en thickbox
* Agregado configurar altura maxima del contenedor de archivos.
* Retiradondo opcion Ver como ICONOS (no es usado)
* Agregado notificacion de carpeta vacia
* Agregando notificacion de LINK incorrecto

= 1.6.x =
* Correccion de errores de idioma en barra de titulos
* Agregado Italiano (Gracias René Querin)

= 1.6.0 =
* Agregado navegacion entre carpetas
* Modificado descarga de archivos ( carpetas )

= 1.5.x =
* Correccion de errores de fuente

= 1.5.0 =
* Correccion de errores de fuente
* Agregado desgarga de archivos
* Agregado descarga de Carpeta en ZIP
* Agregado visualizacion de imagenes
* Agregado cargar contenido via AJAX

= 1.4.x =
* Correccion de errores de fuente

= 1.4 =
* Agregado soporte para caracteres utf-8
* Agregado traduccion al Aleman (Gracias Michael Koloff)

= 1.3.x =
* Correccion de errores de fuente

= 1.3 =
* Implementado de cURL.
* Soporte multiidioma (solo en cURL)
* Descarga de archivos.
* Link a Carpeta Dropbox.

= 1.2 =
* Cambio de Fuente de Dropbox.

= 1.1 =
* Solucion a Bugs reportados.

= 1.0 =
* Version inicial.

== Upgrade Notice ==

= 1.8.1 =
* [-] fopen option is deprecated, now use curl.
* [+] Fix

= 1.8 =
* [New] Now thumbnails can be showed in content
* [New] Widget module can be used.
* [New] Editor widget for Dropbox Folder Share

= 1.7.x =
* Solucion de errores

= 1.7 =
* [Nueva Opcion] Extenciones en ThickBox (No todas las extensiones funcionan, tener cuidado)
* [Nueva Opcion] Altura maxima de contenedor (0 para desabilitar)
* Retiradondo opcion Ver como ICONOS (no es usado)
* Agregado notificacion de carpeta vacia
* Agregando notificacion de LINK incorrecto

= 1.6.x =
* Correccion de errores de idioma en barra de titulos
* Agregado Italiano (Gracias René Querin)

= 1.6.0 =
* Agregado navegacion entre carpetas; Ahora puedes navegar en los subdirectorios de tu carpeta compartida (mediante AJAX)

= 1.5.x =
* Correccion de errores de fuente

= 1.5.0 =
* Correccion de errores de fuente
* Agregado desgarga de archivos
* Agregado descarga de Carpeta en ZIP
* Agregado visualizacion de imagenes
* Agregado cargar contenido via AJAX

= 1.4.x =
* Correccion de errores por cambio de codigo en Dropbox.com

= 1.4 =
* Agregado soporte para caracteres utf-8 (ä/ö/ü)
* Agregado traduccion al Aleman (Gracias Michael Koloff)

= 1.3.x =
* Correccion de errores por cambio de codigo en Dropbox.com

= 1.3 =
* No funcionaba la importacion de contenido mediante cURL (Solucionado).
* Cambio de Shortchode (antes: [dropbox-foldershare-hyno ...]   ahora: [DFS ....]) - El anterior no deja de funcionar.
* Agregado el soporte para internacionalizacion (solo mediante cURL)
* Habilitado el soporte para descarga de archivos.
* Habilitado Link a carpeta en dropbox.
* Modificacion en codigo interno.

= 1.2 =
* Dropbox Cambio forma de acceso a los archivos y esto provocaba que no se mostraran los nombres de archivos (Solucionado).

= 1.1 =
* Gracias a su colaboracion al reportar bugs en el plugin, se pudo solucionar muchos de ellos.
* Ahora cuando se publica un URL de Dropbox inexistente, no es Agregado.

= 1.0 =
* Iniciando proyecto.

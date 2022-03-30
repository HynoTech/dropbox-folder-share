<?php


namespace HynoTech\Drives\Adicionales;


class Carpeta {
	public $id;
	public $nombre;
	public $fechaCreacion;
	public $href;
	public $linkKey;
	public $linkSecureHash;
	public $linkSubPath;
	public $linkType;
	public $propietario;
	public $subCarpetas;
	public $archivos;
	public $dataOriginal;
    public $numSubCarpetas;
    public $numArchivos;
	public function numSubCarpetas() {
        $this->numSubCarpetas = count($this->subCarpetas);
    }
	public function numArchivos() {
        $this->numArchivos = count($this->archivos);
    }
}

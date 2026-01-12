<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class MyConfig extends BaseConfig
{
    //https://www.google.com/recaptcha/admin/site/485994590
    public $name = "C14 MIGRATION";
	public $author = "FIKY ASHARIZA";
    public $recaptha_sitekey = '6LdesPccAAAAAKEzBR5AAvGexBDLOqF3noTDweki';
    public $recaptha_secret = "6LdesPccAAAAALW7qhzuPDXVad11wCwujPuqfiRU";

	public function get_info(){
		return $this->name. " - ". $this->author;
	}


}

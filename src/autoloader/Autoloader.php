<?php

namespace autoloader;

class Autoloader {

    private string $prefixe;
    private string $chemin;

    public function __construct(string $preNamespace, string $cheminRep) {
        $this->prefixe = $preNamespace;
        $this->chemin = $cheminRep;
    }

    public function loadClass(string $classname) {
        $path_to_file = str_replace(array($this->prefixe,'\\'),array($this->chemin,'/'),$classname) . '.php';
        if (is_file($path_to_file)) {

            require_once $path_to_file;
        }
    }

    public function register() {
        spl_autoload_register([__CLASS__, 'loadClass']);
    }
}
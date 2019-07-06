<?php

class Autoloader
{
    private $dir;

    public function __construct($dir) {
        $this->dir = $dir;
        spl_autoload_register(array($this, 'autoload'));
    }

    public function autoload($class_name) {
        $class = str_replace('\\', DS, $class_name);
        $lib_class = strtolower($class) . '.php';
        $class_root = $this->dir;

        $file = $class_root . DS . $lib_class;

        if(file_exists($file)) {
            include_once $file;
            return true;

            if(class_exists($class)) {
                return true;
            }
        }
        return false;
    }
}

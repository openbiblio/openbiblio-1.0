<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

/**
 * Autoload classes. You then need only one include.
 */

class class_autoloader
    {
    protected $class_path;
    protected $class_suffix = '.php';
    public function __construct()
        {
        $this->class_path = dirname(__FILE__) . '/';
        spl_autoload_register(array($this, 'autoload'));
        }
    private function autoload($class)
        {
        $path = $this->class_path . $class . $this->class_suffix;
        if (file_exists($path)) { require_once $path; }
        }
    }

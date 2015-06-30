<?php

namespace System\Std;

final class Environment {
    private static $includes = array();
    private static $rootPath;
    private static $appPath;
    private static $namespaces = array();
    private static $cultureInfo;

    public static function addClassFile($file){
        self::$includes[] = $file;
    }
    
    public static function hasClassFile($file){
        if(in_array($file, self::$includes)){
            return true;
        }
        return false;
    }
    
    public static function getLoadedClassFiles(){
        return self::$includes;
    }

    public static function setRootPath($path){
        self::$rootPath = $path;
    }
    
    public static function getRootPath(){
        return self::$rootPath;
    }
    
    public static function setAppPath($path){
        self::$appPath = $path;
    }
    
    public static function getAppPath(){
        return self::$appPath;
    }
    
    public static function setNamespaces(array $namespaces){
        self::$namespaces = $namespaces;
    }
    
    public static function getNamespaces(){
        return self::$namespaces;
    }
    
    public static function setCulture(\System\Globalization\CultureInfo $cultureInfo){
        self::$cultureInfo = $cultureInfo;
    }
    
    public static function getCulture(){
        return self::$cultureInfo;
    }
}

?>
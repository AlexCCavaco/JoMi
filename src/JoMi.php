<?php

namespace JoMi;

use MatthiasMullie\Minify;

class JoMi {

    protected static $settings = [
        "moduleFilesLocation" => __dir__."/../../../../var/jomi/",
        "fileBasePath" => __dir__."/../../../../"
    ];

    /**
     * @param string $name Module Name
     * @throws \Exception
     */
    static function runModule($name){
        if(self::$settings['moduleFilesLocation']===null) $moduleLocation = __dir__."/../../../../var/jomi/".$name.'.json';
        else $moduleLocation = self::$settings['moduleFilesLocation'].$name.'.json';
        $root = '';
        if(isset(self::$settings['fileBasePath'])) $root = self::$settings['fileBasePath'];
        $module = new JoMiModule($name,$moduleLocation,$root);
        self::run($module);
    }

    /**
     * @param string $to
     * @param string ...$files
     * @return bool
     * @throws \Exception
     */
    static function minify($to,...$files){
        $extension = pathinfo($to)['extension'];
        if(strtoupper($extension)==='js') $min = new Minify\JS();
        else if(strtolower($extension)==='css') $min = new Minify\CSS();
        else throw new \Exception('File Extension is invalid for file "'.$to.'"!');
        $min->add($files)->minify($to);
        return true;
    }

    /**
     * @param JoMiModule $module
     * @return bool
     * @throws \Exception
     */
    static function run(JoMiModule $module){
        if($module->updated()) return $module->run(true);
        return true;
    }

}

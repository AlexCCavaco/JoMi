<?php

namespace JoMi;

class JoMi {

    protected static $module_location = __dir__."/../../../../var/jomi/";
    protected static $file_base_path = __dir__."/../../../../";

    /**
     * @param string $name
     * @param array $settings
     * @param array $vars
     * @return JoMiLocale
     */
    public static function runUsing($name,$settings=[],array $vars=[]){
        self::set($settings);
        $vars = array_merge(['base'=>self::$file_base_path],$vars);
        return new JoMiLocale($name,self::$module_location,$vars);
    }

    /**
     * @param string $name
     * @param array $settings
     * @param array $vars
     * @return bool
     */
    public static function runModule($name,$settings=[],array $vars=[]){
        self::set($settings);
        $vars['base'] = self::$file_base_path;
        return (new JoMiModule($name,self::$module_location,self::$file_base_path,$vars))->run(true);
    }

    /**
     * @param array $settings
     */
    protected static function set($settings=[]){
        if(isset($settings['module-location'])) self::$module_location = $settings['module-location'];
        if(isset($settings['file-base-path'])) self::$file_base_path = $settings['file-base-path'];
    }

}

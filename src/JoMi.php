<?php

namespace JoMi;

class JoMi {

    protected static $module_location = __dir__."/../../../../var/jomi/";
    protected static $file_base_path = __dir__."/../../../../";

    protected $vars=[];

    public function __construct($settings=[],$vars=[]){
        if(isset($settings['module-location'])) self::$module_location = $settings['module-location'];
        if(isset($settings['file-base-path'])) self::$file_base_path = $settings['file-base-path'];
        $this->vars = $vars;
    }

    public static function runModule($name,$settings=[]){
        $mod = new self($settings);
        $module = new JoMiModule($name,self::$module_location,self::$file_base_path,$mod->vars);
        $mod->run($module);
    }

    public function run(JoMiModule $module){
        return $module->run(true);
    }

}

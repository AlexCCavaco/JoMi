<?php

namespace JoMi;

class JoMiLocale {

    private $name;
    private $location;
    private $updated = 0;
    private $toUpdate = false;
    private $upSet = [];
    private $data = [];

    /**
     * @var JoMiSet[]
     */
    private $sets = [];
    private $vars = [];

    /**
     * JoMiModule constructor
     * @param string $name
     * @param string $location
     * @param array $vars
     * @throws \Exception
     */
    public function __construct($name,$location,array $vars=[]){
        $this->name = $name;
        foreach($vars as $k=>$var) $this->vars[$k] = $this->insertParameters($var,$this->vars);
        $this->location = $location.'/jomi-mods.json';
        $this->load();
    }

    protected function load(){
        $content = file_get_contents($this->location);
        if($content===false||($data=json_decode($content,true))===false) $data = [];
        if(isset($data[$this->name])) $this->updated = $data[$this->name];
        $this->data = $data;
    }

    /**
     * @param array $files
     * @param string $into
     * @param string $type
     * @return $this
     */
    public function add($files,$into,$type){
        $this->sets[] = $set = new JoMiSet($files,$into,$type,$this->vars);
        $this->toUpdate |= ($this->upSet[]=!$set->updated($this->updated));
        return $this;
    }

    /**
     * @param bool $save
     * @return bool
     */
    public function run($save=true){
        if(!$this->toUpdate) return true;
        $res = true;
        foreach($this->sets as $k=>$set) if($this->upSet[$k]) $res &= $set->run();
        $this->data[$this->name] = time();
        if($save) $this->save();
        return $res;
    }

    /**
     * @return bool
     */
    public function save(){
        return file_put_contents($this->location,json_encode($this->data,JSON_PRETTY_PRINT))!==false;
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    protected function insertParameters(string $path,array $params){
        $path = preg_replace_callback('/{(.*?)}/',function($match) use ($params){
            if(!isset($params[$match[1]]))
                trigger_error("Variable \"{$match[1]}\" doesn't exist in module or globally!",E_USER_WARNING);
            return $params[$match[1]]??'';
        },$path);
        return str_replace('//','/',$path);
    }

}

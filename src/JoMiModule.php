<?php

namespace JoMi;

class JoMiModule {

    private $name;
    private $location;
    private $baseDir = '';

    /**
     * @var JoMiModuleSet[]
     */
    private $sets = [];
    private $vars = [];

    /**
     * JoMiModule constructor
     * @param string $name
     * @param string $location
     * @param string $baseDir
     * @param array $vars
     * @throws \Exception
     */
    public function __construct($name,$location,$baseDir='',$vars=[]){
        $this->name = $name;
        $this->vars = $vars;
        $this->location = $location.'/'.$name.'.json';
        $this->baseDir = $baseDir;
        $this->loadModule();
    }

    /**
     * @throws \Exception
     */
    protected function loadModule(){
        if(!file_exists($this->location)) throw new \Exception('The module config file doesn\'t exist on "'.$this->location.'"');
        $contents = file_get_contents($this->location);
        if($contents===false) throw new \Exception('Error Reading Module File on "'.$this->location.'"!');
        $data = json_decode($contents,true);
        if($data===false) throw new \Exception('Error Reading Module File on "'.$this->location.'"!');
        $root = __dir__.'/../../../../'; $vars = ['root'=>$root,'base'=>$this->baseDir];
        if(isset($data['var'])){
            if(empty($this->vars)) $this->vars = $data['var'];
            else $this->vars = array_merge($this->vars,$data['var']);
            foreach($data['var'] as $var=>$val) $vars[$var] = $this->insertParameters($val,$vars);
        }
        if(empty($data['join']??[])) throw new \Exception('No set of files to join on Module File on "'.$this->location.'"!');
        foreach($data['join'] as $set) $this->sets[] = new JoMiModuleSet($set,$this->location,$vars);
    }

    /**
     * @param bool $save
     * @return bool
     * @throws \Exception
     */
    public function run($save=true){
        $data = []; $up = false;
        foreach($this->sets as $set){
            $up = $set->run()||$up;
            $data[] = $set->getData();
        }
        if($save&&$up) return $this->save($data);
        return $up;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function save($data){
        return file_put_contents($this->location,json_encode(['var'=>$this->vars,'join'=>$data],JSON_PRETTY_PRINT))!==false;
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

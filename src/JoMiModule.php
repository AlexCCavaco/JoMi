<?php

namespace JoMi;

class JoMiModule {

    private $name;
    private $location;

    /**
     * @var JoMiSet[]
     */
    private $sets = [];

    private $root = '';
    private $files = [];
    private $into = '';
    private $updated = true;
    private $type = '';

    /**
     * JoMiModule constructor
     * @param string $name
     * @param string $location
     * @param string $root
     * @throws \Exception
     */
    public function __construct($name,$location,$root=''){
        $this->name = $name;
        $this->location = $location;
        $this->root = $root;
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
        foreach($data as $set) $this->sets[] = new JoMiSet($set,$this->location);
    }

    /**
     * @param bool $save
     * @return bool
     */
    public function run($save=true){
        $data = [];
        foreach($this->sets as $set){
            $data[] = $set->run();
        }
        if($save) $this->save($data);
        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function save($data){
        return file_put_contents($this->location,json_encode($data))!==false;
    }

}

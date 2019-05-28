<?php

namespace JoMi;

use MatthiasMullie\Minify;

class JoMiSet {

    private $location;
    private $data = [];

    private $root = '';
    private $files = [];
    private $into = '';
    private $updated = true;
    private $type = '';

    /**
     * JoMiModule constructor
     * @param array $data
     * @param string $location
     * @throws \Exception
     */
    public function __construct($data,$location){
        $this->location = $location;
        $this->data = $data;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function loadModule(){
        if(!file_exists($this->location)) throw new \Exception('The module config file doesn\'t exist on "'.$this->location.'"');
        $contents = file_get_contents($this->location);
        if($contents===false) throw new \Exception('Error Reading Module File on "'.$this->location.'"!');
        $data = json_decode($contents,true);
        if($data===false) throw new \Exception('Error Reading Module File on "'.$this->location.'"!');
        return $data;
    }

    /**
     * @throws \Exception
     */
    protected function setData(){
        $prev_root = $this->root;
        if(isset($this->data['root'])) $this->root = $this->data['root'];
        $this->root = str_replace(['<dir>','<root>','//'],[__dir__.'/../../../..',$prev_root,'/'],$this->root);

        if(!isset($this->data['into'])||$this->data['into']==false) throw new \Exception('No [into] File set on Module File on "'.$this->location.'"!');
        $this->into = $this->root.$this->data['into'];

        if(!isset($this->data['files'])||!is_array($this->data['files'])||empty($this->data['files']) )
            throw new \Exception('No [files] set on Module File on "'.$this->location.'"!');
        $this->files = []; $uptime = '';
        foreach($this->data['files'] as $file){
            $this->files[] = $path = $this->root.$file;
            $time = filemtime($this->into);
            if($uptime===''||$time > $uptime) $uptime = $time;
        }

        if(isset($this->data['updated'])&&$this->data['updated']!=false) $this->updated = $this->data['updated'] < $uptime;

        if(isset($this->data['type'])&&
            (($type=$this->data['type']==='css') ($type==='js'))) $this->type = $type;
        else $this->type = strtolower(pathinfo($this->into,PATHINFO_EXTENSION));
    }

    /** @return bool */
    public function updated(){ return $this->updated; }

    /** @return string */
    public function getInto(){ return $this->into; }

    /** @return array */
    public function getFiles(){ return $this->files; }

    /**
     * @return array
     */
    public function run(){
        if($this->type==='js') $min = new Minify\JS();
        else $min = new Minify\CSS();
        $min->add($this->files)->minify($this->into);
        $this->data['updated'] = time();
        $this->updated = false;
        return $this->data;
    }

}

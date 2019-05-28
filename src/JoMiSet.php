<?php

namespace JoMi;

use MatthiasMullie\Minify;

class JoMiSet {

    private $location;
    private $data = [];
    private $vars = [];

    /**
     * JoMiModule constructor
     * @param array $data
     * @param string $location
     * @param array $vars
     * @throws \Exception
     */
    public function __construct(array $data,string $location,array $vars){
        $this->location = $location;
        $this->data = $data;
        $this->vars = $vars;
    }

    /** @return array */
    public function getData(){ return $this->data; }

    /**
     * @return bool
     * @throws \Exception
     */
    public function run(){
        $vars = $this->vars;
        if(isset($this->data['var'])){
            foreach($this->data['var'] as $var=>$val){
                $vars[$var] = $this->insertParameters($val,$vars);
            }
        }

        $files = $this->data['files']??[]; $utime = 0;
        if(empty($files)) throw new \Exception('No [files] set on Module File on "'.$this->location.'"!');
        foreach($files as $k=>$file){
            $files[$k] = $this->insertParameters($file,$vars);
            if(($mtime=filemtime($files[$k]))>$utime) $utime = $mtime;
        }
        $update = $utime > ($this->data['updated']??0);

        if(!isset($this->data['into'])) throw new \Exception('No [into] File set on Module File on "'.$this->location.'"!');
        $into = $this->insertParameters($this->data['into'],$vars);

        if($update){
            $type = $this->data['type']??strtolower(pathinfo($into,PATHINFO_EXTENSION));
            if($type==='css') $min = new Minify\CSS();
            elseif($type==='js') $min = new Minify\JS();
            else throw new \Exception('File extension "'.$type.'" not supported!');
            $min->add($files)->minify($into);
            $this->data['updated'] = time();
            return true;
        }
        return false;
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    protected function insertParameters(string $path,array $params){
        $path = preg_replace_callback('/{(.*?)}/',function($match) use ($params){
            return $params[$match[1]]??'';
        },$path);
        return str_replace('//','/',$path);
    }

}

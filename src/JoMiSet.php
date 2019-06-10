<?php

namespace JoMi;

use MatthiasMullie\Minify;

class JoMiSet {

    private $files;
    private $into;
    private $type;

    private $vars = [];
    private $upTime = 0;

    /**
     * JoMiModule constructor
     * @param array $files
     * @param string $into
     * @param string $type
     * @param array $vars
     * @throws \Exception
     */
    public function __construct($files,$into,$type,$vars=[]){
        $this->type = $type;
        $this->vars = $vars;
        $this->load($files,$into);
    }

    /**
     * @param array $files
     * @param string $into
     * @throws \Exception
     */
    protected function load($files,$into){
        if(empty($files)) throw new \Exception('Files to Minimize should not be empty.');
        if(trim($into)==='') throw new \Exception('Into File should not be empty.');

        foreach($files as $k=>$file){
            $this->files[$k] = $this->insertParameters($file,$this->vars);
            if(($mtime=filemtime($files[$k]))>$this->upTime) $this->upTime = $mtime;
        }
        $this->into = $this->insertParameters($into,$this->vars);
    }

    /**
     * @param int $updated
     * @return bool
     */
    public function updated($updated){
        return $this->upTime > ($updated??0);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function run(){
        $this->type = $this->type??strtolower(pathinfo($this->into,PATHINFO_EXTENSION));
        if($this->type==='css') $min = new Minify\CSS();
        elseif($this->type==='js') $min = new Minify\JS();
        else throw new \Exception('File extension "'.$this->type.'" not supported!');
        $min->add($this->files)->minify($this->into);
        return true;
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

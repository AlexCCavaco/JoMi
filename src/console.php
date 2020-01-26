<?php

namespace JoMi;

set_time_limit(0);
require __dir__.'/../../../../vendor/autoload.php';

function pe($message = '', $newline = true){ echo "\e[0;31m" . date('Y-m-d H:i:s') . ' /o\ ' . $message . "\e[0m" . ($newline ? "\n" : ''); };
function pw($message = '', $newline = true){ echo "\e[1;33m" . date('Y-m-d H:i:s') . ' ~o~ ' . $message . "\e[0m" . ($newline ? "\n" : ''); };
function pi($message = '', $newline = true){ echo "\e[0;34m" . date('Y-m-d H:i:s') . ' >o< ' . $message . "\e[0m" . ($newline ? "\n" : ''); };
function ps($message = '', $newline = true){ echo "\e[0;34m" . date('Y-m-d H:i:s') . ' *o* ' . $message . "\e[0m" . ($newline ? "\n" : ''); };
function pb($message = '', $newline = true){ echo "\em" . date('Y-m-d H:i:s') . ' -o- ' . $message . "\e[0m" . ($newline ? "\n" : ''); };

$config = (JoMi::$module_location).'jomi-mods.json';
$file_b = (JoMi::$file_base_path);

pi("Welcome to JoMi!");
pi("- - - - - - - - - - - - - - -");

if (!file_exists($config)||($data = trim(file_get_contents($config)))==="") {
    pw('JoMi config File not found or empty');
    $data = [];
} else {
    $data = json_decode($data, true);
}

do {
    echo "\n";
    pb("\e[5m> ", false);
    $in = readline();
    $options = explode(' ', $in);
    $count = sizeof($options) - 1;
    $cmd = $options[0];

    if ($cmd==='run') {
        if($count===0){
            pe('Command Malformed (type "help run")');
            continue;
        }
        update($options[1]);
    } elseif ($cmd==='list') {
        if(count($data)===0) pb('No modules found');
        foreach($data as $name=>$update){
            pb($name.' - updated: '.gmdate('Y-m-d H:i:s',$update));
        }
    } elseif ($cmd==='update') {
        $up = true;
        $option = 'all'; $time = 10;
        if($count>0)
            if($count>1&&$options[1]==='-t') $time = $options[2];
            else {
                $option = $options[1];
                if($count>2&&$options[2]==='-t') $time = $options[3];
            }
        if(count($data)===0){
            pb('No modules found');
            continue;
        }
        while($up===true){
            if($option==='all') $up=updateAll($data);
            else $up=update($option);
            sleep($time);
        }
        break;
    } elseif($in==='exit'){
        break;
    } else {
        pw("Command $cmd not found!");
    }

} while (true);
pb('Goodbye!');

function updateAll($data){
    pb('Updating all Modules');
    foreach($data as $name=>$update){
        if(!update($name)) return false;
    }
    return true;
}

function update($name){
    pb('Updating '.$name);
    try {
        if(JoMi::runModule($name)){
            ps("Module $name updated");
        } else {
            pb('Module skipped');
        }
    } catch(\Exception $e){
        pe($e->getMessage());
        return false;
    }
    return true;
}

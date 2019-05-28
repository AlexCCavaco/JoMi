# JoMi
Minifier for PHP

JoMi uses .json files (called modules) to configure file manipulations.
Manipulations happen when any of the source files is changed, otherwhise no processing occurs.

## Installation

```
composer require alexccavaco/jomi
```

## Running JoMi

The basic/easy way of running JoMi is as follows:

```
JoMi::runModule($name, $config);
```

JoMi::runModule() takes two arguments. The first represents the name of the module and the second is a configuration array.

Configurations:
- "module-location" - Represents the folder where modules are stored
- "file-base-path" - Represents the base folder of the files

## Module Json Example File:

{varname} - Represents a variable named "varname".
These can be assigned via "var" keys as shown in the example bellow.

```
{
  "var": {
    "public": "{root}/public",
    "var": "{root}/var"
  },
  "set": [
    {
      "files":[
        "{var}/test1.css",
        "{var}/test2.css"
      ],
      "into": "{public}/test.min.css",
      "type": "css",
      "updated": 1558896289
    },
    {
      "var":{
        "public": "{root}/public/"
      },
      "files":[
        "{var}/test1.js",
        "{var}/test2.js",
        "{var}/test3.js"
      ],
      "into": "{public}/test.min.js",
      "type": "js",
      "updated": 1558896289
    }
  ]
}
```

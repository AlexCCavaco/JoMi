# JoMi
Minifier for PHP

JoMi Joins and Minifies one or multiple files into one.
It can use .json files (called modules) to configure file manipulations or via the class itself.
Manipulations happen when any of the source files are changed, otherwise, no processing occurs.

## Installation

```
composer require alexccavaco/jomi
```

## Running JoMi moduled in Class

```
$mod = JoMi::runUsing($name, $config, $vars);
$mod->add($arrayOfFiles, $pathToDestination, $type);
$mod->run();
```

JoMi::runUsing() takes three arguments. The first represents the name of the module and is required, the second is a configuration array and the third an array o variables (to be used to ease file paths). The last two are optional.

Configurations:
- "module-location" - Represents the folder where modules are stored
- "file-base-path" - Represents the base folder of the files

The add method creates a minified instance of $arrayOfFiles pointing to $pathToDestination. The latter containing both the joined and the minified data (after running). The third and last argument establishes the type of files being handled, currently css or js files only. This argument is optional, and if it's not set, JoMi will look into the $pathToDestination file type (.css or .js) to make the decision.

The add method can be chained as exemplified below:

```
JoMi::runUsing($name, $config, $vars)->add(...)->add(...)->run();
```

## Running JoMi using Module Files

```
JoMi::runModule($name, $config, $vars);
```

JoMi::runModule() takes three arguments. The first represents the name of the module and is required, the second is a configuration array and the third an array o variables (to be used to ease file paths). The last two are optional.

Configurations are as above.

### Module Json Example File:

{varname} - Represents a variable named "varname".
These can be assigned via "var" keys as shown in the example below.

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

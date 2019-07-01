<p align="center">
<img src='https://github.com/zipavlin/fibers-helper/blob/master/docs/.vuepress/public/fibers-helper-logo.png?raw=true' style='display:block;margin-left:auto;margin-right:auto;'>
</p>

# Fibers Helper

Fibers Helper is a developer oriented tool - a collection of assorted helpers that are used in other parts of Fibers, primarily for collection of details and modification of different Laravel part.

## Introduction

Fibers Helper is an assorted collection of Laravel helpers that will help you collect additional information about specific parts of your application or even modify it in some cases.  

This helpers are primarily used internally by other Fibers packages. 

## Quick Start
```
# install composer package
composer require-dev fibers/helper

# import facades
use Fibers/Helper/Facades/ModelHelper;

# use helper
$modelInfo = ModelHelper::fromClass('App\Models\Place')->collect('attributes', 'relationships');
```

## Documentation
Please refer to [https://zipavlin.github.io/fibers-helper/](https://zipavlin.github.io/fibers-helper/) for more in dept installation & usage documentation and helpers' API reference.

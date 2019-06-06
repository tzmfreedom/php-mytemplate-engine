# My PHP Template Engine

PHP Template Engine

## Install

```bash
$ composer config repositories.tzmfreedom/php-mytemplate-engine vcs https://github.com/tzmfreedom/php-mytemplate-engine
$ composer require tzmfreedom/php-mytemplate-engine:dev-master
```

## Usage

```php
<?php

require_once 'vendor/autoload.php';

$engine = new \MyTemplate\Engine();
echo $engine->render("sample.my", ['xxx' => 'yyy']);
```

MyTemplate\Engine cache compiled template by default.
If you don't want to cache, you can specify NULL to constructor argument.
```php
new \MyTemplate\Engine(null);
```

The cache directory is `cache` by default.
If you change cache directory, you can specify string directory path to constructor argument.
```php
new \MyTemplate\Engine('/tmp');
```

## Template Syntax

output variable
```
{{ foo }}
```

if statement
```
{{ if xxx }}
<div>hello</div>
{{ else }}
<div>world</div>
{{ end }}
```

for statement
```
{{ for var : vars }}
<li>{{ var }}</li>
{{ end }}
```

include other template
```
{{ include 'other.my' }}
```

lookup getter property
```
{{ foo.bar }}
```

This is compiled to
```php
$foo->getBar();
```
So foo variable should be implemented public `getBar()` method.

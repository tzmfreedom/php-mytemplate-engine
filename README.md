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

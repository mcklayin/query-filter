# QueryFilter

Easily create filters for your Eloquent model.

Based on [Jeffray Way's Laracast tutorial](https://github.com/laracasts/Dedicated-Query-String-Filtering/).

## Installation

The library is currently not available on Composer, so you have to declare it manually in your `composer.json`.

To do this, add the following in your `composer.json` :

```json
{
    "require" : {
        "mcklayin/query-filter": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mcklayin/query-filter"
        }
    ]
}
```

Or type in console:
composer require "mcklayin/query-filter @dev"

## Usage

- Create your model filters, for exemple in a `App\Http\Filters` namespace :

```php
<?php

namespace App\Http\Filters;

use McklayiN\QueryFilter\QueryFilter;

class MyModelFilter extends QueryFilter
{
    public function foo($value)
    {
        return $this->builder->where('foo', 'bar');
        //or
        //return $this->where('foo', 'bar');
    }
}
```

- Then, add the `FilterableTrait` on your model to allow the use of `MyModel::filter()` :

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use McklayiN\QueryFilter\Traits\FilterableTrait;

class MyClass extends Model
{
    use FilterableTrait;
}
```

- Finally, you can use the `MyModel::filter()` method in your controller :

```php
<?php

namespace App\Http\Controllers;

use App\Http\Filters\MyModelFilter;
use App\MyModel;

class MyController extends Controller
{
    public function index(MyModelFilter $filter)
    {
        $data = MyModel::filter($filter)->get();

        return response()->json(compact('data'));
    }
}
```

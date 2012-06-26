li3_paginate
============

li3_pagination was designed to facilitate generating of pagination for Lithium Framework projects. So far helper can
generate a pagination from a record set for the current context. You cannot provide custom arguments for the route
generation. Helper cannot extract the query from the provided record set either. It is planed to extend the behavior
of the helper to support custom route parameters as well as arbitrary record counts.

Installation
------------

Clone this repo into the `libraries` folder of your project. Before you can use this helper, you need to tell lithium
to load this extension. The best way to do it is to add it as a library in the bootstrap process:

``` php
<?php

// /app/config/bootstrap/libraries.php

// ...
Libraries::add('li3_pagination', array('bootstrap' => false));
// ...
```

You also need to inform Lithium how to map the page param from the request. You can use the example route below or change
it according to your needs:

``` php
<?php

// /app/config/routes.php

// ...
Router::connect('/{:controller}/{:action}/page/{:page:[0-9]+}');
// ...
```

That's all. You are set and ready to use the li3_pagination helper.

Usage
-----

li3_pagination was designed to have a full control over pagination with a minimal setup. In a nutshell you just need to
retrieve your results in the controller and pass them to the helper:


``` php
<?php

// IndexController.php

// ...
public function index() {
    $limit = 10;
    $page = $this->request->page ?: 1;
    $options = compact('limit', 'page');
    $binding = Posts::find('all', $options);
    return compact('binding', 'options');
}
// ...
?>
```

``` php
<?php
$this->pagination->create($binding, $options);
$this->pagination->start();
$this->pagination->first();
$this->pagination->previous();
$this->pagination->pages();
$this->pagination->next();
$this->pagination->last();
$this->pagination->end();
?>
```

li3_pagination does not have a constructor and is really similar in the usage to the form helper provided with the Lithium
Framework. Create method takes a binding as the first argument and an array of options as the second argument. You can define
any class as the binding as long as it implements the method `model()`. The value returned by `model()` method has to be
a string or an object you can use to invoke the method `count()`. Basically the `\lithium\data\collection\RecordSet` is
the perfect candidate as the first argument. Second argument is an array of options. So far li3_paginate supports following
options

``` php
$options = array(
    'page' => 1,
    'limit' => 20,
    'start' => true,
);
```

TODO Document the options for proxies

TODO
----
* Complete this documentation and expose all the options and filters supported by this helper.
* Add support for arbitrary result counts and make the binding optional.
* Add support for arbitrary route parameters.
* Introduce filters for all relevant methods.
* Complete the code documentation.
* Refactor the code to meet the lithium coding standards.
* Refactor the default settings and configuration.
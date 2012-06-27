li3_paginate
============

li3_pagination was designed to facilitate generating of pagination for Lithium Framework projects. So far helper can
generate a pagination from a record set for the current context or from an arbitrary record count. You cannot provide
custom arguments for the route generation. Helper cannot extract the query from the provided record set either. It is
planed to extend the behavior of the helper to support custom route parameters.

Installation
------------

Clone this repo into the `libraries` folder of your project.

	cd /path/to/app/libraries
	git clone https://github.com/theDisco/li3_pagination.git

Before you can use this helper, you need to tell lithium
to load this extension. The best way to do it is to add it as a library in the bootstrap process:

``` php
<?php

// /path/to/app/config/bootstrap/libraries.php

// ...
Libraries::add('li3_pagination', array('bootstrap' => false));
// ...
```

You also need to inform Lithium how to map the page param from the request. You can use the example route below or change
it according to your needs:

``` php
<?php

// /path/to/app/config/routes.php

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
echo $this->pagination->create($binding, $options);
echo $this->pagination->first();
echo $this->pagination->previous();
echo $this->pagination->pages();
echo $this->pagination->next();
echo $this->pagination->last();
echo $this->pagination->end();
?>
```

You can also use the compound methods for creating the first, previous, next and last page.

``` php
<?php
echo $this->pagination->create($binding, $options);
echo $this->pagination->pre();
echo $this->pagination->pages();
echo $this->pagination->post();
echo $this->pagination->end();
?>
```

li3_pagination does not have a constructor and is really similar in the usage to the form helper provided with the Lithium
Framework. Create method takes a binding as the first argument and an array of options as the second argument. You can define
any class as the binding as long as it implements the method `model()`. The value returned by `model()` method has to be
a string or an object you can use to invoke the method `count()`. Basically the `\lithium\data\collection\RecordSet` is
the perfect candidate as the first argument. Second argument is an array of options.

If you have defined any conditions for your model, li3_pagination won't be able to retrieve the correct count. In this case
you can provide the records count as the first argument of `create()` method.

``` php
<?php
echo $this->pagination->create('150', $options);
?>
```

Configuration and Options
-------------------------

``` php
$options = array(
    'page' => 1,
    'limit' => 20,
    'start' => true,
);
```

TODO Document the options

TODO
----
* Document the options and filters supported by this helper.
* Add support for arbitrary route parameters.
* Introduce filters for all relevant methods.
* Complete the code documentation.
* Refactor the default settings and configuration.
* Finish tests
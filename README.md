Seagull
=======

First, this is called Seagull, a very far fetched reference to a seagull intersection.

### Settings on intersections

This is a tiny library to help you out with your settings. The way it works is best explained with an example.
Start by adding settings to a new object. This can be done in several ways.

```php
// instantiate Seagull and pass a config to the constructor
$values = array(
    'db' => array(
        'default' => array(
            'host' => 'localhost',
            'user' => 'root',
            ...
        )
    ),
    ...
);

$config = new Seagull($values);

// values can also be set, or added, using the setter:
$config->set('db.default.user', 'root');

// or, take a route to halfway, and the rest with an array:
$config->set('db.default', array(
    'host' => 'localhost',
    'user' => 'root'
));

// your configuration can be accessed like this:
$db_user = $config->get('db.default.user');
$default_db_settings = $config->get('db.default');

```
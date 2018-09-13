### Wordpress Plugin Rest APIs

Contains two rest API's:

1. Events Manager
2. OIO Ad Manager

Built using the [Slim Framework](https://www.slimframework.com/) and [NotORM](http://www.notorm.com/).

### Features

Provides REST endpoints to interact with wordpress plugins via JSON.

This API does not allow the creation/submission of events.

### Creating Your Own Plugins

You can create your own plugins and add them to the plugins/ folder. The only requirement is a 'routes.php' file. This will determine the accessible API URLs/endpoints.

### Configuration

Rename or copy `app/config.template.php` to `app/config.php`. Alter the database configuration values to match your Wordpress installation.

```php
$config['db']['host']   = "localhost";
$config['db']['user']   = "username";
$config['db']['pass']   = "password";
$config['db']['dbname'] = "wordpress_database";
$config['db']['charset'] = "utf8";
```

Also, set the JWT authentication secret.

```php
// JWT Authentication Secret
$config['jwt']['secret'] = "setThisToSomethingSuperSecret";
```

It is highly recommended that the database user be given **READ ONLY** access to the Wordpress database.

### NotORM

[NotORM](http://www.notorm.com/) is used to interact with the database. An ORM would be overkill for this API. NotORM provides speed and a simple query syntax that makes it perfect for an API like this.

### License

MIT

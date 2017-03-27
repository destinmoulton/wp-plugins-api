### Events Manager Rest API
A REST API to access Events Manager data.

### Features
Provides REST endpoints to perform event queries. Data is presented as JSON.

This API does not allow the creation/submission of events.

### Configuration
Rename or copy `app/config.template.php` to `app/config.php`. Alter the database configuration values to match your Wordpress installation.

It is highly recommended that the database user be given **READ ONLY** access to the Wordpress database.

### NotORM
[NotORM](http://www.notorm.com/) is used to interact with the database. An ORM would be overkill for this API. NotORM provides speed and a simple query syntax that makes it perfect for an API like this.

### License
This project is released under the MIT license. 
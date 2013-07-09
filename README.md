# spire

spire is a REST interface for mongodb instances.

![spire](http://i.imgur.com/wLO0OxJ.png)


## installation

clone the repository.
```sh
 $ git clone https://github.com/emre/spire.git
```
install the dependencies.
```sh
 $ composer install
```
that's all. next is step is <a href="http://silex.sensiolabs.org/doc/web_servers.html">configuring your webserver</a>.

## getting started

you can test your api from your command line. 

**getting a list of items**
``` sh
curl --dump-header -X get 'http://localhost:8090/users/'
```
**adding an item**
``` sh
curl --dump-header - -H "Content-Type: application/json" -X POST --data '{"first_name":"John", "last_name":"Doe", "location": "Miami, FL"}' http://localhost/users/
```
**updating an item**
``` sh
curl --dump-header - -H "Content-Type: application/json" -X PUT --data '{"first_name":"Jane", "last_name": "Brown"}' http://localhost/users/:USER_ID:/
```

## bundles
you can represent entries in your way. add your custom Resource class to custom_resource.php like this:

``` php
class CustomResource extends \Spire\Resources\Resource {

    public function build_users_bundle($data) {
        $data["fullname"] = sprintf("%s %s", $data["first_name"], $data["last_name"]);
        return $data;
    }


}

```
point it out in your settings.py:
``` php
$RESOURCE_CLASS = 'CustomResource';
```


## supported methods

| Method        | Path          |           Action              |
| ------------- |---------------| ------------------------------|
| GET           | /users        | Returns all records. **offset** and **limit** parameters supported for pagination     |
| GET           | /users/:id    | Returns a single document     |
| POST          | /users        | Creates a new document        |
| PUT           | /users/:id    | Replaces an existing document |
| DELETE        | /users/:id    | Removes an existing document  |



## running tests

```sh
 $ vendor/bin/phpunit tests/SpireTest.php
```

## note
 - This a _remembering PHP project_ for me since I did not write a single line of PHP for like 3 years. Any contributions to make it better will be accepted.

 - see <a href="http://github.com/fatiherikli">@fatiherikli</a>'s <a href="http://github.com/fatiherikli/kule">kule</a> for a python/better alternative.


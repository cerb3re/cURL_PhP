##simple single-user Slim todo app

example definition of REST webservice to create/read/update/delete/ tasks


 - use [Slim  php Framework](https://www.slimframework.com/) as php Framework

 - added *CORS* header to make it available everywhere.



#install

Prepare your database, see `resources/todo-app.sql` and tune your `src/settings.php`

Download dependencies ( need `composer` )

```shell
composer install
```

#run

`composer start`


##available API

 - get all todos:

 ```
 curl http://localhost:8080/api/v1/todos
 ```

 - get todo with id=6:

 ```
 curl http://localhost:8080/api/v1/todos/6
 ```

 - update todo with id=6:

 ```
 curl -XPUT -d '{"task":"my task", "priority":1}' http://localhost:8080/api/v1/todos/6
 ```

 - delete todo with id=6:

 ```
 curl -XDELETE  http://localhost:8080/api/v1/todos/6
 ```

 - create todo:

 ```
 curl -XPOST -d '{"task":"my task", "priority":1}' http://localhost:8080/api/v1/todos
 ```


Sample mini-js app available at `http://localhost:8080/todos_object.html`

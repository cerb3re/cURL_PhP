<?php
// Routes

$app->group('/api/v1', function () use ($app) {
  // get all todos
  $app->get('/todos',function ($request, $response, $args) {
       $sth = $this->db->prepare("SELECT * FROM task ORDER BY task");
      $sth->execute();
      $todos = $sth->fetchAll();
      return $this->response->withJson($todos);
  });

  $app->get('/todos/[{id}]', function ($request, $response, $args) {
         $sth = $this->db->prepare("SELECT * FROM task WHERE id=:id");
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $todos = $sth->fetchObject();
        return $this->response->withJson($todos);
    });


    $app->get('/todos/search/[{query}]', function ($request, $response, $args) {
           $sth = $this->db->prepare("SELECT * FROM task WHERE task LIKE :query ORDER BY task");
          $query = "%".$args['query']."%";
          $sth->bindParam("query", $query);
          $sth->execute();
          $todos = $sth->fetchAll();
          return $this->response->withJson($todos);
      });

      // Add a new todo
    $app->post('/todos', function ($request, $response) {
        $todo = json_decode($request->getBody());
        $sql = "INSERT INTO task (task, priority) VALUES (:task, :priority)";
        $sth = $this->db->prepare($sql);
        $sth->bindParam("task", $todo->task);
        $sth->bindParam("priority", $todo->priority);
        $sth->execute();
        $todo->id=$this->db->lastInsertId();
        //$input['id'] = $this->db->lastInsertId();
        //$this->response->withStatus(201);
        return $this->response->withJson($todo)->withStatus(201);
    });


    // DELETE a todo with given id
    $app->delete('/todos/[{id}]', function ($request, $response, $args) {
         $sth = $this->db->prepare("DELETE FROM task WHERE id=:id");
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        return $this->response->withStatus(204); //no-content
    });


// Update todo with given id
    $app->put('/todos/[{id}]', function ($request, $response, $args) {
        //$input = $request->getParsedBody();
        //$this->logger->debug($request->getBody());
        $todo = json_decode($request->getBody());
        $sql = "UPDATE task SET task=:task,priority=:priority  WHERE id=:id";
         $sth = $this->db->prepare($sql);
        $sth->bindParam("id", $args['id']);
        $sth->bindParam("task", $todo->task);
        $sth->bindParam("priority", $todo->priority);
        $sth->execute();

        //load complete oci_fetch_object
       $sth = $this->db->prepare("SELECT * FROM task WHERE id=:id");
       $sth->bindParam("id", $args['id']);
       $sth->execute();
       $todo = $sth->fetchObject();

        //$input['id'] = $args['id'];
        return $this->response->withJson($todo);
    });





});

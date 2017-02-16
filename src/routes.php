<?php
// Routes

//ROUTE DE L'APPLI ANDROID
$app->group('/appli', function () use ($app)
{
  $app->get('/listing',function ($request, $response, $args) {
       $sth = $this->db->prepare("
        SELECT 
          name, surname, phone, email, departure, arrived, nbplace, dateD, dateA, dayPost
        FROM 
          destination 
        INNER JOIN 
          user ON destination.userId = user.id 
        INNER JOIN 
          date ON destination.dateId = date.id
        ");
      $sth->execute();
      $todos = $sth->fetchAll();
      $array = array("data" => $todos);
      return $this->response->withJson($array);    
  });
  
  $app->get('/user',function ($request, $response, $args) {
       $sth = $this->db->prepare("
        SELECT 
           name, id
        FROM 
          user 
        ");
      $sth->execute();
      $todos = $sth->fetchAll();
      $array = array("data" => $todos);
      return $this->response->withJson($array);    
  });

  $app->get('/listing/complet', function($request, $response, $args)
  {
    $res = $this->db->prepare("
      SELECT
        *
      FROM
        destination
      INNER JOIN
        user ON destination.userId = user.id
      INNER JOIN
        date ON destination.dateId = date.id
    ");
    $res->execute();
    $complet  = $res->fetchAll();
    //$array    = array("data" => $complet);
    $jsonEncode = json_encode($complet);
    return ($jsonEncode);
  });
  // DEBUG
  $app->get('/listing/debug', function($request, $response, $args)
  {
    $res = $this->db->prepare("
      SELECT
        *
      FROM
        destination
      INNER JOIN
        user ON destination.userId = user.id
      INNER JOIN
        date ON destination.dateId = date.id
    ");
    $res->execute();
    $complet  = $res->fetchAll();
    //$array    = array("data" => $complet);
    $jsonEncode = json_encode($complet);
    $jsonDecode = json_decode($jsonEncode, true);

    print $jsonEncode;
    print "<br /><br />";
    $int = 0;
    $count = 0;
    foreach ($jsonDecode as $item) 
    {
      print 'id: '        . $item["id"] . '<br />';
      print 'userId: '    . $item["userId"] . '<br />';
      print 'departure: ' . $item["departure"] . '<br />';
      print 'arrived: '   . $item["arrived"] . '<br />';
      print 'nbPlace: '   . $item["nbPlace"] . '<br />';
      print 'dateId: '    . $item["dateId"] . '<br />';
      print 'name: '      . $item["name"] . '<br />';
      print 'surname: '   . $item["surname"] . '<br />';
      print 'phone: '     . $item["phone"] . '<br />';
      print 'email: '     . $item["email"] . '<br />';
      print 'dateD: '     . $item["dateD"] . '<br />';
      print 'dateA: '     . $item["dateA"] . '<br />';
      print 'dayPost: '   . $item["dayPost"] . '<br />';  
      print "<br />";
      $count += 1;
      $int = sizeof($item);
    }
    print "<br />Il y a " . $int . " éléments à traiter par groupe" ;
    print "<br />Il y a " . $count . " groupes" ;
    print "<br /> Pour une total de " . $int * $count . " éléments" ;
  });
  // AJOUTER DES DONNEES DEPUIS L'APPLI ANDROID
  $app->post('/post', function($request, $response)
  {
        $submit = json_decode($request->getBody());
        $sql = "INSERT INTO destination (`userId`, `departure`, `arrived`, `nbPlace`, `dateId`) VALUES (:userId, :departure, :arrived, :nbPlace, :dateId)";
        //$sql = "INSERT INTO `destination` (`userId`, `departure`, `arrived`, `nbPlace`, `dateId`) VALUES ('2', 'toto', 'titi', '4', '6')";
        
        $sth = $this->db->prepare($sql);
        $sth->bindParam("userId",       $submit->userId);
        $sth->bindParam("departure",    $submit->departure);
        $sth->bindParam("arrived",      $submit->arrived);
        $sth->bindParam("nbPlace",      $submit->nbPlace);
        $sth->bindParam("dateId",       $submit->dateId);
        $sth->execute();
        $submit->id=$this->db->lastInsertId();
         
        
        //$input['id'] = $this->db->lastInsertId();
        //$this->response->withStatus(201);
        return $this->response->withJson($submit)->withStatus(201);
  });
  // METTRE A JOUR DES DONNEES DEPUIS L'APPLI ANDROID
      $app->put('/update', function ($request, $response, $args) {
        //$input = $request->getParsedBody();
        //$this->logger->debug($request->getBody());
        $todo = json_decode($request->getBody());
        //$sql = "UPDATE destination SET task=:task,priority=:priority  WHERE id=:id";
        $sql = "UPDATE destination SET (`userId`, `departure`, `arrived`, `nbPlace`, `dateId`) WHERE id = :id";
         $sth = $this->db->prepare($sql);
        $sth->bindParam("id", $args['id']);
        $sth->bindParam("userId", $todo->userId);
        $sth->bindParam("departure", $todo->departure);
        $sth->bindParam("arrived", $todo->arrived); 
        $sth->bindParam("nbPlace", $todo->nbPlace);
        if ($sth->bindParam("dateId", $todo->dateId) == null)
        {
            
        }
        else
        {
            $sth->bindParam("dateId", $todo->dateId);
        }
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
// ROUTE DEFAULT

$app->group('/api/v1', function () use ($app) {
  // get all todos
  $app->get('/todos',function ($request, $response, $args) {
       $sth = $this->db->prepare("SELECT * FROM destination");
      $sth->execute();
      $todos = $sth->fetchAll();
      $array = array("data" => $todos);
      return $this->response->withJson($array);
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
        $sql = "INSERT INTO destination (task, priority) VALUES (:task, :priority)";
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

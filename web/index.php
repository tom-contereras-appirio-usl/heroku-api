<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;


$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
               array(
                'pdo.server' => array(
                   'driver'   => 'pgsql',
                   'user' => $dbopts["user"],
                   'password' => $dbopts["pass"],
                   'host' => $dbopts["host"],
                   'port' => $dbopts["port"],
                   'dbname' => ltrim($dbopts["path"],'/')
                   )
               )
);




// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));


$app->post('/create-member/', function() use($app) {

$request = new HttpRequest();
$request->seUrl('https://usl-salesforce-webapp.heroku.com/create-member/');
$request->setMethod(HTTP_METH_POST);


/*
  $postgres_id = '56787';
  $firstname = 'England';
  $lastname = 'Contreras';

  $data = [
      'postgres_id' => $postgres_id,
      'firstname' => $firstname,
      'lastname' => $lastname,
  ];

  return json_encode($data);

  //$stmt = $app['pdo']->prepare("INSERT INTO salesforcedev (firstname, lastname) VALUES ('England', 'Contreras')");
  //$sql = "INSERT INTO salesforcedev.contact (Id, Postgrest_Id__c, FirstName, LastName) VALUES (:postgres_id, :firstname, :lastname)";
  $sql = 'INSERT INTO salesforcedev.contact (Postgrest_Id__c, FirstName, LastName) VALUES ('12345', 'England', 'Contreras')';
  $stmt = $app['pdo']->prepare($sql);
  $stmt->execute($data);

  return json_encode($data);

*/

});





$app->get('/retrieve-member/', function() use($app) {
  $result = $app['pdo']->prepare('SELECT id, firstname, lastname FROM salesforcedev.contact WHERE id=12');
  $result->execute();

  $names = array();

  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $names[] = $row;
  }

  return json_encode($names);
  //return $result;  This doesn't work

});



$app->run();

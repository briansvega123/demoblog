<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
		'db.options' => array(
				'driver' => 'pdo_mysql',
				'host'	 => '127.0.0.1',
				'dbname' => 'phptest',
				'user'	 => 'root',
				'password' => 'cookie',			
		),
));

$app->get('/', function() use ($app){	
	$posts = $app['db']->fetchAll('SELECT * FROM blog ORDER BY created LIMIT 10');
	$rpost = $app['db']->fetchAll('SELECT * FROM blog ORDER BY created LIMIT 5');
	$apost = $app['db']->fetchAll('SELECT count(*) as count, YEAR(created) as year, MONTH(created) as month FROM  blog WHERE created > DATE_SUB(now(), INTERVAL 12 MONTH) GROUP BY YEAR(created), MONTH(created)
');
	$archive = $app['db']->fetchAll('SELECT * FROM blog WHERE created > DATE_SUB(now(), INTERVAL 12 MONTH)');
	return $app['twig']->render('index.twig', array(
		'posts' => $posts,
		'rpost' => $rpost,
		'apost' => $apost,
		'archive' => $archive,
	));

});

$app->get('/post/{id}', function($id) use ($app) {
	$sql = "SELECT * FROM blog WHERE id = ?";
	$posts = $app['db']->fetchAssoc($sql, array((int) $id));
    return $app['twig']->render('post.twig', array(
		'posts' => $posts,
	));
});

$app->get('/archive/{year}/{month}', function($year, $month) use ($app) {
	$sql = "SELECT * FROM blog WHERE YEAR(created) = ? AND MONTH(created) = ?";
	$stmt = $app['db']->executeQuery($sql, array((int) $year, $month));
	$posts = $stmt->fetchAll();
	return $app['twig']->render('archive.twig', array(
		'postz' => $posts,
	));
	
});

$app->run();

?>
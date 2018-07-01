<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/js/keys.php';

// setup Slim

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;
$config['todoist']['key'] = $todoistApiKey;
$config['todoist-share']['ignore'] = [
    'shopping',
    'errands',
];

$app = new Slim\App(['settings' => $config]);

// set up DB and templating

$container = $app->getContainer();

$container['view'] = function ($container) {
    $templates = __DIR__ . '/templates/';
    $cache = __DIR__ . '/tmp/views/';
    $cache = false;
    $debug = false;
    // $debug = true;
    $view = new Slim\Views\Twig($templates, compact('cache', 'debug'));
    $view->getEnvironment()->addGlobal('_get', $_GET);

    if ($debug) {
        $view->addExtension(new \Slim\Views\TwigExtension(
            $container['router'],
            $container['request']->getUri()
        ));
        $view->addExtension(new \Twig_Extension_Debug());
    }
    return $view;
};

// routes

$app->get('/', function (Request $request, Response $response) {

    $projects = getTodoistData('projects', $this->get('settings')['todoist']['key']);
    $ignoreIDs = [];
    foreach ($projects as $project) {
        if (in_array(strtolower($project['name']), $ignoreNames)) {
            $ignoreIDs[] = $project['id'];
        }
    }

    $tasks = getTodoistData('tasks', $this->get('settings')['todoist']['key']);
    $days = [];
    foreach ($tasks as $i => $task) {
        if (!$task['completed'] &&
            !in_array($task['project_id'], $ignoreIDs) &&
            !empty($task['due'])) {
                $days[$task['due']['date']][] = $task;
        }
    }

});

$app->run();

// functions

function getTodoistData($uri, $key) {

    include __DIR__ . '/keys.php';
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL             => "https://beta.todoist.com/API/v8/$uri",
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_HTTPHEADER      => ["Authorization: Bearer $key"],
    ]);

    $result = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    if ($error) {
        return $error;
    } else {
        return json_decode($result, true); 
    }
}


?>
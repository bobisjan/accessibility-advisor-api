<?php

/**
 * Accessibility Advisor API
 *
 * Jan Bobisud (bobisjan@fel.cvut.cz)
 *
 * Department of Computer Graphics and Interaction
 * Faculty of Electrical Engineering
 * Czech Technical University in Prague
 */

use Nette\Application\Routers\Route,
    Nette\Application\Responses\JsonResponse,
    Nette\Diagnostics\Debugger,
    Nette\Http\IResponse;



// Load Nette Framework
require __DIR__ . '/data/libs/nette.min.php';

// Configure application
$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
$configurator->enableDebugger(__DIR__ . '/data/log', 'bobisjan@fel.cvut.cz');

// Create Dependency Injection container
$configurator->setTempDirectory(__DIR__ . '/data/temp');

// Create Robot Loader for class loading
$configurator->createRobotLoader()
    ->addDirectory(__DIR__ . '/data/model')
    ->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/data/config/config.neon');
$configurator->addConfig(__DIR__ . '/data/config/config.local.neon');
$container = $configurator->createContainer();

// Setup router using mod_rewrite detection
$container->router[] = new Route('[index.php]', function() {
    return 'Accessibility Advisor API';
});

$container->router[] = new Route('<model>[/<id>]', function($model, $id, $presenter) use ($configurator) {
    try {
        $store = $presenter->context->getService('store');
        $query = $presenter->request->getParameters();
        $data = NULL;

        unset($query['callback']);
        unset($query['model']);
        unset($query['id']);

        if ($id !== NULL) {
            $data = $store->{$model}->find($id);
        } elseif (isset($query['ids'])) {
            $data[$model] = $store->{$model}->findMany($query['ids']);
        } else {
            $data[$model] = $store->{$model}->findAll($query);
        }
        return new JsonResponse($data);

    } catch (Exception $e) {
        if (!$configurator->isDebugMode()) {
            Debugger::log($e);
        }
        return $presenter->error($e->getMessage(), IResponse::S500_INTERNAL_SERVER_ERROR);
    }
});

// Run the application!
$container->application->run();
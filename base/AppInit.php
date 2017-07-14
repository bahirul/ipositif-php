<?php
use FastRoute\Dispatcher;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;


/**
 * Autoload
 */
require(__DIR__ . '/../vendor/autoload.php');

/**
 * Request Instance
 *
 */

$request = Request::createFromGlobals();

/*
 * Config initialization
 */
if (file_exists(__DIR__ . '/../config/app.php') !== true) {
    Response::create('Missing config app.php !!!', Response::HTTP_INTERNAL_SERVER_ERROR)
        ->prepare($request)
        ->send();
    return;
}

/**
 * Load Config
 */
$config = require(__DIR__ . '/../config/app.php');

/*
 * Container setup
 */
$container = new Container();
$container
    ->add('Twig_Environment')
    ->withArgument(
        new Twig_Loader_Filesystem(__DIR__ . '/../views/')
    );
$container
    ->delegate(
        // Auto-wiring based on constructor typehints.
        // http://container.thephpleague.com/auto-wiring
        new ReflectionContainer()
    );

/*
 * Error handler
 */
$whoops = new Run;
if (APP_ENV === 'dev') {
    $whoops->pushHandler(
        new PrettyPageHandler()
    );
} else {
    $whoops->pushHandler(
        // Using the pretty error handler in production is likely a bad idea.
        // Instead respond with a generic error message.
        function () use ($request,$container) {
            $error_controller = $container->get('app\base\ErrorController');
            $response = $error_controller->index(500,'An internal server error occured');
            $response
                ->prepare($request)
                ->send();
        }
    );
}
$whoops->register();

/*
 * Routes
 */
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $routes = require __DIR__ . '/../config/routes.php';
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
});

/*
 * Dispatch
 */
$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

switch ($routeInfo[0]) {

    case Dispatcher::NOT_FOUND:
        $error_controller = $container->get('app\base\ErrorController');
        $response = $error_controller->index(404,'Page Not Found');
        if ($response instanceof Response) {
            $response
                ->prepare($request)
                ->send();
        }
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        $error_controller = $container->get('app\base\ErrorController');
        $response = $error_controller->index(405,'Method Not Allowed');
        if ($response instanceof Response) {
            $response
                ->prepare($request)
                ->send();
        }
        break;
    case Dispatcher::FOUND:
        // Fully qualified class name of the controller, short controller first
        $fqcn = class_exists('app\controllers\\' . $routeInfo[1][0] . 'Controller') ? 'app\controllers\\' . $routeInfo[1][0] . 'Controller' : $routeInfo[1][0];
        
        // Controller method responsible for handling the request
        $routeMethod = $routeInfo[1][1];

        // Route parameters (ex. /products/{category}/{id})
        $routeParams = $routeInfo[2];

        // Obtain an instance of route's controller
        // Resolves constructor dependencies using the container
        $controller = $container->get($fqcn);

        // Generate a response by invoking the appropriate route method in the controller
        $response = $controller->$routeMethod($routeParams);

        if ($response instanceof Response || $response instanceof BinaryFileResponse) {
            // Send the generated response back to the user
            $response
                ->prepare($request)
                ->send();
        }
        break;

    default:
        // According to the dispatch(..) method's documentation this shouldn't happen.
        // But it's here anyways just to cover all of our bases.
        Response::create('Received unexpected response from dispatcher.', Response::HTTP_INTERNAL_SERVER_ERROR)
        ->prepare($request)
        ->send();
    
    return;
}
<?php

/*
/Report all PHP errors, but use only in the development
*/
ini_set('display_errors', 1);
error_reporting(E_ALL);

$vendor = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($vendor)) {
    throw new RuntimeException('Install dependencies to run this script.');
}

require_once $vendor;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();
$request->setMethod('POST');

$response = new JsonResponse();

$response->headers->set('Content-Type', 'application/json');
$response->headers->set('Access-Control-Allow-Origin', '*');


switch ($request->getPathInfo()) {
    case '/':
        $response->setData(['message' => 'Task companies search']);
        break;

    case '/search':
        $response->setContent((new App\CompanyController($response, $request))->search());
        break;

    default:
        $response->setData(['message' => 'Not found!']);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
}

$response->send();
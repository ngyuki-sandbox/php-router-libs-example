<?php
namespace App;

require __DIR__ . '/../vendor/autoload.php';

use function FastRoute\simpleDispatcher;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;

$dispatcher = simpleDispatcher(function(RouteCollector $r) {

    $r->addRoute('GET', '/test', 'test GET');
    $r->addRoute('POST', '/test', 'test POST');

    // 複数のメソッド
    $r->addRoute(['GET', 'POST'], '/test2', 'test2');

    // 波括弧はパラメータ（スラッシュは含まない）
    //
    //      /username/abc
    //    ! /username/abc/xyz
    //
    $r->addRoute('GET', '/username/{name}', 'username');

    // コロンで正規表現
    //
    //      /user/123
    //    ! /user/abc
    //
    $r->addRoute('GET', '/user/{id:\d+}', 'user');


    // スラッシュをパラメータに含める
    //
    //      /uri/aaa/bbb/ccc
    //
    $r->addRoute('GET', '/uri/{name:.+}', 'uri');

    // 角括弧はオプショナル
    //
    //      /opt/123
    //      /opt/123/abc
    //
    $r->addRoute('GET', '/opt/{id:\d+}[/{name}]', 'opt');

    // オプショナルは最後のセグメントだけ
    //
    //      FastRoute\BadRouteException
    //
    //$r->addRoute('GET', '/opt2[/{id:\d+}]/{name}', 'opt2');

    // ショートハンド
    $r->get('/get', 'get_handler');
    $r->post('/post', 'post_handler');

    // グループ
    //
    //  /admin/ore
    //  /admin/are
    //
    $r->addGroup('/admin', function (RouteCollector $r) {
        $r->get('/ore', "admin ore");
        $r->get('/are', "admin are");
    });

    print_r($r->getData());
});

// Fetch method and URI from somewhere
$method = $argv[1] ?? 'GET';
$uri = $argv[2] ?? '/users';

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($method, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        var_dump('404 Not Found');
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        var_dump('405 Method Not Allowed ... allowedMethods ' . implode('/', $allowedMethods));
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $params = $routeInfo[2];
        var_dump($handler, $params);
        break;
}

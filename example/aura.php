<?php
namespace App;

require __DIR__ . '/../vendor/autoload.php';

use Aura\Router\Map;
use Aura\Router\RouterContainer;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

$routerContainer = new RouterContainer();

$map = $routerContainer->getMap();

(function(Map $map){
    $map->get('test', '/test', 'handler');

    // プレースホルダ
    $map->get('blog.read', '/blog/{id}', 'handler');

    // 正規表現で制限
    $map->post('blog.write', '/blog/{id}', 'handler')->tokens(['id' => '\d+']);

    // 複数の正規表現
    $map->get('ore', '/ore/{id}{format}')
        ->tokens([
            'id' => '\d+',
            'format' => '(\.[^/]+)?',
        ])
    ;

    // デフォルト値
    $map->get('are', '/are/{id}{format}')
        ->tokens([
            'id' => '\d+',
            'format' => '(\.[^/]+)?',
        ])
        ->defaults([
            'format' => '.html',
        ])
    ;

    // オプショナル？
    $map->get('date', '/date{/year,month,day}')
        ->tokens([
            'year' => '\d{4}',
            'month' => '\d{2}',
            'day' => '\d{2}',
        ]);

    $map->get('opt', '/opt{/val}')
        ->defaults([
            'val' => 123,
        ]);

    // ワイルドカード ... /wild/aaa/bbb/ccc
    $map->get('wild', '/wild')->wildcard('card');

    // 認証 ... ただの追加の属性？
    $map->get('auth', '/auth')->auth(['isAdmin' => true]);


    // Extra ... ↑と何が違う？
    $map->get('extras', '/extras')->extras(['hoge' => 999]);

    // グループ
    $map->attach('admin.', '/admin', function (Map $map) {

        $map->tokens([
            'id'     => '\d+',
            'format' => '(\.json|\.atom|\.html)?',
        ]);

        $map->defaults([
            'format' => '.html',
        ]);

        $map->get('home', '');
        $map->post('add', '');
        $map->get('read', '/{id}{format}');
        $map->post('post', '/{id}');
    });

    // cache all
    $map->get('catchall', '{/controller,action}')->wildcard('parts')
        ->defaults([
            'controller' => 'index',
            'action' => 'index',
        ])
    ;

})($routerContainer->getMap());

///

$method = $argv[1] ?? 'GET';
$uri = $argv[2] ?? '/test';

$request = (new ServerRequest())->withUri(new Uri("http://localhost/$uri"))->withMethod($method);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if (!$route) {
    $failed = $matcher->getFailedRoute()->failedRule;
    var_dump($failed);

} else {
    $generator = $routerContainer->getGenerator();

    print_r([
        'name'       => $route->name,
        'handler'    => $route->handler,
        'attributes' => $route->attributes,
        'auth'       => $route->auth,
        'extras'     => $route->extras,
        // リバースルーティング
        'path_r'     => $generator->generate($route->name, $route->attributes)
    ]);
}

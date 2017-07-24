# php-router-libs-example

- Aura.Router
    - https://github.com/auraphp/Aura.Router
    - https://packagist.org/packages/aura/router
    - https://github.com/auraphp/Aura.Router/blob/3.x/docs/index.md
- FastRoute
    - https://github.com/nikic/FastRoute
    - https://packagist.org/packages/nikic/fast-route
    - http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
- zend-router
    - https://github.com/zendframework/zend-expressive-router
    - https://packagist.org/packages/zendframework/zend-router

## FastRoute

- 普通
- 正規表現でパターンを指定
- 巨大な単一の正規表現にコンパイルされる
- 固定のルートは正規表現にはならずに連想配列
- コンパイル結果をキャッシュできる
- リバースルーティングできない？

## Aura.Router

- 普通
- ホスト名とかヘッダとかもルーティングの条件にできる
- PSR-7 Request が必須
- たぶんコンパイル結果をキャッシュできる
- リバースルーティングできる
- catchall できる
    - `$map->get('catchall', '{/controller,action}')->wildcard('parts')->defaults(['controller' => 'index', 'action' => 'index'])`

## zend-router

- 辛そう

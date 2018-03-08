# Turbolinks Laravel

Turbolinks Laravel 可以让你在 [Laravel](https://laravel.com) 应用中使用 [Turbolinks](https://github.com/turbolinks/turbolinks)。 编码实现参考：[turbolinks-rails](https://github.com/turbolinks/turbolinks-rails)

## 环境要求

* laravel >= 5.5
* turbolinks >= 5.1.0

## 安装

```
composer require "lym125/turbolinks-laravel"
```

## 使用

1. 安装 [turbolinks](https://github.com/turbolinks/turbolinks/tree/v5.1.0)。

2. 在你的 `Laravel` 应用中找到 `app/Http/Kernel.php` 文件，然后注册中间件。

```php
protected $routeMiddleware = [
    ...
    'turbolinks' => \Lym125\Turbolinks\Turbolinks::class,
];
```

3. 在路由中使用注册的 `turbolinks` 中间件。

```php
Route::group(['middleware' => ['turbolinks']], function () {
    //
});
```

4. [更多使用方式](https://laravel.com/docs/5.6/middleware)。
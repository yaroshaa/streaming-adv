# Swagger
Publish a config and all view files

```shell
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

Generating Swagger documentation

```shell
php artisan l5-swagger:generate
```

Check generated api docs by url
http://localhost:8000/api/documentation

Swagger additional information
- https://github.com/DarkaOnLine/L5-Swagger
- https://swagger.io/docs/specification/about/
- https://github.com/zircote/swagger-php
- https://github.com/swagger-api/swagger-ui
- https://ikolodiy.com/posts/laravel-swagger-tips-examples
- https://github.com/zircote/swagger-php/blob/master/Examples/petstore-3.0/controllers/Pet.php
- https://github.com/zircote/swagger-php/blob/master/Examples/petstore-3.0/models/RequestBody.php

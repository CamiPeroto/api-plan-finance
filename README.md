## Requisitos
* PHP 8.2 ou superior
* MySQL 8 ou superior
* Composer

## Como rodar o projeto baixado

## Sequência para criar o projeto
Criar projeto com Laravel
```
composer create-project laravel/laravel .
```

Alterar no arquivo .env as credencias do banco de dados<br>

Instalar o Laravel Sanctum para a API
```
php artisan install:api
```
Gerar a documentação com Swagger
```
 php artisan l5-swagger:generate
```
Rodar o servidor
```
php artisan serve --port=8001
```
Rodar o servidor para o Rafael acessar na mesma rede
```
php artisan serve --host=0.0.0.0 --port=8001
```

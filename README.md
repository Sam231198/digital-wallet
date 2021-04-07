# Digital Wallet

Uma API que simula uma um carteira digital simples que realizar transições entre usuários e transferência de um usuário para uma empresa.

## Tecnologias

 - [PHP 8](https://www.php.net/releases/8.0/pt_BR.php)
 - [Composer](https://getcomposer.org/)
 - [MySQL](https://www.mysql.com/)
 - [Lumen 8](https://lumen.laravel.com/)

## Rodar Localmente

 Faça o clone desse repositório em sua maquina local:
```bash
git clone https://github.com/Sam231198/digital_wallet  
```

Depois que o clone do projeto estiver conclui, dentro da pasta raiz do projeto rode o `composer` para instalar as dependências:
```bash
cd  digital_wallet
composer install
```

Em seguida rode a `migrate` para gerar o banco:
```php
php artisan migrate
```

por fim vamos rodar a aplicação:
```php
php -S localhost:8000 -t public
```
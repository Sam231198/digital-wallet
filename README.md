
# Digital Wallet
Uma API que simula uma um carteira digital simples que realizar transições entre usuários e transferência de um usuário para uma empresa.

## Tecnologias

-  [PHP 8](https://www.php.net/releases/8.0/pt_BR.php)
-  [Composer](https://getcomposer.org/)
-  [MySQL](https://www.mysql.com/)
-  [Lumen 8](https://lumen.laravel.com/)

## Rodar Localmente

Faça o clone desse repositório em sua maquina local:
```bash
$ git clone https://github.com/Sam231198/digital_wallet
```

Depois que o clone do projeto estiver concluido, dentro da pasta raiz do projeto rode o `composer` para instalar as dependências:
```bash
$ cd digital_wallet
$ composer install
```

Em seguida rode a `migrate` para gerar o banco:
```php
$ php  artisan  migrate
```
por fim vamos rodar a aplicação:
```php
$ php  -S  localhost:8000  -t  public
```

## Utilizando a API
Para testar os links, utilize um API Client Rest, o [Insominia](https://insomnia.rest/download) é uma ótima opção, mas pode usar o que preferir.

### Listar Pessoas Físicas:
Para receber uma lista de pessoas físicas basta fazer uma requisição `GET` para `localhost:8000/pf/listar` que será retornado uma lista de dados de pessoas físicas em JSON.

### Cadastrar Pessoas Físicas:
Para cadastrar uma pessoas físicas basta fazer uma requisição `POST` para `localhost:8000/pf/cadastrar`, passando esses valores em JSON:

```json
{
	"nome" : "Herculano Cicrano",
	"cpf" : "51191900061",
	"email" : "herculano@gmail.com",
	"saldo" : 500.00,
	"senha" : "123"
}
```

> O `saldo` é opcional, o valor padrão é `0.00`.

> Ao final do cadastro será retornado um JSON com as informações da conta.

### Listar Pessoas Jurídica:
Para receber uma lista de pessoas jurídica basta fazer uma requisição `GET` para `localhost:8000/pj/listar` que será retornado uma lista de dados de pessoas jurídica em JSON.

### Cadastrar Pessoas Jurídica:
Para cadastrar uma pessoas jurídica basta fazer uma requisição `POST` para `localhost:8000/pf/cadastrar`, passando esses valores em JSON:

```json
{
	"nome" : "Herculano LTDA",
	"cnpj" : "21975454000158",
	"email" : "herculanoEmpresa@gmail.com",
	"saldo" : 500.00,
	"senha" : "123"
}
```

> O `saldo` é opcional, o valor padrão é `0.00`.

> Ao final do cadastro será retornado um JSON com as informações da conta.

### Consultar Conta
Para consultar a conta, deve se fazer uma requisição `POST` para `localhost:8000/conta/consulta` passando os valores em JSON listados nos exemplos.

Exemplo de consulta da conta de uma pessoa física:
```json
{
	"cpf" : "51191900061",
	"senha" : "123"
}
```


Exemplo de consulta da conta de uma pessoa jurídica:
```json
{
	"cnpj" : "21975454000158",
	"senha" : "123"
}
```

> Ao final do requisição será retornado um JSON com as informações da consulta da conta.

### Transferência
Para realizar uma transferência é necessário fazer uma requisição `POST` para `URL` passando esses valores em JSON:

```json
{
	"cpf" : "51191900061",
	"senha" : "123",
	"valor" : 10.00,
	"receptor" : "84814927000155"
}
```
> Será retornado um JSON com informações de registro da transição.

> Apenas a pessoa física ode realizar transferência, pessoa jurídica só pode receber apenas.

> O campo `receptor` recebe tanto CNPJ quanto CPF.
<h1>Proposta - Transferência Conta - API</h1>
<h3 align="center">Proposta desenvolvida com laravel</h3>


## Instalação 

Clonnando repositório

    git clone https://github.com/acrossicauda/proposta-conta-bancaria.git

Acessando pasta do da aplicação

    cd conta-bancaria

Instalando dependencias do composer

        composer install

Gerando uma cópia do .env que contém as configurações de conexão com o banco

    cp .env.example .env

Gerando uma chave para a aplicação

    php artisan key:generate


Gerando as migrations

    php artisan migrate

Iniciando o servidor local

    php artisan serve

Você poderá acessar o servidor utilizando http://localhost:8000
    

**Rodando as migrations**

    php artisan migrate
    
**Iniciando o servidor da aplicação**
    php artisan serve

## Database seeding

Rodando seeder que contém um unico cliente para testes

    php artisan db:seed

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:refresh
    

##Usando a API
###Saque:

<!-- language: php -->
```
POST /api/transaction HTTP/1.1
Cookie: PHPSESSID=km4fmahjcp9ue9vhr2k0qutsb9
Content-Type: application/json
Host: localhost:8000
Content-Length: 209

{
	"conta": {
		"codigoCliente": 1,
		"Ativa": true,
		"LimiteDisponivel": 100
	},
	"transacao": [
		{
			"tipo": "saque",
			"motivo": "teste",
			"valor": 30,
			"data": "2021-07-17T17: 00: 00.000Z"
		}
	]
}

```

###Deposito:

<!-- language: php -->
```
POST /api/transaction HTTP/1.1
Cookie: PHPSESSID=km4fmahjcp9ue9vhr2k0qutsb9
Content-Type: application/json
Host: localhost:8000
Content-Length: 211

{
	"conta": {
		"codigoCliente": 1,
		"Ativa": true,
		"LimiteDisponivel": 100
	},
	"transacao": [
		{
			"tipo": "deposito",
			"motivo": "teste",
			"valor": 10,
			"data": "2021-07-17T18: 00:00.000Z"
		}
	]
}
```

# ConectaSUS — Cadastro de Pacientes

Aplicação web desenvolvida em Laravel para cadastrar, consultar, editar e excluir pacientes vinculados a unidades de saúde.

O sistema utiliza uma API REST que retorna dados em JSON e uma interface web desenvolvida com HTML, CSS, JavaScript e Bootstrap.

## Tecnologias utilizadas

- PHP
- Laravel
- MySQL
- HTML, CSS e JavaScript
- Bootstrap 5
- Git e GitHub

## Funcionalidades

- Cadastro de pacientes vinculados a unidades de saúde.
- Listagem e consulta de pacientes.
- Edição e exclusão de pacientes.
- Validação dos dados dos pacientes.
- Pesquisa por nome ou CPF.
- Paginação da listagem.
- Consulta das unidades de saúde disponíveis.

## Requisitos

- PHP compatível com a versão do Laravel utilizada.
- Composer.
- MySQL.
- Git.

## Como executar o projeto

### 1. Clonar o repositório

```bash
git clone https://github.com/Pedro-henrique-1997/conectasus-pacientes.git
cd conectasus-pacientes
```

### 2. Instalar as dependências

```bash
composer install
```

### 3. Configurar o ambiente

No Windows PowerShell, copie o arquivo de exemplo:

```powershell
Copy-Item .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

### 4. Configurar o banco de dados

Crie um banco de dados MySQL chamado `conectasus`.

No arquivo `.env`, configure os dados da conexão:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=conectasus
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

Substitua `seu_usuario` e `sua_senha` pelas credenciais do seu MySQL.

### 5. Criar as tabelas e cadastrar as unidades de saúde

```bash
php artisan migrate --seed
```

Esse comando executa as migrations pendentes e o `DatabaseSeeder`, que chama o `HealthUnitSeeder`.

### 6. Iniciar a aplicação

```bash
php artisan serve
```

Acesse a aplicação pelo navegador:

- Listagem de pacientes: http://127.0.0.1:8000/patients
- Cadastro de pacientes: http://127.0.0.1:8000/patients/create

## Endpoints da API

| Método | Endpoint | Descrição |
|---|---|---|
| GET | `/api/health-units` | Listar unidades de saúde |
| GET | `/api/patients` | Listar pacientes |
| POST | `/api/patients` | Cadastrar paciente |
| GET | `/api/patients/{id}` | Consultar paciente por ID |
| PUT | `/api/patients/{id}` | Atualizar paciente |
| DELETE | `/api/patients/{id}` | Excluir paciente |

A listagem de pacientes permite pesquisa pelo parâmetro `search` e utiliza paginação.

Exemplo: `/api/patients?search=Maria`

## Consultas SQL

O arquivo `sql/consultas.sql` contém três consultas SQL:

1. Listar pacientes com suas respectivas unidades de saúde.
2. Contar pacientes por unidade, incluindo unidades sem pacientes.
3. Pesquisar pacientes pelo nome utilizando `LIKE`.

## Observações

- O arquivo `.env` contém configurações locais e não deve ser compartilhado.
- As migrations criam a estrutura do banco de dados.
- O seeder cadastra as unidades de saúde iniciais.

Projeto desenvolvido como desafio técnico.

Criado por Pedro Henrique

Github: https://github.com/Pedro-henrique-1997

Linkedin: https://www.linkedin.com/in/pedro-hgomes-dev

# Framework by William Omar Gibram

Este projeto é um framework em desenvolvimento, criado com o objetivo de explorar e entender os fundamentos de frameworks PHP, além de aprimorar habilidades técnicas. **Não é recomendado para uso em produção.**

---

## Objetivo

O principal objetivo deste framework é:

- **Aprender:** Entender como frameworks funcionam internamente.
- **Explorar:** Sair do básico de CRUDs e trabalhar em algo mais técnico e desafiador.
- **Criar:** Desenvolver um framework funcional e documentado.

---

## Estrutura do Projeto

Abaixo está uma visão geral da estrutura do projeto e suas principais responsabilidades:

### **1. Engine**
Contém os componentes principais que dão suporte ao framework:

- **Router:** Gerencia as rotas e controla o fluxo de requisições.
- **Render:** Responsável por renderizar controladores, modelos e views.
- **Http:** Captura e armazena informações da requisição HTTP.
- **Access:** Gerencia sessões e permissões de acesso.

### **2. App**
Contém a lógica da aplicação:

- **Controller:** Controladores que processam as requisições e chamam os modelos.
- **Model:** Modelos que lidam com a lógica de negócios e acesso ao banco de dados.
- **View:** Arquivos de visualização que exibem os dados ao usuário.

### **3. Public**
Contém os arquivos públicos acessíveis pelo navegador:

- **Index.php:** Ponto de entrada da aplicação.
- **CSS/JS:** Arquivos de estilo e scripts para a interface.

---

## Fluxo de Requisição

1. **Entrada:** Todas as requisições passam pelo arquivo `Public/Index.php`.
2. **Roteamento:** O `Router` identifica a rota e verifica permissões.
3. **Renderização:**
   - Se for uma rota protegida, verifica se o usuário está logado.
   - Renderiza o controlador, modelo e view correspondente.
4. **Resposta:** Retorna a resposta ao cliente.

---

## Funcionalidades

- **Gerenciamento de Rotas:**
  - Rotas protegidas, livres e sem necessidade de login.
  - Suporte a métodos HTTP (GET, POST, etc.).

- **Sistema de Permissões:**
  - Controle de acesso baseado em permissões do usuário.

- **Renderização Dinâmica:**
  - Renderiza controladores, modelos e views com base na rota.

- **Conexão com Banco de Dados:**
  - Gerenciado pela classe `Database` com suporte a `.env`.

---

## Tecnologias Utilizadas

- **PHP:** Linguagem principal do framework.
- **Composer:** Gerenciamento de dependências.
- **MySQL:** Banco de dados para persistência.
- **PHPUnit:** Testes automatizados.

---

## Como Usar

1. **Instale as dependências:**
   Certifique-se de que o Composer está instalado em sua máquina e execute o comando abaixo na raiz do projeto:
   ```bash
   composer install

2. **Configure o arquivo .env:**
    Crie um arquivo .env na raiz do projeto (se ainda não existir) e adicione as informações do banco de dados, como no exemplo abaixo:

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha



## Estrutura de Rotas
As rotas são definidas na classe Engine\Router\Routes. Exemplos:

- Livre: /login
- Protegida: /administrativo/almoxarifado
- Sem necessidade de login: /page-not-found

## Contribuição
Este projeto é pessoal e focado no aprendizado. No entanto, sugestões e feedbacks são bem-vindos.

## Observações
JIRA: O progresso do projeto pode ser acompanhado no JIRA.
Nota: Este framework é um projeto experimental e está em constante evolução. ```


## Comunicação entre Routes, Controller, Model e View

A estrutura do framework segue um fluxo bem definido para processar requisições e renderizar respostas. Abaixo está uma explicação detalhada de como as partes **Routes**, **Controller**, **Model** e **View** se comunicam:

---

## Comunicação entre Routes, Controller, Model e View

A estrutura do framework segue um fluxo bem definido para processar requisições e renderizar respostas. Abaixo está uma explicação detalhada de como as partes **Routes**, **Controller**, **Model** e **View** se comunicam:

---

### **1. Routes**
As rotas são definidas na classe `Engine\Router\Routes`. Cada rota possui uma URI, um método HTTP (GET, POST, etc.) e uma referência ao **Controller** e à ação que será executada.

Exemplo de rota:
```php
"/login" => [
    "POST" => [
        "Controller" => "Login@loggin",
    ],
],
```
- URI: /login
- Método: POST
- Controller: Login (classe) e loggin (método)

O nome antes do @ refere-se ao Controller, enquanto o nome após o @ refere-se ao método (ação) que será executado.

---

2. **Router**
O Router interpreta a rota solicitada e determina qual Controller e Action devem ser executados. Ele separa o nome do Controller e da Action com base no formato Controller@Action e delega a execução para a classe responsável por renderizar o Controller.

3. **Controller**
O Controller processa a lógica da requisição e interage com o Model. Ele é responsável por validar os dados e chamar os métodos do Model necessários.

```php
<?php
namespace App\Controller\Login;

use App\Model\Login\Login as LoginModel;

class Login
{
    public static function loggin()
    {
        $user_Input_Is_Valid = Aux_Login::validate_User_Input_For_Login($_POST['username'], $_POST['password']);
        if ($user_Input_Is_Valid) {
            LoginModel::loggin($user_Input_Is_Valid['username'], $user_Input_Is_Valid['password']);
        }
    }
}
```
- O método loggin valida os dados do usuário e chama o método correspondente no Model (LoginModel::loggin).

4. **Model**
O Model lida com a lógica de negócios e o acesso ao banco de dados. Ele é chamado pelo Controller e retorna os dados processados.

Exemplo de Model:

```php
<?php
namespace App\Model\Login;

use App\Model\Database\Database;

class Login
{
    public static function loggin($username, $password)
    {
        $conn = Database::conectaDB();
        $sql = "SELECT * FROM TBL_USERS WHERE COL_USERS_EMAIL = :username LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result && password_verify($password, $result['COL_USERS_PASSWORD'])) {
            // Inicializa a sessão e retorna sucesso
        } else {
            echo json_encode(["message" => "Credenciais Invalidas"]);
        }
    }
}
```
- O Model realiza a consulta no banco de dados e retorna os dados processados para o Controller.

5. **View**
A View é responsável por exibir os dados ao usuário. Ela é chamada pelo Controller e renderizada com os dados retornados pelo Model.

Exemplo de View:

```
<h1>Bem-vindo ao Login</h1>
<form method="post">
    <input type="email" name="username" placeholder="Email">
    <input type="password" name="password" placeholder="Senha">
    <button type="submit">Entrar</button>
</form>
```
- A View recebe os dados processados pelo Model e os exibe no formato HTML.

### Fluxo Completo
- O Router identifica a rota e separa o Controller e a Action.
- O Controller é carregado e executa a lógica da requisição.
- O Model é chamado pelo Controller para acessar ou manipular os dados.
- A View é renderizada com os dados retornados pelo Model.
- Essa estrutura modular facilita a manutenção e escalabilidade do framework.

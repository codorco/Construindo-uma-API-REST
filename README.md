# API REST - Sistema de Gerenciamento de Clientes

![PHP Version](https://img.shields.io/badge/php-8.2-blue.svg)
![MySQL Version](https://img.shields.io/badge/mysql-8.0-orange.svg)
![Docker](https://img.shields.io/badge/docker-ready-blue.svg)
![Status](https://img.shields.io/badge/status-conclu%C3%ADdo-brightgreen)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Esta API foi desenvolvida como projeto de estudos para compreender o funcionamento de uma arquitetura REST utilizando **PHP**, **MySQL** e **Docker**.

A seguir você encontra a documentação de uso, configuração e uma visão geral dos recursos disponíveis.

---

## 🛠️ Requisitos

- Docker e Docker Compose instalados na máquina.
- PHP 8.2 (quando for rodar sem container).
- MySQL 8.0 (pode ser o contêiner definido abaixo).

> ⚠️ A autenticação é feita via HTTP Basic Auth; certifique‑se de possuir um usuário na tabela `users` com senha criptografada (password_hash).

---

## 🚀 Inicializando o ambiente

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/codorco/Construindo-uma-API-REST.git
   cd "Construindo uma API REST"
   ```

2. **Configurar as variáveis de ambiente**
   - Copie o arquivo de exemplo e edite com as credenciais do banco:
     ```bash
     cp _inc/config.sample.php _inc/config.php
     # abra _inc/config.php e preencha MYSQL_HOST, MYSQL_DATABASE, etc.
     ```

3. **Construir e subir os containers**
   ```bash
   docker-compose up --build
   ```

   O serviço PHP estará disponível em `http://localhost` e o MySQL em `localhost:3306`.

4. **Criar as tabelas no banco** (pode fazer via mysql client dentro do container `db`):
   ```sql
   CREATE TABLE clientes (
     id INT AUTO_INCREMENT PRIMARY KEY,
     nome VARCHAR(255) NOT NULL,
     sexo CHAR(1) NOT NULL,
     data_nascimento DATE NOT NULL,
     email VARCHAR(255) NOT NULL,
     telefone VARCHAR(50) NOT NULL,
     morada VARCHAR(255) NOT NULL,
     cidade VARCHAR(100) NOT NULL,
     ativo TINYINT(1) NOT NULL
   );

   CREATE TABLE users (
     id INT AUTO_INCREMENT PRIMARY KEY,
     username VARCHAR(50) NOT NULL UNIQUE,
     passwrd VARCHAR(255) NOT NULL
   );
   ```

   > **Dica:** insira um usuário teste com `INSERT INTO users (username, passwrd) VALUES ('admin', '<hash>');` onde `<hash>` é gerado por `password_hash('sua_senha', PASSWORD_DEFAULT)`.

---

## 🔐 Autenticação

Todas as requisições devem incluir credenciais HTTP Basic Auth válidas. Se não forem fornecidas ou estiverem incorretas, a API responde com erro.

Adicionalmente, toda rota admite um campo opcional `integration_key` (no query string ou corpo JSON). Ele será devolvido no campo `integration_key` da resposta, útil para correlacionar chamadas em sistemas externos.

---

## 📦 Estrutura das respostas

```json
{
  "status": "success" | "error",
  "error_message": "mensagem de erro (se houver)",
  "data": ... ,          // objeto ou array com o resultado
  "<campos_adicionais>": ... ,
  "integration_key": "..." , // se enviado
  "time_response": 167... ,
  "api_version": "1.0.0"
}
```

Alguns endpoints adicionam campos extras (por exemplo, `total_clients`).

---

## 📁 Endpoints disponíveis

### 1. Verificar status da API
- **Método:** `GET`
- **URL:** `/api_status/`

> Retorna apenas o estado (`success`) e a versão.

### 2. Criar novo cliente
- **Método:** `POST`
- **URL:** `/add_new_client/`
- **Corpo (JSON):**
  ```json
  {
    "nome": "João Silva",
    "sexo": "m",
    "data_nascimento": "1990-01-01",
    "email": "joao@example.com",
    "telefone": "51999990000",
    "morada": "Rua X, 123",
    "cidade": "Porto Alegre",
    "ativo": 1,
    "integration_key": "opcional"
  }
  ```

> A API rejeita nomes duplicados e retorna erro em caso de campos faltantes.

### 3. Listar todos os clientes
- **Método:** `GET`
- **URL:** `/get_all_clients/`
- **Query string opcional:** `?integration_key=...`

> A resposta traz `data` com o array de clientes e `total_clients` com a contagem.

### 4. Obter cliente por ID
- **Método:** `GET`
- **URL:** `/get_client/?id=<número>`

### 5. Filtrar por cidade
- **Método:** `GET`
- **URL:** `/get_clients_by_city/?city=<nome>`
- Retorna os clientes da cidade informada e o campo `total_clients`.

### 6. Filtrar por domínio de email
- **Método:** `GET`
- **URL:** `/get_clients_by_email_domain/?domain=<domínio>`
- Faz `LIKE %@domínio%` na coluna de email.

### 7. Contagem de homens e mulheres
- **Método:** `GET`
- **URL:** `/get_total_males_and_females/`
- Resposta com dois registros: sexo (`Homens`/`Mulheres`) e total.

### 8. Atualizar nome do cliente
- **Método:** `PUT`
- **URL:** `/update_client_name/`
- **Corpo (JSON):** `{ "id": 1, "nome": "novo nome", "integration_key": "..." }`

### 9. Atualizar email ou telefone
- **Método:** `PUT`
- **URL:** `/update_client_email_or_phone/`
- **Corpo (JSON):** precisa selecionar o `id` colocar o novo `email` ou `telefone` ou ambos.

### 10. Deletar cliente
- **Método:** `DELETE`
- **URL:** `/delete_client/`
- **Corpo (JSON):** `{ "id": 1 }`

---

## 💡 Observações

- Todos os corpos esperam `Content-Type: application/json`.
- Erros de validação ou falta de parâmetros retornam `status: "error"` com mensagem explicativa.
- A API utiliza transações simples e `PDO` para acesso ao MySQL (classe em `_inc/Database.php`).

---

## 🧩 Estrutura de pastas

```
./
├── _inc/           # código de apoio (config, banco, helpers, resposta)
├── add_new_client/
├── delete_client/
├── get_all_clients/
├── get_client/
├── get_clients_by_city/
├── get_clients_by_email_domain/
├── get_total_males_and_females/
├── update_client_email_or_phone/
├── update_client_name/
├── api_status/
├── Dockerfile
├── docker-compose.yml
└── README.md       # (este arquivo)
```

---

## 📝 Licença
Projeto de estudo — sinta‑se à vontade para usar e adaptar. Cite a origem se reutilizar partes significativas.

---

# 👨‍💻 Autor

Codorco  
https://github.com/codorco

Projeto desenvolvido para fins de estudo e prática.

Projeto feito com base no Curso: [Desenvolvimento Web Compacto e Completo - João Ribeiro  ](https://www.udemy.com/course/desenvolvimento-web-compacto-e-completo/)

---

# 📅 Última atualização

03 de Março de 2026
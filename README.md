# 👨‍💻 Sistema de Gerenciamento de Usuários

![Badge](https://img.shields.io/badge/PHP-MVC-blue) ![Badge](https://img.shields.io/badge/MySQL-Database-orange) ![Badge](https://img.shields.io/badge/Author-marcelolii-success)

> Projeto completo de gerenciamento de usuários utilizando PHP com padrão MVC, PDO e MySQL. Seguro, organizado e extensível! 🚀

---

## 🛠️ Tecnologias Utilizadas

- 🔹 PHP (sem frameworks)
- 🔸 PDO para conexão com banco de dados
- 🧩 Arquitetura MVC
- 🗄️ MySQL
- 🔐 Hash de senha com `password_hash`
- 📄 Variáveis de ambiente com `.env`

---

## 📸 Funcionalidades

- ✅ Cadastro de usuários
- ✅ Autenticação com sessão
- ✅ Sistema de permissões (usuário/admin)
- ✅ Listagem, edição e exclusão de usuários
- ✅ Proteção contra CSRF e XSS
- ✅ Mensagens de feedback (sucesso/erro)
- ✅ Página de login protegida

---

## 🔒 Segurança

- 🔐 Uso de `password_hash` e `password_verify` para proteger senhas
- 🧼 Escapando HTML com `htmlspecialchars`
- 🔒 Sessões protegidas com verificação de login
- 🔎 Validação de entrada e mensagens seguras

---

## ⚙️ Como instalar

```bash
# Clone o repositório
git clone https://github.com/marcelolii/sistema-usuarios.git

# Entre na pasta do projeto
cd sistema-usuarios

# Crie o banco de dados no MySQL e importe o arquivo SQL (caso exista)

# Configure seu arquivo .env dentro da pasta /Engine
cp .env.example .env

# Edite o .env com suas credenciais de banco:
DB_HOST=localhost
DB_NAME=sistema
DB_USER=root
DB_PASS=sua_senha

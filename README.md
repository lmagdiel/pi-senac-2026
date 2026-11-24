# App Help Desk

Sistema web para abertura, acompanhamento e gerenciamento de chamados de suporte técnico.

Projeto desenvolvido como parte do **Projeto Integrador do SENAC – 2026**.

**Video demonstrativo:** https://drive.google.com/file/d/1OHOq2RM3VhpzyWO90FcfayuH7HHP8HDD/view?usp=sharing

## Sobre o projeto

O App Help Desk centraliza o registro e o acompanhamento de solicitações de suporte técnico. Usuários podem abrir chamados e consultar suas solicitações, enquanto técnicos e administradores podem acompanhar e atualizar os atendimentos de acordo com suas permissões.

## Funcionalidades

- Cadastro e autenticação de usuários.
- Controle de acesso por perfil: **Usuário**, **Técnico** e **Administrador**.
- Abertura de chamados com título, categoria e descrição.
- Consulta dos chamados.
- Acompanhamento dos chamados por status: **Aberto**, **Em andamento** e **Finalizado**.
- Atualização de chamados por técnicos e administradores, incluindo descrição técnica e valor.
- Administração de usuários: consulta, edição, exclusão e autorização/definição de perfil.
- Relatórios com totais de chamados por status.

## Tecnologias

- **PHP** – lógica da aplicação e processamento das requisições.
- **MySQL** – banco de dados.
- **HTML e CSS** – estrutura e apresentação das páginas.
- **Bootstrap 4** – componentes e estilos da interface.

## Estrutura do repositório

```text
pi-senac-2026/
├── app_help_desk_bd/       # Páginas e funcionalidades da aplicação
├── app_help_desk_bd_cima/  # Configuração do banco e lógica sensível de autenticação
└── database/
    ├── banco.sql           # Criação do banco e das tabelas
    ├── migracao_status.sql # Migração para a versão atual
    └── bd.txt              # Anotações e comandos SQL de apoio
```

A pasta `app_help_desk_bd_cima` contém arquivos chamados pela aplicação, mas que devem ficar fora da raiz pública do servidor web sempre que possível. O arquivo `.htaccess` incluído nela bloqueia o acesso direto quando o Apache está configurado para respeitá-lo.

## Requisitos

- PHP com as extensões `mysqli` e `mysqlnd` habilitadas.
- MySQL ou MariaDB.
- Servidor web Apache (por exemplo, pelo XAMPP).
- Navegador atualizado.

## Como instalar e executar localmente

### 1. Clonar o repositório

```bash
git clone https://github.com/lmagdiel/pi-senac-2026.git
```

### 2. Configurar o servidor local

Inicie o Apache e o MySQL pelo XAMPP (ou ambiente equivalente).

Coloque a pasta do projeto no diretório servido pelo Apache, como `htdocs` no XAMPP. Preserve a estrutura de diretórios do repositório, pois a aplicação procura os arquivos de configuração em `app_help_desk_bd_cima` a partir de `app_help_desk_bd`.

### 3. Criar o banco de dados

No phpMyAdmin ou em um cliente MySQL, execute o script:

```text
database/banco.sql
```

Esse script cria o banco `helpdesk`, as tabelas `usuarios` e `chamados` e um usuário administrador inicial.

### 4. Configurar a conexão

Revise as constantes de conexão no arquivo:

```text
app_help_desk_bd_cima/config.php
```

Ajuste `HOST`, `USER`, `PASS` e `BASE` conforme o ambiente MySQL local. Por padrão, o arquivo está configurado para `localhost`, usuário `root`, senha vazia e banco `helpdesk`.

### 5. Acessar a aplicação

Com Apache e MySQL em execução, abra no navegador:

```text
http://localhost/pi-senac-2026/app_help_desk_bd/
```

Se a pasta tiver outro nome dentro de `htdocs`, ajuste o endereço de acordo.

## Acesso inicial

O script `database/banco.sql` insere um usuário administrador inicial:

| Campo | Valor |
|---|---|
| E-mail | `marcos@senacsp.edu.br` |
| Senha inicial | `123` |

**Importante:** altere a senha inicial após o primeiro acesso e não utilize essas credenciais em um ambiente público.

## Banco de dados

O banco `helpdesk` possui duas tabelas principais:

- **usuarios:** armazena nome, e-mail, senha e perfil de acesso.
- **chamados:** armazena título, categoria, descrição, usuário responsável pela abertura, status, descrição técnica e valor.

### Atualização de bancos existentes

Se você já possui uma instalação anterior do banco e está atualizando para a versão atual do projeto, execute:

```text
database/migracao_status.sql
```

O script atualiza o status legado `Andamento` para `Em andamento` e amplia o campo de senha para suportar os hashes gerados por `password_hash`. Faça backup antes de executar a migração.

## Observações de segurança

- Configure credenciais próprias para o banco de dados.
- Não publique senhas reais nem arquivos de configuração com credenciais de produção.
- A pasta `app_help_desk_bd_cima` contém lógica sensível e deve permanecer fora da raiz pública do servidor sempre que a configuração do ambiente permitir.
- O `.htaccess` ajuda a bloquear o acesso direto a essa pasta em servidores Apache, mas não substitui uma configuração segura do servidor.
- Utilize HTTPS e credenciais fortes em ambientes publicados.

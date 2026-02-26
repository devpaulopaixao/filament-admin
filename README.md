# Gestão de Painéis

> Plataforma moderna para gerenciamento e exibição de painéis digitais em telas, com transmissão em tempo real, controle granular de acesso e painel administrativo completo.

---

## Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
  - [Painéis](#painéis)
  - [Telas](#telas)
  - [Usuários e Permissões](#usuários-e-permissões)
  - [Dashboard](#dashboard)
- [Stack Tecnológica](#stack-tecnológica)
- [Instalação](#instalação)
  - [Pré-requisitos](#pré-requisitos)
  - [Configuração](#configuração)
  - [Ambiente de Desenvolvimento](#ambiente-de-desenvolvimento)
- [Variáveis de Ambiente](#variáveis-de-ambiente)
- [Licença](#licença)

---

## Sobre o Projeto

O **Gestão de Painéis** é uma solução web completa para empresas que precisam gerenciar e distribuir conteúdo digital para múltiplas telas — TVs, monitores, totens e displays corporativos.

Administradores criam **painéis** compostos por links e recursos, que são associados a **telas** físicas distribuídas em diferentes locais. Qualquer atualização feita no painel administrativo é refletida instantaneamente em todas as telas conectadas, via **WebSocket com Laravel Reverb**, sem necessidade de recarregar a página.

O sistema oferece controle granular de permissões por usuário e recurso, registro completo de acessos e organização de painéis por grupos — tudo centralizado em um painel administrativo intuitivo construído com **Filament**.

---

## Funcionalidades

### Painéis

- Criação de painéis com título, status ativo/inativo e hash UUID único
- Agrupamento em **grupos de painéis** (PanelGroups) para melhor organização
- Adição de **links** com ordenação por número de exibição
- Opção de exibir ou ocultar controles na visualização pública
- Controle de usuários com acesso individual a cada painel (relação many-to-many)
- Broadcast automático a cada atualização via evento `PanelUpdated`

### Telas

- Cadastro de telas vinculadas a painéis específicos
- Controle de usuários com acesso por tela
- Registro detalhado de logs de acesso (`ScreenAccessLog`) com data e hora
- Broadcast automático a cada atualização via evento `ScreenUpdated`

### Usuários e Permissões

- Gerenciamento completo de usuários diretamente no painel administrativo
- Papéis e permissões granulares com **Spatie Laravel Permission** integrado ao **Filament Shield**
- Controle de acesso por recurso — defina exatamente o que cada usuário pode ver e fazer

### Dashboard

- Widgets de estatísticas gerais com visão consolidada (`StatsOverviewWidget`)
- Listagem dos painéis criados mais recentemente (`LatestPanelsWidget`)
- Listagem das telas criadas mais recentemente (`LatestScreensWidget`)

---

## Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Framework PHP | Laravel 11 |
| Painel Administrativo | Filament 4.0 |
| Controle de Papéis e Permissões | Spatie Laravel Permission + Filament Shield |
| WebSocket / Broadcast em Tempo Real | Laravel Reverb |
| Banco de Dados | MySQL |
| Frontend (Admin) | Livewire + Alpine.js (via Filament) |
| Build de Assets | Vite |

---

## Instalação

### Pré-requisitos

- PHP >= 8.2
- Composer
- Node.js e npm
- MySQL

### Configuração

Clone o repositório e instale as dependências:

```bash
git clone <repositório>
cd filament-admin
composer install
npm install
```

Prepare o ambiente:

```bash
cp .env.example .env
php artisan key:generate
```

Configure o arquivo `.env` com as credenciais do banco de dados e do Reverb (veja a seção [Variáveis de Ambiente](#variáveis-de-ambiente)), depois execute:

```bash
php artisan migrate
php artisan db:seed                        # opcional — popula dados iniciais
php artisan shield:generate --all          # gera permissões do Filament Shield
npm run build
```

### Ambiente de Desenvolvimento

Inicie os serviços em terminais separados:

```bash
php artisan serve            # servidor HTTP
php artisan reverb:start     # servidor WebSocket
npm run dev                  # Vite com hot-reload
```

---

## Variáveis de Ambiente

As principais variáveis que devem ser configuradas no arquivo `.env`:

```env
APP_NAME="Gestão de Painéis"
APP_URL=http://seu-dominio.com
APP_TIMEZONE=America/Sao_Paulo

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel

BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=localhost
REVERB_PORT=8080
```

---

## Licença

Este projeto está licenciado sob a [MIT License](https://opensource.org/licenses/MIT).

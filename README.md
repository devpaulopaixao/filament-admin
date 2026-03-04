# Gestão de Painéis

> Plataforma moderna para gerenciamento e exibição de painéis digitais em telas, com transmissão em tempo real, controle granular de acesso e painel administrativo completo.

---

## Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
  - [Painéis](#painéis)
  - [Grupos de Painéis](#grupos-de-painéis)
  - [Telas](#telas)
  - [Usuários e Permissões](#usuários-e-permissões)
  - [Dashboard](#dashboard)
  - [Auditoria](#auditoria)
  - [Exibição Pública](#exibição-pública)
  - [Monitoramento](#monitoramento)
- [Stack Tecnológica](#stack-tecnológica)
- [Instalação](#instalação)
  - [Pré-requisitos](#pré-requisitos)
  - [Configuração](#configuração)
  - [Ambiente de Desenvolvimento](#ambiente-de-desenvolvimento)
- [Variáveis de Ambiente](#variáveis-de-ambiente)
- [Licença](#licença)

---

## Sobre o Projeto

Sistema de Gestão de Painéis Embedados(Power Bi, Looker e sites que liberam esta funcionalidade)

Desenvolvemos o Sistema de Gestão de Painéis Digitais para centralizar o controle de conteúdo exibido em telas distribuídas por diferentes localidades. A partir desse sistema é possível:

- Criar e gerenciar painéis digitais com links e conteúdos ordenados;
- Organizar painéis em grupos para facilitar a administração;
- Vincular painéis a telas físicas (TVs, monitores e totens) de forma simples;
- Atualizar o conteúdo em tempo real em todas as telas conectadas, via WebSocket;
- Controlar o acesso de usuários por painel ou grupo de painéis;
- Definir perfis de acesso com permissões granulares por funcionalidade;
- Ativar ou desativar painéis, links e telas conforme necessário;
- Acompanhar todas as ações realizadas no sistema por meio de trilha de auditoria completa, registrando usuário, data, IP e valores anteriores e posteriores a cada alteração;
- Monitorar a performance da aplicação (memória, banco de dados, requests HTTP, filas) com Laravel Pulse.

---

## Funcionalidades

### Painéis

- Criação de painéis com título, status ativo/inativo e hash UUID único
- Opção de exibir ou ocultar controles e título na visualização pública
- Agrupamento em **grupos de painéis** (PanelGroups) para melhor organização
- Adição de **links** com URL, duração de exibição e ordenação por número de exibição
- Controle de usuários com acesso individual a cada painel e grupo (relação many-to-many)
- Duplicação de painéis com cópia de links e usuários permitidos
- Broadcast automático a cada atualização via evento `PanelUpdated`
- Soft delete com possibilidade de restauração

### Grupos de Painéis

- Criação de grupos para organizar painéis de forma hierárquica
- Controle de usuários com acesso por grupo (relação many-to-many)
- Contagem de painéis vinculados exibida diretamente na listagem
- Soft delete com possibilidade de restauração

### Telas

- Cadastro de telas vinculadas a painéis específicos
- Controle de usuários com acesso por tela
- Detecção automática de tipo de dispositivo (mobile, tablet, desktop)
- Registro detalhado de logs de acesso (`ScreenAccessLog`) com data, hora, IP e dispositivo
- Widget de estatísticas de acesso: total, últimas 24h, últimos 7 dias e dispositivo mais utilizado
- Gráfico de tendência de acessos com filtros de 7, 30 e 90 dias
- Broadcast automático a cada atualização via evento `ScreenUpdated`
- Soft delete com possibilidade de restauração

### Usuários e Permissões

- Gerenciamento completo de usuários diretamente no painel administrativo
- Papéis e permissões granulares com **Spatie Laravel Permission** integrado ao **Filament Shield**
- Atribuição de permissões diretas ao usuário, independentemente do papel
- Controle de acesso por recurso — defina exatamente o que cada usuário pode ver e fazer
- Duplicação de usuários com cópia de papéis e permissões
- **Impersonação de usuários**: administradores podem acessar o sistema como outro usuário sem precisar da senha
- Soft delete com possibilidade de restauração

### Dashboard

- Widgets de estatísticas gerais com visão consolidada e filtragem por papel (`StatsOverviewWidget`)
- Listagem dos painéis criados mais recentemente (`LatestPanelsWidget`)
- Listagem das telas criadas mais recentemente (`LatestScreensWidget`)
- Alternância de idioma (Português / Inglês)

### Auditoria

- Trilha de auditoria completa de todas as alterações realizadas no sistema
- Registro de usuário responsável, data, IP, user agent e URL da ação
- Visualização dos valores anteriores e posteriores a cada alteração
- Evento customizado `links_updated` para rastrear mudanças nos links de um painel (antes e depois)
- Filtros por tipo de modelo e por usuário responsável

### Exibição Pública

- Rota pública `/painel/{hash}` para exibição de painéis embedados
- Rota pública `/tela/{id}` para exibição de telas vinculadas
- Acesso autenticado via token criptografado com expiração de 24 horas (AES-256-GCM)
- API interna para carregamento de dados encriptados pelo frontend

### Monitoramento

- Dashboard de performance integrado via **Laravel Pulse**
- Monitoramento de uso de memória, queries ao banco de dados, requests HTTP e filas
- Acesso restrito ao papel `super_admin`

---

## Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Framework PHP | Laravel 11 |
| Painel Administrativo | Filament 4.0 |
| Controle de Papéis e Permissões | Spatie Laravel Permission + Filament Shield |
| WebSocket / Broadcast em Tempo Real | Laravel Reverb + Laravel Echo + Pusher JS |
| Banco de Dados | MySQL |
| Frontend (Admin) | Livewire + Alpine.js (via Filament) |
| Frontend (Exibição Pública) | React 19 |
| Criptografia (Frontend) | @noble/ciphers (AES-256-GCM) |
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

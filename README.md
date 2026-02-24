# Gestão de Painéis

Sistema web para gerenciamento e exibição de painéis digitais em telas, com controle de acesso por usuário, transmissão em tempo real e painel administrativo completo.

## Proposta

A aplicação permite que administradores criem **painéis** compostos por links/recursos e os associem a **telas** físicas (TVs, monitores, totens). Cada tela exibe o painel designado em tempo real, com atualizações instantâneas via WebSocket. O sistema conta com controle granular de permissões, registro de acessos e agrupamento de painéis por categorias.

## Stack

| Camada | Tecnologia |
|---|---|
| Framework PHP | Laravel 11 |
| Painel Admin | Filament 4.0 |
| Controle de Papéis e Permissões | Spatie Laravel Permission + Filament Shield |
| WebSocket / Broadcast | Laravel Reverb |
| Banco de Dados | MySQL |
| Frontend (Admin) | Livewire + Alpine.js (via Filament) |
| Build Assets | Vite |

## Recursos

### Painéis
- Criação de painéis com título, status ativo/inativo e hash UUID único
- Agrupamento de painéis em **grupos** (PanelGroups) para organização
- Adição de **links** ordenados por número de exibição em cada painel
- Opção de exibir ou ocultar controles na tela pública
- Controle de usuários com acesso a cada painel (relação many-to-many)
- Transmissão automática via broadcast a cada atualização (`PanelUpdated`)

### Telas (Screens)
- Cadastro de telas vinculadas a painéis
- Controle de usuários com acesso a cada tela
- Registro de logs de acesso (`ScreenAccessLog`) com data
- Transmissão automática via broadcast a cada atualização (`ScreenUpdated`)

### Usuários e Permissões
- Gerenciamento completo de usuários no painel Filament
- Papéis e permissões com **Spatie Permission** integrado ao **Filament Shield**
- Controle de acesso por recurso no painel administrativo

### Dashboard
- Widgets de estatísticas gerais (`StatsOverviewWidget`)
- Listagem dos últimos painéis criados (`LatestPanelsWidget`)
- Listagem das últimas telas criadas (`LatestScreensWidget`)

## Instalação

```bash
git clone <repositório>
cd filament-admin
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure o arquivo `.env` com as credenciais do banco de dados e do Reverb, depois execute:

```bash
php artisan migrate
php artisan db:seed          # opcional — popula dados iniciais
php artisan filament:install --shield  # configura permissões do Filament Shield
npm run build
```

### Executar em desenvolvimento

Em terminais separados:

```bash
php artisan serve            # servidor HTTP
php artisan reverb:start     # servidor WebSocket
npm run dev                  # Vite com hot-reload
```

## Variáveis de Ambiente Relevantes

```env
APP_NAME="Gestão de painéis"
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

## Licença

Este projeto está licenciado sob a [MIT license](https://opensource.org/licenses/MIT).

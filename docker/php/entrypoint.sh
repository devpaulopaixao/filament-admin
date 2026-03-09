#!/bin/sh
set -e

ENV_FILE="/var/www/.env"

# ── 1. Garante que o .env existe ───────────────────────────────────────────────
if [ ! -f "$ENV_FILE" ]; then
    echo "[entrypoint] .env não encontrado — copiando .env.example"
    cp /var/www/.env.example "$ENV_FILE"
fi

# ── 2. Função para sobrescrever variável no .env ────────────────────────────────
# Valores com espaços são automaticamente colocados entre aspas duplas
set_env() {
    local KEY="$1"
    local VALUE="$2"
    # Envolve em aspas se o valor contiver espaço ou caracteres especiais
    case "$VALUE" in
        *\ * | *\$* | *\&* | *\#*) VALUE="\"${VALUE}\"" ;;
    esac
    if grep -q "^${KEY}=" "$ENV_FILE"; then
        sed -i "s|^${KEY}=.*|${KEY}=${VALUE}|" "$ENV_FILE"
    else
        echo "${KEY}=${VALUE}" >> "$ENV_FILE"
    fi
}

# ── 3. Banco de dados ──────────────────────────────────────────────────────────
set_env "DB_HOST"     "${DB_HOST:-mysql}"
set_env "DB_PORT"     "${DB_PORT:-3306}"
set_env "DB_DATABASE" "${DB_DATABASE:-filament_admin}"
set_env "DB_USERNAME" "${DB_USERNAME:-root}"
set_env "DB_PASSWORD" "${DB_PASSWORD:-secret}"

# ── 4. Aplicação ───────────────────────────────────────────────────────────────
set_env "APP_ENV"   "${APP_ENV:-local}"
set_env "APP_DEBUG" "${APP_DEBUG:-true}"
set_env "APP_URL"   "${APP_URL:-http://localhost}"

# ── 5. Sessão — deriva SESSION_DOMAIN do APP_URL se não definido ───────────────
if [ -n "${SESSION_DOMAIN}" ]; then
    set_env "SESSION_DOMAIN" "${SESSION_DOMAIN}"
else
    DERIVED=$(echo "${APP_URL:-http://localhost}" | sed 's|https\?://||' | sed 's|/.*||' | sed 's|:.*||')
    set_env "SESSION_DOMAIN" "${DERIVED}"
fi
set_env "SESSION_SECURE_COOKIE" "${SESSION_SECURE_COOKIE:-false}"
set_env "SESSION_DRIVER"        "${SESSION_DRIVER:-file}"

# ── 6. Redis ───────────────────────────────────────────────────────────────────
set_env "REDIS_HOST" "${REDIS_HOST:-redis}"
set_env "REDIS_PORT" "${REDIS_PORT:-6379}"

# ── 7. Reverb ──────────────────────────────────────────────────────────────────
# REVERB_HOST: nome do serviço Docker (usado pelo PHP server-side para broadcast)
# VITE_REVERB_HOST: endereço que o browser usa (sempre o host público)
set_env "REVERB_HOST"   "${REVERB_HOST:-reverb}"
set_env "REVERB_PORT"   "${REVERB_PORT:-8080}"
set_env "REVERB_SCHEME" "${REVERB_SCHEME:-http}"
# Vite usa o host público (localhost em dev), não o nome do serviço Docker
VITE_HOST=$(echo "${APP_URL:-http://localhost}" | sed 's|https\?://||' | sed 's|/.*||' | sed 's|:.*||')
set_env "VITE_REVERB_HOST"   "${VITE_REVERB_HOST:-${VITE_HOST}}"
set_env "VITE_REVERB_PORT"   "${REVERB_PORT:-8080}"
set_env "VITE_REVERB_SCHEME" "${REVERB_SCHEME:-http}"

# ── 8. Super Admin ─────────────────────────────────────────────────────────────
set_env "SUPER_ADMIN_NAME"     "${SUPER_ADMIN_NAME:-Super Admin}"
set_env "SUPER_ADMIN_EMAIL"    "${SUPER_ADMIN_EMAIL:-admin@admin.com}"
set_env "SUPER_ADMIN_PASSWORD" "${SUPER_ADMIN_PASSWORD:-password}"

# ── 9. Gera APP_KEY se vazio ───────────────────────────────────────────────────
APP_KEY_VALUE=$(grep "^APP_KEY=" "$ENV_FILE" | cut -d= -f2)
if [ -z "$APP_KEY_VALUE" ]; then
    echo "[entrypoint] Gerando APP_KEY..."
    php /var/www/artisan key:generate --force
fi

# ── 10. Permissões de storage ──────────────────────────────────────────────────
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# ── 11. Migrations ─────────────────────────────────────────────────────────────
echo "[entrypoint] Rodando migrations..."
php /var/www/artisan migrate --force --no-interaction

# ── 12. Seeders ────────────────────────────────────────────────────────────────
echo "[entrypoint] Rodando seeders..."
php /var/www/artisan db:seed --force --no-interaction

# ── 13. Cache em produção ──────────────────────────────────────────────────────
if [ "${APP_ENV}" = "production" ]; then
    php /var/www/artisan config:cache
    php /var/www/artisan route:cache
    php /var/www/artisan view:cache
else
    php /var/www/artisan config:clear
fi

echo "[entrypoint] Pronto. Iniciando php-fpm..."
exec "$@"

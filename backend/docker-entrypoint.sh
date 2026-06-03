#!/bin/bash
set -e

WORK_DIR="/var/www/html"
cd "$WORK_DIR"

echo "────────────────────────────────────────────────"
echo "  MCCP — Backend Entrypoint"
echo "────────────────────────────────────────────────"

# ─── 1. Instalar Laravel si no existe ────────────────────────────────────────
if [ ! -f "$WORK_DIR/artisan" ]; then
    echo "[INFO] Laravel no encontrado. Instalando Laravel 12..."
    composer create-project laravel/laravel:^12.0 /tmp/laravel_install \
        --no-interaction \
        --no-progress \
        --prefer-dist

    echo "[INFO] Copiando archivos de Laravel al volumen..."
    rsync -a --ignore-existing /tmp/laravel_install/ "$WORK_DIR/"
    rm -rf /tmp/laravel_install
    echo "[OK] Laravel 12 instalado."
fi

# ─── 2. Instalar dependencias Composer ───────────────────────────────────────
if [ ! -d "$WORK_DIR/vendor" ]; then
    echo "[INFO] Instalando dependencias Composer..."
    composer install \
        --no-interaction \
        --no-progress \
        --optimize-autoloader
    echo "[OK] Dependencias instaladas."
fi

# ─── 3. Configurar .env ───────────────────────────────────────────────────────
if [ ! -f "$WORK_DIR/.env" ]; then
    echo "[INFO] Creando .env desde .env.example..."
    cp "$WORK_DIR/.env.example" "$WORK_DIR/.env"
fi

# Inyectar variables solo si vienen desde el entorno (docker-compose)
# Usar | como delimitador de sed para evitar conflictos con / en valores
sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=pgsql|" "$WORK_DIR/.env"
sed -i "s|^DB_HOST=.*|DB_HOST=${DB_HOST:-postgres}|" "$WORK_DIR/.env"
sed -i "s|^DB_PORT=.*|DB_PORT=${DB_PORT:-5432}|" "$WORK_DIR/.env"
sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE:-mccp_db}|" "$WORK_DIR/.env"
sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME:-mccp_user}|" "$WORK_DIR/.env"
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD:-mccp_password}|" "$WORK_DIR/.env"
sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=database|" "$WORK_DIR/.env"
sed -i "s|^CACHE_STORE=.*|CACHE_STORE=file|" "$WORK_DIR/.env"

# Solo sobreescribir GEMINI/SLACK si la variable de entorno está definida
if [ -n "${GEMINI_ENABLED:-}" ]; then
    sed -i "s|^GEMINI_ENABLED=.*|GEMINI_ENABLED=${GEMINI_ENABLED}|" "$WORK_DIR/.env"
fi
if [ -n "${GEMINI_API_KEY:-}" ]; then
    sed -i "s|^GEMINI_API_KEY=.*|GEMINI_API_KEY=${GEMINI_API_KEY}|" "$WORK_DIR/.env"
fi
if [ -n "${GEMINI_MODEL:-}" ]; then
    sed -i "s|^GEMINI_MODEL=.*|GEMINI_MODEL=${GEMINI_MODEL}|" "$WORK_DIR/.env"
fi
if [ -n "${SLACK_WEBHOOK_URL:-}" ]; then
    sed -i "s|^SLACK_WEBHOOK_URL=.*|SLACK_WEBHOOK_URL=${SLACK_WEBHOOK_URL}|" "$WORK_DIR/.env"
fi

# ─── 4. Generar APP_KEY si no existe ────────────────────────────────────────
if grep -q "^APP_KEY=$" "$WORK_DIR/.env" || ! grep -q "^APP_KEY=" "$WORK_DIR/.env"; then
    echo "[INFO] Generando APP_KEY..."
    php artisan key:generate --force
    echo "[OK] APP_KEY generado."
fi

# ─── 5. Permisos de Storage ───────────────────────────────────────────────────
echo "[INFO] Configurando permisos de storage..."
chown -R www-data:www-data "$WORK_DIR/storage" "$WORK_DIR/bootstrap/cache" 2>/dev/null || true
chmod -R 775 "$WORK_DIR/storage" "$WORK_DIR/bootstrap/cache" 2>/dev/null || true

# ─── 6. Clear config cache ───────────────────────────────────────────────────
php artisan config:clear
php artisan cache:clear

# ─── 7. Ejecutar migraciones ─────────────────────────────────────────────────
echo "[INFO] Ejecutando migraciones..."
php artisan migrate --force
echo "[OK] Migraciones ejecutadas."

echo "────────────────────────────────────────────────"
echo "  [OK] Backend listo. Iniciando PHP-FPM..."
echo "────────────────────────────────────────────────"

exec "$@"

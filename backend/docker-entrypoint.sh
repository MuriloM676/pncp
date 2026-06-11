#!/bin/bash
set -e

# Ajustar permissões da pasta de logs se ela existir
if [ -d "logs" ]; then
    chown -R www-data:www-data logs
fi

# Ajustar permissões do diretório atual
chown -R www-data:www-data /var/www/html

exec "$@"

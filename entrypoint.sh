#!/bin/sh
# entrypoint.sh — Railway startup script for FRS PHP App
# Sets Apache to listen on Railway's dynamic $PORT, then starts Apache

PORT="${PORT:-80}"

# Update Apache ports config
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf

# Update default virtualhost port
sed -i "s/*:80/*:$PORT/" /etc/apache2/sites-enabled/000-default.conf

echo "Starting Apache on port $PORT..."
exec apache2-foreground

#!/bin/bash
set -e

# Configure Apache to listen on Render's PORT
# Default to 80 if PORT is not set
PORT=${PORT:-80}

sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf

# Start Apache
apache2-foreground

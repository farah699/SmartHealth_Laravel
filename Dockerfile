FROM php:8.2-fpm

# Variables d'environnement
ENV DEBIAN_FRONTEND=noninteractive
ENV NODE_VERSION=18

# Arguments de build
ARG user=smarthealth
ARG uid=1000

# Installation des dépendances système (combiné avec nginx et supervisor)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Installation de Node.js 18
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Installation de l'extension Redis
RUN pecl install redis && docker-php-ext-enable redis

# Configuration PHP pour production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Création de l'utilisateur système
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Configuration du répertoire de travail
WORKDIR /var/www

# Copie des fichiers de dépendances
COPY composer.json composer.lock package.json package-lock.json webpack.mix.js ./

# Installation des dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Installation des dépendances Node.js
RUN npm ci --only=production

# Copie du code source
COPY . .

# Créer les dossiers manquants
RUN mkdir -p resources/assets/images \
    && mkdir -p resources/assets/scss \
    && mkdir -p resources/assets/js \
    && mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && mkdir -p /var/www/storage/app/public/audio \
    && mkdir -p /var/www/storage/app/public/avatars \
    && mkdir -p /var/www/storage/app/public/images

# Créer des fichiers SCSS vides si nécessaire
RUN touch resources/assets/scss/app.scss || true
RUN touch resources/assets/scss/bootstrap.scss || true
RUN touch resources/assets/scss/icons.scss || true

# Build des assets pour production
RUN npm run production

# Exécuter les scripts post-install
RUN composer run-script post-autoload-dump --no-interaction || true

# Nettoyage des dépendances de développement Node.js
RUN npm prune --production \
    && rm -rf node_modules/.cache

# Configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Configuration Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Configuration des permissions
RUN chown -R $user:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/public/storage || true

# Configuration de santé
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost || exit 1

# Switch vers l'utilisateur créé
USER $user

# Exposition du port
EXPOSE 80

# Commande de démarrage
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
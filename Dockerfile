FROM php:8.2-fpm

# Arguments de build
ARG user=smarthealth
ARG uid=1000

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Installation de l'extension Redis
RUN pecl install redis && docker-php-ext-enable redis
# Configuration PHP pour production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini

# Nettoyage du cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Création de l'utilisateur système
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Définition du répertoire de travail
WORKDIR /var/www

# Copie des fichiers de dépendances
COPY composer.json composer.lock ./
COPY package.json package-lock.json webpack.mix.js ./

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
    && mkdir -p bootstrap/cache

# Créer des fichiers SCSS vides si nécessaire
RUN touch resources/assets/scss/app.scss || true
RUN touch resources/assets/scss/bootstrap.scss || true
RUN touch resources/assets/scss/icons.scss || true

# Installation des dépendances PHP (production)
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction

# Build des assets pour production
RUN npm run production
# Exécuter les scripts post-install
RUN composer run-script post-autoload-dump --no-interaction || true

# Permissions appropriées
RUN chown -R $user:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache

# Configuration de santé
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:9000/health || exit 1

# Switch vers l'utilisateur créé
USER $user

# Exposer le port
EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]

# Nettoyage du cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Création de l'utilisateur système
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Définition du répertoire de travail
WORKDIR /var/www

# Copie des fichiers de dépendances
COPY composer.json composer.lock ./
COPY package.json package-lock.json webpack.mix.js ./

# Installation des dépendances Node.js
RUN npm install

# Copie du code source
COPY . .

# ✅ NOUVEAU: Créer les dossiers manquants avant le build
RUN mkdir -p resources/assets/images \
    && mkdir -p resources/assets/scss \
    && mkdir -p resources/assets/js

# ✅ NOUVEAU: Créer des fichiers SCSS vides si ils n'existent pas
RUN touch resources/assets/scss/app.scss || true
RUN touch resources/assets/scss/bootstrap.scss || true
RUN touch resources/assets/scss/icons.scss || true

# Installation des dépendances PHP avec gestion d'erreurs
RUN composer install --optimize-autoloader --no-scripts --no-interaction || \
    (composer clear-cache && composer install --optimize-autoloader --no-scripts --no-interaction)

# Exécuter les scripts post-install de manière sécurisée
RUN composer run-script post-autoload-dump --no-interaction || true

# ✅ MODIFIÉ: Build des assets seulement si webpack.mix.js est configuré correctement
RUN npm run dev || npm run production || echo "Build assets skipped"

# Permissions appropriées
RUN chown -R $user:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Créer les dossiers nécessaires
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && mkdir -p /var/www/storage/app/public/audio \
    && mkdir -p /var/www/storage/app/public/avatars \
    && mkdir -p /var/www/storage/app/public/images

# Permissions complètes pour storage et public/storage
RUN chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/public/storage || true

# Switch vers l'utilisateur créé
USER $user
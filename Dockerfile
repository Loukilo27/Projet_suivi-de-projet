# =====================================================================
#  Recette de l'image de l'application (PHP + serveur web FrankenPHP)
#  Trois étapes : "base" (commune), "dev" (ton PC), "prod" (le serveur)
# =====================================================================

# ---------- Étape commune ----------
FROM dunglas/frankenphp:1-php8.4 AS base

# Outils utilisés par Composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP : pdo_mysql = pilote pour parler à MySQL
RUN install-php-extensions pdo_mysql intl zip opcache

# Composer (gestionnaire de dépendances PHP)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Dossier de l'application dans le conteneur
WORKDIR /app


# ---------- Développement (sur ton PC) ----------
# Pas de COPY du code : ton dossier local est "branché" sur /app
# par compose.override.yaml. Tu modifies un fichier -> le conteneur le voit.
FROM base AS dev
ENV APP_ENV=dev


# ---------- Production (sur le serveur) ----------
# Ici le code est copié DANS l'image, dépendances installées sans les outils de dev.
FROM base AS prod
ENV APP_ENV=prod
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && php bin/console asset-map:compile

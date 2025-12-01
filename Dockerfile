FROM webdevops/php-nginx:8.2

WORKDIR /app

COPY . /app

RUN composer install --no-dev --optimize-autoloader

ENV APP_ENV=production
ENV APP_DEBUG=false

RUN php artisan key:generate

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=8080

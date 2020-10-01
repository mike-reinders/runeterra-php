FROM php:7.4-cli-alpine3.12

RUN apk add --no-cache \
        git \
        inotify-tools \
    && php -r "copy('https://getcomposer.org/download/1.10.13/composer.phar', '/usr/local/bin/composer'); \
        if (hash_file('sha256', '/usr/local/bin/composer') !== '5ca7445cfd48dd27c5a84aa005a47b4d9fd91132313830609875df3a6973708f') { \
            print 'composer is corrupted'.PHP_EOL; \
            exit(1); \
        }" \
    && chmod +x /usr/local/bin/composer

COPY ./entrypoint.sh /

WORKDIR /repository
ENTRYPOINT ["/entrypoint.sh"]
CMD ["test"]
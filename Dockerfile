FROM php:8.3-cli-alpine as symfony_app

RUN apk update
RUN apk add --no-cache bash openrc

## AMQP installaztion
RUN apk --no-cache add build-base \
        autoconf \
        rabbitmq-c-dev
RUN pecl install amqp
RUN docker-php-ext-enable amqp
## AMQP installaztion

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

## OpenRC initialization
RUN openrc
RUN touch /run/openrc/softlevel
RUN sed -i 's/VSERVER/DOCKER/Ig' /lib/rc/sh/init.sh

# configured /etc/rc.conf for docker
RUN sed -i '/getty/d' /etc/inittab

# included the volume rc complains about
VOLUME ["/sys/fs/cgroup"]

COPY docker/openrc/symfony-worker-template /etc/init.d
## OpenRC initialization

COPY . /app
WORKDIR /app

EXPOSE 3910

# Starting up the application
ENV MESSENGER_WORKER_COUNT = "null"

COPY docker/bootstrap/bootstrap.sh /usr/local/bin/app-bootstrap
RUN chmod +x /usr/local/bin/app-bootstrap

CMD ["app-bootstrap"]
# Starting up the application

FROM ubuntu:20.04

COPY --from=composer:1.10.10 /usr/bin/composer /usr/bin/composer
COPY docker-entrypoint /usr/local/sbin/docker-entrypoint

RUN apt update
RUN DEBIAN_FRONTEND="noninteractive" apt install -y software-properties-common sudo git tzdata
RUN rm /etc/localtime && ln -s /usr/share/zoneinfo/Europe/Moscow /etc/localtime
RUN LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
RUN apt update
RUN apt install -y php7.4 php7.4-cli php7.4-curl php7.4-fpm php7.4-pgsql php7.4-xml php-xdebug
RUN sed -i 's/listen = \/run\/php\/php7.4-fpm.sock/listen = 0.0.0.0:9000/' /etc/php/7.4/fpm/pool.d/www.conf
RUN sed -i 's/user = www-data/user = developer/' /etc/php/7.4/fpm/pool.d/www.conf
RUN sed -i 's/group = www-data/group = developer/' /etc/php/7.4/fpm/pool.d/www.conf
RUN sed -i 's/owner = www-data/owner = developer/' /etc/php/7.4/fpm/pool.d/www.conf
RUN sed -i 's/max_execution_time = 30/max_execution_time = 3600/' /etc/php/7.4/fpm/php.ini
RUN sed -i 's/memory_limit = 128M/memory_limit = 1G/' /etc/php/7.4/fpm/php.ini
RUN mkdir /run/php
RUN chown www-data. /run/php
RUN useradd -m developer
RUN mkdir /var/www
RUN chown developer. /var/www
RUN echo 'developer ALL=(ALL) NOPASSWD: ALL' > /etc/sudoers
RUN chmod 0755 /usr/local/sbin/docker-entrypoint
RUN echo "xdebug.remote_enable=1" >> /etc/php/7.4/mods-available/xdebug.ini
RUN echo "xdebug.max_nesting_level=512" >> /etc/php/7.4/mods-available/xdebug.ini
RUN echo "xdebug.remote_autostart=1" >> /etc/php/7.4/mods-available/xdebug.ini
RUN echo "xdebug.remote_host=" >> /etc/php/7.4/mods-available/xdebug.ini
RUN echo "xdebug.remote_port=" >> /etc/php/7.4/mods-available/xdebug.ini
RUN echo "xdebug.idekey=" >> /etc/php/7.4/mods-available/xdebug.ini

ENTRYPOINT ["/usr/local/sbin/docker-entrypoint"]

CMD ["/usr/sbin/php-fpm7.4", "--nodaemonize", "--fpm-config", "/etc/php/7.4/fpm/php-fpm.conf"]

USER developer
WORKDIR /var/www

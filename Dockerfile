#FROM php:7.4-cli as build
#RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql pgsql
#
#RUN apt-get update && \
#	apt-get install -y --no-install-recommends \
#		git procps
#
#RUN git clone https://github.com/krakjoe/parallel
#
#RUN phpize
#
#RUN ./configure
#
#RUN make install
#
#RUN EXTENSION_DIR=`php-config --extension-dir 2>/dev/null` && \
#	cp "$EXTENSION_DIR/parallel.so" /parallel.so
#
#RUN sha256sum /parallel.so
#
#FROM php:7.4-cli
#
#COPY --from=build /parallel.so /parallel.so
#
#RUN EXTENSION_DIR=`php-config --extension-dir 2>/dev/null` && \
#	mv /parallel.so "$EXTENSION_DIR/parallel.so" && \
#	docker-php-ext-enable parallel
#
#COPY . /usr/src/myapp
#WORKDIR /usr/src/myapp
#CMD [ "php", "./index.php" ]

FROM php:7.3-zts-stretch AS build

RUN apt-get update && \
	apt-get install -y --no-install-recommends \
		git procps


RUN git clone https://github.com/krakjoe/parallel

WORKDIR /parallel

RUN phpize

RUN ./configure

RUN make install

RUN EXTENSION_DIR=`php-config --extension-dir 2>/dev/null` && \
	cp "$EXTENSION_DIR/parallel.so" /parallel.so

RUN sha256sum /parallel.so


FROM php:7.3-zts-stretch

RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql pgsql

COPY --from=build /parallel.so /parallel.so

RUN EXTENSION_DIR=`php-config --extension-dir 2>/dev/null` && \
	mv /parallel.so "$EXTENSION_DIR/parallel.so" && \
	docker-php-ext-enable parallel

COPY . /usr/src/myapp
WORKDIR /usr/src/myapp
CMD [ "php", "./index.php" ]
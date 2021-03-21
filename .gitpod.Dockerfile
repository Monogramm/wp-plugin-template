FROM gitpod/workspace-mysql:latest

# Install custom tools, runtimes, etc.
# For example "bastet", a command-line tetris clone:
# RUN brew install bastet
#
# More information: https://www.gitpod.io/docs/config-docker/

#ENV WORDPRESS_DB_NAME=wordpressdb \
#	WORDPRESS_DB_USER=wordpress \
#	WORDPRESS_DB_PWD=wordpress \
#	WORDPRESS_DB_HOST=localhost:3306 \
#	TMPDIR=.gitpod/tmp \
#	WP_TESTS_DIR=.gitpod/tmp/wordpress-tests-lib \
#	WP_CORE_DIR=.gitpod/www \
#	WORDPRESS_ADMIN_LOGIN=root \
#	WORDPRESS_ADMIN_PWD=wordpress \
#	WP_PLUGIN=wp-plugin-template \
#	PROJECT_DIR=.gitpod/www/wp-content/plugins/${WP_PLUGIN} \
#	NGINX_DOCROOT_IN_REPO=.gitpod/www

RUN set -ex; \
    sudo mkdir -p /var/log/nginx; \
    sudo chown gitpod:gitpod /var/log/nginx

COPY .gitpod/nginx.conf /etc/nginx/nginx.conf

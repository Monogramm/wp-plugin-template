#!/bin/bash
set -e

. .env

###########################################################
# Functions

log() {
    echo "[${0}] [$(date +%Y-%m-%dT%H:%M:%S)] ${1}"
}

build() {
    # Node install and CSS / JS minifier
    npm install
    npm run start

    # Docker container(s) build
    docker-compose build

    # PHP Composer install and code fixer
    #composer install
    ./vendor/bin/phpcbf
}

prepare_release() {
    NEW_VERSION=${1}
    if [ -z "${NEW_VERSION}" ] ; then
        log 'Missing release version!'
        return 1;
    fi

    log 'TODO Updating app version...'
    sed -i \
        -e "s|\"version\": \".*\"|\"version\": \"${NEW_VERSION}\"|g" \
        ./.gitmoji-changelogrc

    log 'Updating plugin version...'
    sed -i \
        -e "s|version = '.*'|version = '${NEW_VERSION}'|g" \
        ./includes/"class-${WP_PLUGIN}.php"

    sed -i \
        -e "s|Version: .*|Version: ${NEW_VERSION}|g" \
        -e "s| __FILE__, '.*' | __FILE__, '${NEW_VERSION}' |g" \
        ./"${WP_PLUGIN}.php"

    # Generate Changelog for version
    log "Generating Changelog for version '${NEW_VERSION}'..."
    npm install
    npm run gitmoji-changelog

    # TODO Add and commit to git with message `:bookmark: Release X.Y.Z`

    ./bin/generate-plugin-zip.sh "${WP_PLUGIN}"
}

usage() {
    echo "usage: ./manage.sh COMMAND [ARGUMENTS]

    Commands:
        start               Start dev / test env (docker)
        stop                Stop dev / test env
        logs                Follow logs of dev / test env
        reset               Reset all data of dev / test env
        i18n                Update i18n locales
        build               Build and clean source code
        prepare-release     Prepare app release
    "
}

###########################################################
# Runtime

case "${1}" in
    # DEV env
    start) docker-compose up -d ${@:2};;
    stop) docker-compose down ${@:2};;
    logs) docker-compose logs -f ${@:2};;
    reset) docker-compose down
    rm -rf /srv/wordpress;;
    i18n) npm install
    npm run i18n;;
    build) build;;
    prepare-release) build
    prepare_release ${@:2};;
    # Help
    *) usage;;
esac

exit 0

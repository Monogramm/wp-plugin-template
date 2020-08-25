#!/usr/bin/env bash

zip -FSrq "${WP_PLUGIN:-${1}}" . -x "vendor/*" ".*" "*.md" "*.sh" "docker-compose*" "Dockerfile"


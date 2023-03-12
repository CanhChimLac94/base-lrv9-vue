#!/usr/bin/env bash

# sudo chmod -R 777 .

dir="./vendor/bin/sail"
if [ "$1" = "up" -o "$1" = "dev" -o "$1" = "run" ]; then
  if [ -n "$2" ]; then
    "${dir}" "up" "-d"
  else
    "${dir}" "up"
  fi
  if [ "$2" = "install-vue" -o "$2" = "i-vue" ]; then
    "${dir}" "composer" "require" "laravel/jetstream"
    "${dir}" "artisan" "jetstream:install" "inertia"
  elif [ "$2" = "db-rollback" ]; then
    "${dir}" "artisan" "migrate:rollback" "--force"
  elif [ "$2" = "migrate" ]; then
    "${dir}" "artisan" "migrate"
  elif [ "$2" = "db-update" ]; then
    "${dir}" "artisan" "migrate:rollback" "--force"
    "${dir}" "artisan" "migrate"
  elif [ "$2" = "dev" ]; then
    "${dir}" "npm" "install"
    "${dir}" "npm" "run" "dev"
  fi
else
  "${dir}" "$@"
fi

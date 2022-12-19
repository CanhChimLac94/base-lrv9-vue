#!/usr/bin/env bash

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
    "${dir}" "down"
  elif [ "$2" = "update-db" ]; then
    "${dir}" "artisan" "migrate:rollback" "--force"
    "${dir}" "artisan" "migrate"
    # "${dir}" "down"
  elif [ "$2" = "dev" ]; then
    "${dir}" "npm" "install"
    "${dir}" "npm" "run" "dev"
  elif [ "$2" = "migrate" ]; then
    "${dir}" "artisan" "migrate"
  fi
else
  "${dir}" "$@"
fi

#!/bin/sh

REPOSITORY_DIRECTORY="/repository"

set -e
cd "${REPOSITORY_DIRECTORY}"

trap 'CAUGHT_CTRL_C=true; printf "\nCaught CTRL-C, exiting..\n";' INT
CAUGHT_CTRL_C=false

if [ "${1}" = "test" ]; then
  ## Clone repository
  if [ ! -d "${REPOSITORY_DIRECTORY}" ] || [ -z "$(ls -AL -- "${REPOSITORY_DIRECTORY}")" ]; then
    git clone https://github.com/mike-reinders/runeterra-php.git "${REPOSITORY_DIRECTORY}"
  fi

  ## Install composer packages
  if [ ! -d "${REPOSITORY_DIRECTORY}/vendor" ]; then
    composer install --no-interaction "--working-dir=${REPOSITORY_DIRECTORY}"
  fi

  ## Run Test
  vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests

  ## Watch and run Test again
  if [ "${2}" = "watch" ]; then
    while [ "${CAUGHT_CTRL_C}" = false ]; do
      printf "\nWatching repository for changes..\n"
      inotifywait -q -e modify,create,delete -r "${REPOSITORY_DIRECTORY}"

      if [ "${CAUGHT_CTRL_C}" = false ]; then
        if vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests && [ "${?}" -ne 130 ]; then
          echo "PHP Test returned non-zero exit status ${?}"
        fi
      fi
    done
  fi
else
  if [ -z "${*}" ]; then
    echo "no arguments specified"
  else
    exec "${@}"
  fi
fi

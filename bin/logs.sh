#!/bin/sh
set -e
docker-compose -f docker-compose-$1.yml -p $1.infinity.api logs --follow $2
exec $@

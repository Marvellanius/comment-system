#!/bin/bash
PROJECT=comment-system
docker-compose -f docker-compose.yml up -d
docker-compose exec -w /code phpfpm composer install
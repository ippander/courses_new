#!/bin/bash

docker run --name aui-courses-new -d -p 80:80 -p 3306:3306 -v `pwd`/code:/app tutum/lamp
sleep 5
# docker exec -i aui-courses mysql < localhost.sql

docker exec -d aui-courses-new bash -c "echo 'display_errors = On' >> /etc/php5/apache2/php.ini"
docker exec -d aui-courses-new bash -c "echo 'error_reporting = E_ALL' >> /etc/php5/apache2/php.ini"

docker exec -i aui-courses-new mysql -e "create user 'course_admin'@'%' identified by 'aurajoki'"
docker exec -i aui-courses-new mysql -e "GRANT ALL PRIVILEGES ON aurajoen_courses . * TO 'course_admin'@'%';"
docker exec -i aui-courses-new mysql -e "GRANT ALL PRIVILEGES ON aurajoen_courses_new . * TO 'course_admin'@'%';"

docker exec -i aui-courses-new mysql < aurajoen_courses.sql
docker exec -i aui-courses-new mysql < init.sql

sleep 5

./curling.sh

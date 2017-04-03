#!/bin/bash

curl  -X POST -i http://localhost/api/admin/place \
	--data '{ "name": "Nättinummen uimahalli" }' \
	-H 'Content-Type: application/json'

curl  -X POST -i http://localhost/api/admin/season \
	--data '{ "name": "16/17", "startYear": 2016, "endYear": 2017 }' \
	-H 'Content-Type: application/json'

curl  -X POST -i http://localhost/api/admin/period \
	--data '{ "seasonId": "1", "quarter": "Q2" }' \
	-H 'Content-Type: application/json'

curl  -X POST -i http://localhost/api/admin/course \
	--data '{ "name": "Tekniikkauimakoulu yli 8v", "description": "deskripsuuni" }' \
	-H 'Content-Type: application/json'




# first_person() {
# 	cat <<EOF
# {
# 	"account_id": "1",
# 	"first_name": "Eka Etunimi",
# 	"last_name": "Eka sukunimi",
# 	"birthday": "2010-10-12",
# 	"street_address": "Eka katusoite",
# 	"zipcode": "34567",
# 	"city": "Tampesteri"
# }
# EOF
# }

# second_person() {
# 	cat <<EOF
# {
# 	"account_id": "1",
# 	"first_name": "Toka Etunimi",
# 	"last_name": "Eka sukunimi",
# 	"birthday": "2012-10-12",
# 	"street_address": "Eka katusoite",
# 	"zipcode": "34567",
# 	"city": "Tampesteri"
# }
# EOF
# }

# curl -X POST -i http://localhost/api/add_person.php \
# 	--data "$(first_person)" -H 'Content-Type: application/json'

# curl -X POST -i http://localhost/api/add_person.php \
# 	--data "$(second_person)" -H 'Content-Type: application/json'

# curl -X POST -i http://localhost/api/add_sibling.php \
# 	--data '{ "person_id": "1", "sibling_id": "2" }' -H 'Content-Type: application/json'
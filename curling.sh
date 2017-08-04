#!/bin/bash

# curl  -X POST -i http://localhost/api/account \
# 	--data '{ "email": "foobar@baz.com", "password": "asdf"  }' \
# 	-H 'Content-Type: application/json'

# first_person() {
# 	cat <<EOF
# {
# 	"account_id": "1",
# 	"email": "foobar@baz.com",
# 	"password": "asdf",
# 	"first_name": "Eka Etunimi",
# 	"last_name": "Eka sukunimi",
# 	"birthday": "2010-10-12",
# 	"street_address": "Eka katusoite",
# 	"zipcode": "34567",
# 	"post_office": "Tampesteri"
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

first_swimmer() {
	cat <<EOF
{
	"accountId": "1",
	"firstName": "Daavid",
	"lastName": "Delffari",
	"birthday": "2010-10-12"
}
EOF
}

# curl -X POST -i http://localhost/api/account \
# 	--data "$(first_person)" -H 'Content-Type: application/json'

# curl -X POST -i http://localhost/api/person/1 \
# 	--data "$(first_swimmer)" -H 'Content-Type: application/json'

curl -X POST -i http://localhost/api/login \
	--data '{ "username": "foobar2@baz.com", "password": "asdfasdf"}'

# curl -X POST -i http://localhost/api/add_person.php \
# 	--data "$(second_person)" -H 'Content-Type: application/json'

# curl -X POST -i http://localhost/api/add_sibling.php \
# 	--data '{ "person_id": "1", "sibling_id": "2" }' -H 'Content-Type: application/json'
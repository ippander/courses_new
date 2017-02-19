#!/bin/bash

# curl  -X POST -i http://localhost/api/add_account.php \
# 	--data '{ "email": "foobar@baz.com", "password": "asdf"  }' \
# 	-H 'Content-Type: application/json'

first_person() {
	cat <<EOF
{
	"account_id": "2",
	"first_name": "Eka Etunimi",
	"last_name": "Eka sukunimi",
	"birthday": "2010-10-12",
	"street_address": "Eka katusoite",
	"zipcode": "34567",
	"city": "Tampesteri"
}
EOF
}

curl -X POST -i http://localhost/api/add_person.php \
	--data "$(first_person)" -H 'Content-Type: application/json'
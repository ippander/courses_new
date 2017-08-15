<?php

error_reporting( E_ALL );
ini_set('display_errors', 1);

require("queries.php");

sendInvoicesFrom("2016-12-18");
// sendInvoiceForRegistration(9678);
?>
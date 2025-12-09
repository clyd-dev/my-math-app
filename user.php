<?php

$hash_password = password_hash("admin123", PASSWORD_DEFAULT);

echo $hash_password;

?>
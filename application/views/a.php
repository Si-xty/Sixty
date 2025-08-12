<?php
$password = '123';
$hash = password_hash($password, PASSWORD_BCRYPT);

// Descomentar echo cuando necesite ver el hash generado

// echo "Hash generado: " . $hash;
?>
<?php
require 'include.php';

$auth->logout();
header('Location: /');
die();

<?php
require '../include.php';

$stmt = $db->prepare("
DELETE FROM stats_skill
WHERE skill_id = -1");
$stmt->execute();

die('Skills have been cleaned up.');
<?php
ob_start();
require 'include.php';
ob_end_clean();

$focus_id = null;
$zoom = 1;
if (isset($_GET['system_id'])) {
  $system_id = $_GET['system_id'];
} elseif (isset($_GET['planet_id'])) {
  $stmt = $db->prepare("SELECT * FROM planet WHERE id = :id");
  $stmt->bindValue(':id', $_GET['planet_id']);
  $stmt->execute();
  $row = $stmt->fetch();
  $system_id = $row['system_id'];
  $focus_id = $_GET['planet_id'];
  $zoom = 3;
}

$stmt = $db->prepare("SELECT * FROM system WHERE id = :id");
$stmt->bindValue(':id', $system_id);
$stmt->execute();
$system = $stmt->fetch();

$stmt = $db->prepare("SELECT * FROM planet WHERE system_id = :id");
$stmt->bindValue(':id', $system_id);
$stmt->execute();
$planets = array();
while ($row = $stmt->fetch()) {
  $planets[] = $row;
}

function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array('r' => $r, 'g' => $g, 'b' => $b);
   return $rgb;
}

$orbit = new Orbit($system['name']);
foreach ($planets as $planet) {
  $orbit->addPlanet(
    $planet['id'],
    $planet['orbit_distance'],
    $planet['orbit_position'],
    $planet['orbit_parent_planet_id'],
    $planet['name'],
    hex2rgb($planet['color']),
    $planet['size']);
}
$orbit->focus($focus_id);
$orbit->zoom($zoom);

header('Content-Type: image/gif');
header('Cache-Control: public, max-age='.(6 * 60 * 60));
header('Expires: '.date("l, d M y H:i:s T", time() + 6 * 60 * 60));
header('Date: '.date("l, d M y H:i:s T", time()));
header('Pragma: cache');

if (!isset($_GET['time'])) {
  $orbit->renderAnimated();
} else {
  $orbit->render((float)$_GET['time']);
}


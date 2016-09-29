<?php

require_once 'GIFEncoder.class.php';

final class OrbitGraphNode {
  private $id;
  private $children;
  private $distance;
  private $position;
  private $name;
  private $color;
  private $size;
  
  public function __construct($id, $distance, $position, $name, $color, $size) {
    $this->id = $id;
    $this->children = array();
    $this->distance = $distance;
    $this->position = $position;
    $this->name = $name;
    $this->color = $color;
    $this->size = $size;
  }
  
  public function getID() {
    return $this->id;
  }
  
  public function getName() {
    return $this->name;
  }
  
  public function getColor() {
    return $this->color;
  }
  
  public function getSize() {
    return $this->size;
  }
  
  public function getDistance() {
    return $this->distance;
  }
  
  public function getPosition() {
    return $this->position;
  }
  
  public function getChildren() {
    return $this->children;
  }
  
  public function addChild(OrbitGraphNode $node) {
    $this->children[] = $node;
  }
  
  public static function walkGraphToFindNode(OrbitGraphNode $root, $id) {
    if ($root->id === $id)
      return $root;
    foreach ($root->children as $child) {
      $walk = self::walkGraphToFindNode($child, $id);
      if ($walk !== null)
        return $walk;
    }
    return null;
  }
  
  public static function findMaximumDistance(OrbitGraphNode $root, $zoom) {
    $distance = 0;
    $active = null;
    foreach ($root->children as $child) {
      if ($child->getDistance() * $zoom > $distance) {
        $distance = $child->getDistance() * $zoom;
        $active = $child;
      }
    }
    if ($active !== null) {
      $distance += self::findMaximumDistance($active, $zoom);
    }
    return $distance;
  }
}

final class Orbit {
  
  private $root;
  private $focus_id;
  private $zoom_amount;
  private $pending;
  private $legend;
  
  public function __construct($name) {
    $this->root = new OrbitGraphNode(null, 0, 0, $name, array('r' => 222, 'g' => 222, 'b' => 0), 20);
    $this->zoom = 1;
    $this->focus_id = null;
    $this->pending = array();
    $this->legend = array();
  }
  
  public function allocateColors($image) {
    // Animated GIF treats 0, 0, 0 as transparent, so black is 1, 1, 1.
    return array(
      'white' => imagecolorallocate($image, 255, 255, 255),
      'red' => imagecolorallocate($image, 255, 0, 0),
      'yellow' => imagecolorallocate($image, 255, 255, 0),
      'gold' => imagecolorallocate($image, 127, 127, 0),
      'blue' => imagecolorallocate($image, 0, 0, 255),
      'green' => imagecolorallocate($image, 0, 255, 0),
      'black' => imagecolorallocate($image, 1, 1, 1));
  }
  
  public function addPlanet($id, $distance, $position, $parent_id, $name, $color, $size) {
    $parent = OrbitGraphNode::walkGraphToFindNode($this->root, $parent_id);
    if ($parent === null) {
      $this->pending[] = array('parent' => $parent_id, 'node' => new OrbitGraphNode($id, $distance, $position, $name, $color, $size), 'resolved' => false);
    } else {
      $node = new OrbitGraphNode($id, $distance, $position, $name, $color, $size);
      $parent->addChild($node);
      $this->resolvePending($id, $node);
    }
  }
  
  public function resolvePending($parent_id, $parent_node) {
    foreach ($this->pending as $p) {
      if ($p['parent'] === $parent_id && !$p['resolved']) {
        $parent_node->addChild($p['node']);
        $p['resolved'] = true;
        $this->resolvePending($p['node']->getID(), $p['node']);
      }
    }
  }
  
  public function focus($planet_id) {
    $this->focus_id = $planet_id;
  }
  
  public function zoom($zoom) {
    $this->zoom_amount = $zoom;
  }
  
  public function get($time, $show_legend = true) {
    if ($this->focus_id === null) {
      $focus = $this->root;
    } else {
      $focus = OrbitGraphNode::walkGraphToFindNode($this->root, $this->focus_id);
    }
    
    $size = max(OrbitGraphNode::findMaximumDistance($focus, $this->zoom_amount) * 2, 100) + 10;
    $image = imagecreate($size + 120, $size);
    imagecolorallocate($image, 255, 255, 255);
    $colors = $this->allocateColors($image);
    
    $rawcolor = $focus->getColor();
    $color = imagecolorallocate($image, $rawcolor['r'], $rawcolor['g'], $rawcolor['b']);
    imagefilledellipse(
      $image,
      $size / 2,
      $size / 2,
      $focus->getSize() * $this->zoom_amount,
      $focus->getSize() * $this->zoom_amount,
      $color);
    
    $this->legend = array();
    $this->legend[] = array('color' => $color, 'name' => $focus->getName());
    $this->walkAndRender($image, $colors, $size / 2, $focus, $time, $size / 2, $size / 2);
    
    if ($show_legend) {
      $i = 0;
      foreach ($this->legend as $entry) {
        imagestring($image, 1, $size, $i * 10 + 10, $entry['name'], $entry['color']);
        $i++;
      }
    }
    
    return $image;
  }
  
  public function render($time = 0) {
    $image = $this->get($time);
    imagegif($image);
    imagedestroy($image);
  }
  
  public function renderAnimated() {
    $increment = 0.0002;
    $frames = array();
    $framed = array();
    for ($i = 0; $i < 1; $i += $increment) {
      $image = $this->get($i);
      ob_start();
      imagegif($image);
      imagedestroy($image);
      $frames[] = ob_get_clean();
      $framed[] = 1;
    }
    
    $gif = new GIFEncoder($frames,$framed,0,2,0,0,0,'bin');
    echo $gif->GetAnimation();
  }
  
  public function walkAndRender($image, $colors, $max, $node, $time, $px, $py) {
    foreach ($node->getChildren() as $child) {
      $pos = $child->getPosition() + $time;
      $speed = (int)(($max - $child->getDistance()));
      while ($pos > 1) $pos -= 1;
      while ($pos < 0) $pos += 1;
      $x = $px + sin($pos * 3.14 * 2 * $speed) * $child->getDistance() * $this->zoom_amount;
      $y = $py - cos($pos * 3.14 * 2 * $speed) * $child->getDistance() * $this->zoom_amount;
      $rawcolor = $child->getColor();
      $color = imagecolorallocate($image, $rawcolor['r'], $rawcolor['g'], $rawcolor['b']);
      imageellipse($image, $px, $py, $child->getDistance() * $this->zoom_amount * 2, $child->getDistance() * $this->zoom_amount * 2, $colors['black']);
      $d = max($child->getSize() * $this->zoom_amount, 5);
      imagefilledellipse($image, $x, $y, $d, $d, $color);
      $this->walkAndRender($image, $colors, $max, $child, $time, $x, $y);
      $time = -$time;
      $this->legend[] = array('color' => $color, 'name' => $child->getName());
    }
  }
}

function render_orbit_map($name, $id) {
  $diff = time() - strtotime("00:00");
  $result = (float)$diff / (24 * 60 * 60);
?>
<img id="current" src="/orbit.php?<?php echo $name; ?>=<?php echo $id; ?>&amp;time=<?php echo $result; ?>" /><br />
<a href="/orbit.php?<?php echo $name; ?>=<?php echo $id; ?>">Projected Orbit</a>
<?php
}

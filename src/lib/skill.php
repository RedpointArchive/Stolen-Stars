<?php

class Skill extends DAO {
  protected $parent_id;
  protected $name;
  
  public function getRelationships() {
    return array(
      'parent_id' => 'skill') + parent::getRelationships();
  }
}

function list_skills($db, $stats_id) {
  $stmt = $db->prepare("
SELECT
  skill.id AS id,
  skill.name AS name,
  skill.parent_id AS parent_id,
  stats_skill.value AS value
FROM stats_skill
LEFT JOIN skill
  ON skill.id = stats_skill.skill_id
WHERE stats_skill.stats_id = :id
ORDER BY skill.name ASC");
  $stmt->bindValue(':id', $stats_id);
  $stmt->execute();
  $skills = array();
  while ($row = $stmt->fetch()) {
    $skills[] = $row;
  }
  
  // Top-level
  $output = false;
  foreach ($skills as $skill) {
    if ($skill["parent_id"] !== null) {
      continue;
    }
    if (!$output) {
      echo '<ul>';
      $output = true;
    }
    echo '<li>'.$skill["name"].': 1d'.$skill['value'];
    
    // Specialisations
    $output2 = false;
    foreach ($skills as $specialisation) {
      if ($specialisation["parent_id"] !== $skill['id']) {
        continue;
      }
      if (!$output2) {
        echo '<ul>';
        $output2 = true;
      }
      echo '<li>'.$specialisation["name"].': 1d'.$specialisation['value'].'</li>';
    }
    if ($output2) {
      echo '</ul>';
    }
    
    echo '</li>';
  }
  
  if ($output) {
    echo '</ul>';
  } else {
    echo 'There are no skills listed.';
  }
}

?>
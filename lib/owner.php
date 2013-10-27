<?php

function getOwnershipDetails(ManagedDAO $object) {
  $owner = $object->getOwner();
  $gm = $object->getGM();
  
  $styles = '';
  $styles .= 'display: inline-block;';
  $styles .= 'border: 1px dashed grey;';
  $styles .= 'font-size: 12px;';
  $styles .= 'height: 32px;';
  
  $styles_b = 'width: 32px; height: 32px;';
  
  $styles_c = '-moz-transform:rotate(-90deg);';
  $styles_c .= '-webkit-transform:rotate(-90deg);';
  $styles_c .= '-o-transform:rotate(-90deg);';
  $styles_c .= '-ms-transform:rotate(-90deg);';
  $styles_c .= 'transform:rotate(-90deg);';
  $styles_c .= 'display: inline-block;';
  $styles_c .= 'width: 16px;';
  $styles_c .= 'font-size: 10px;';
  $styles_c .= 'font-family: monospace;';
  $styles_c .= 'position: relative;';
  
  $styles_d = $styles_c;
  $styles_d .= 'top: -13px;';
  
  $styles_c .= 'top: -6px;';
  
  $result = '';
  $result .= '<div style="'.$styles.'">';
  $result .= '<span style="'.$styles_c.'">Owner</span>';
  $result .= '<img style="'.$styles_b.'; border-right: 1px dashed grey;" ';
  if ($owner !== null) {
    $result .= 'title="'.$owner->getUsername().'" src="';
    $result .= $owner->getAvatarURL();
  } else {
    $result .= 'title="No-one" src="';
    $result .= 'http://www.gravatar.com/avatar/'.
      '205e460b479e2e5b48aec07710c08d50?f=y&d=mm';
  }
  $result .= '" />';
  $result .= '<span style="'.$styles_d.'">GM</span>';
  if ($gm !== null) {
    $result .= '<img style="'.$styles_b.'" ';
    $result .= 'title="'.$gm->getUsername().'" src="';
    $result .= $gm->getAvatarURL();
  } else {
    if ($owner !== null) {
      $result .= '<img style="'.$styles_b.'; opacity: 0.5;" ';
      $result .= 'title="'.$owner->getUsername().' (inferred from ownership)" src="';
      $result .= $owner->getAvatarURL();
    } else {
      $result .= '<img style="'.$styles_b.'" ';
      $result .= 'title="No-one" src="';
      $result .= 'http://www.gravatar.com/avatar/'.
        '205e460b479e2e5b48aec07710c08d50?f=y&d=mm';
    }
  }
  $result .= '" />';
  $result .= '</div>';
  return $result;
}

function renderOwnershipLinks(ManagedDAO $object) {
  global $auth, $db;
  if ($object->getOwnerID() === null) {
    echo ' &bull; ';
    echo '<a href="/ownership.php?type='.get_class($object).
      '&id='.$object->getID().'&mode=take'.
      '&r='.urlencode($_SERVER['REQUEST_URI']).
      '">Take Ownership</a>';
  } else if ($object->getOwnerID() === $auth->getUser()->getID() &&
      $object->getGMID() !== null) {
    echo ' &bull; ';
    echo '<a href="/ownership.php?type='.get_class($object).
      '&id='.$object->getID().'&mode=take'.
      '&r='.urlencode($_SERVER['REQUEST_URI']).
      '">Recover Ownership</a>';
  }
  if ($object->canManage() &&
      ($object->getOwnerID() === $auth->getUser()->getID() ||
      $object->getGMID() === $auth->getUser()->getID())) {
    $query = 'type='.get_class($object).
      '&id='.$object->getID().'&mode=delegate'.
      '&r='.urlencode($_SERVER['REQUEST_URI']);
?>
<form action="/ownership.php?<?php echo $query; ?>" method="POST">
  <?php
    $user = new User($db);
    $users = $user->loadAll();
    if (count($users) > 1) {
  ?>
  <p>Delegate to: <select name="target">
  <?php
      foreach ($users as $u) {
        if ($u->getID() !== $auth->getUser()->getID()) {
          echo '<option value="'.$u->getID().'">'.$u->getUsername().'</option>';
        }
      }
  ?>
  </select> <input type="submit" /></p>
  <?php } ?>
</form>
<?php
  }
}
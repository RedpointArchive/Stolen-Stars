<?php
require 'include.php';

if ($_GET['player_id'] != null) {
  $get_id = $_GET['player_id'];
  $inv = Inventory::loadFromPlayer($db, $get_id);
} else if ($_GET['store_id'] != null) {
  $get_id = $_GET['store_id'];
  $inv = Inventory::loadFromStore($db, $get_id);
} else if ($_GET['store_template_id'] != null) {
  $get_id = $_GET['store_template_id'];
  $inv = Inventory::loadFromStoreTemplate($db, $get_id);
}
$iteminsts = $inv->getItemInstances();

$all_items = Item::getItemDefinitions($db);

if (array_key_exists('submit', $_POST)) {
  foreach ($_POST as $key => $value) {
    if (substr($key, 0, 7) == 'invtype') {
      $id = (int)substr($key, 7);
      
      $item = new ItemInstance($db, $id);
      $item->load();
      
      if ($_POST[$key] === -1) {
        $item->delete();
        continue;
      }
      
      $item->setItem(new Item($db, $_POST[$key]));
      if (!array_key_exists('invquantity'.$id, $_POST)) {
        $item->setQuantity(1);
      } else {
        $item->setQuantity($_POST['invquantity'.$id]);
      }
      $item->setValue($_POST['invvalue'.$id]);
      $item->save();
    }
    if (substr($key, 0, 7) == 'newtype') {
      $id = (int)substr($key, 7);
      
      $item = new ItemInstance($db, -1);
      $item->setInventory($inv);
      $item->setItem(new Item($db, $_POST['newtype'.$id]));
      if (!array_key_exists('newquantity'.$id, $_POST)) {
        $item->setQuantity(1);
      } else {
        $item->setQuantity($_POST['newquantity'.$id]);
      }
      $item->setValue($_POST['newvalue'.$id]);
      $item->save();
    }
  }
  create_log($db, "The inventory of ".find_inventory_related_name($db, $get_id)." was edited");
  
  header('Location: '.find_inventory_related_url($db, $get_id));
  die();
}

?>
<h1>Edit <?php echo find_inventory_related_name($db, $get_id); ?>'s Inventory</h1>
<a href="<?php echo find_inventory_related_url($db, $get_id); ?>">Back / Cancel Changes</a>
<form action="/edit_inventory.php?<?php echo $_SERVER['QUERY_STRING']; ?>" method="post">
<h2>Inventory</h2>
<table>
  <tr>
    <th>Quantity</th>
    <th>Name</th>
    <th>Notes</th>
  </tr>
<?php
  foreach ($iteminsts as $iteminst) {
?>
  <tr>
    <td>
      <?php if ($iteminst->getItem()->getHasQuantity()) { ?>
        <input
          type="number"
          name="invquantity<?php echo $iteminst->getID(); ?>"
          value="<?php echo $iteminst->getQuantity(); ?>" />
      <?php } else { ?>
        <input
          type="number"
          name="invquantity<?php echo $iteminst->getID(); ?>"
          value="1" 
          disabled="disabled" />
      <?php } ?>
    </td>
    <td><select name="invtype<?php echo $iteminst->getID(); ?>" style="width: 200px;">
    <option value="-1">(delete this item)</option>
<?php
    foreach ($all_items as $item) {
      $selected = '';
      if ($iteminst->getItem()->getID() == $item->getID()) {
        $selected = ' selected="selected"';
      }
      echo '<option value="'.$item->getID().'"'.$selected.'>'.$item->getName().'</option>';
    }
?></select></td>
    <td>
      <input type="text" name="invvalue<?php echo $iteminst->getID(); ?>" value="<?php echo $iteminst->getValue(); ?>" />
    </td>
  </tr>
<?php
  }
  for ($i = 0; $i < 10; $i++) {
?>
  <tr>
    <td>
      <input
        type="number"
        name="newquantity<?php echo $i; ?>"
        value="" />
    </td>
    <td><select name="newtype<?php echo $i; ?>" style="width: 200px;"><option value="-1">(nothing)</option><?php
    foreach ($all_items as $item) {
      echo '<option value="'.$item->getID().'">'.$item->getName().'</option>';
    }
?></td>
    <td>
      <input type="text" name="newvalue<?php echo $i; ?>" value="" />
    </td>
  </tr>
<?php } ?>
</table>
<br/>
<input type="submit" name="submit" value="Save All Changes!" />
</form>
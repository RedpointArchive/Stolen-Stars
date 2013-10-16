<?php

final class Item {
  private $db;
  private $id;
  private $name;
  private $has_quantity;
  
  public function __construct($db, $id) {
    $this->db = $db;
    $this->id = $id;
  }
  
  public function setInternal($name, $has_quantity) {
    $this->name = $name;
    $this->has_quantity = $has_quantity;
  }
  
  public function load() {
    $stmt = $this->db->prepare("SELECT name, has_quantity FROM item WHERE id = :id");
    $stmt->bindValue(":id", $this->id);
    $stmt->execute();
    $result = $stmt->fetch();
    $this->name = $result["name"];
    $this->has_quantity = $result["has_quantity"];
  }
  
  public function save() {
    if ($this->id === -1) {
      $stmt = $this->db->prepare("
INSERT INTO item (name, has_quantity)
VALUES (:name, :has_quantity)");
    } else {
      $stmt = $this->db->prepare("
UPDATE item
SET name = :name, has_quantity = :has_quantity
WHERE id = :id");
      $stmt->bindValue(":id", $this->id);
    }
    $stmt->bindValue(":name", $this->name);
    $stmt->bindValue(":has_quantity", $this->has_quantity);
    $stmt->execute();
  }
  
  public function getID() {
    return $this->id;
  }
  
  public function getName() {
    return $this->name;
  }
  
  public function getHasQuantity() {
    return $this->has_quantity;
  }
  
  public function createInstance() {
    $stmt = $this->db->prepare("
INSERT INTO inventory_item
(inventory_id, item_id, quantity, value)
VALUES
(null, :id, 1, null)");
    $stmt->bindValue(":id", $this->id);
    $stmt->execute();
    $instance_id = $this->db->lastInsertId();
    return new ItemInstance(
      $this->db,
      $instance_id);
  }
  
  public static function getItemDefinitions($db) {
    $stmt = $db->query("SELECT id, name, has_quantity FROM item");
    $stmt->execute();
    $items = array();
    while ($row = $stmt->fetch()) {
      $item = new Item($db, $row["id"]);
      $item->setInternal($row["name"], $row["has_quantity"]);
      $items[] = $item;
    }
    return $items;
  }
}

final class ItemInstance {
  private $db;
  private $id;
  private $inventory;
  private $item;
  private $quantity;
  private $value;
  
  public function __construct($db, $id) {
    $this->db = $db;
    $this->id = $id;
  }
  
  public function setInternal($inventory, $item, $quantity, $value) {
    $this->inventory = $inventory;
    $this->item = $item;
    $this->quantity = $quantity;
    $this->value = $value;
  }
  
  public function load() {
    $stmt = $this->db->prepare("
SELECT
  inventory_item.id,
  inventory_item.item_id,
  inventory_item.inventory_id,
  item.name as name,
  item.has_quantity as has_quantity,
  inventory_item.quantity,
  inventory_item.value
FROM inventory_item
JOIN item
  ON item.id = inventory_item.item_id
WHERE inventory_item.id = :id");
    $stmt->bindValue(":id", $this->id);
    $stmt->execute();
    $result = $stmt->fetch();
    $this->item = new Item($this->db, $result["item_id"]);
    $this->item->setInternal($result["name"], $result["has_quantity"]);
    $this->inventory = new Inventory($this->db, $result["inventory_id"]);
    $this->quantity = $result["quantity"];
    $this->value = $result["value"];
  }
  
  public function save() {
    if ($this->id === -1) {
      $stmt = $this->db->prepare("
INSERT INTO inventory_item (inventory_id, item_id, quantity, value)
VALUES (:inventory_id, :item_id, :quantity, :value)");
    } else {
      $stmt = $this->db->prepare("
UPDATE inventory_item
SET
  item_id = :item_id,
  inventory_id = :inventory_id,
  quantity = :quantity,
  value = :value
WHERE id = :id");
      $stmt->bindValue(":id", $this->id);
    }
    $stmt->bindValue(":item_id", $this->item->getID());
    $stmt->bindValue(":inventory_id", $this->inventory->getID());
    $stmt->bindValue(":quantity", $this->quantity);
    $stmt->bindValue(":value", $this->value);
    $stmt->execute();
  }
  
  public function delete() {
    $stmt = $this->db->prepare("
DELETE FROM inventory_item
WHERE id = :id");
    $stmt->bindValue(":id", $this->id);
    $stmt->execute();
    $this->id = -1;
  }
  
  public function getID() {
    return $this->id;
  }
  
  public function getItem() {
    return $this->item;
  }
  
  public function setItem($value) {
    $this->item = $value;
  }
  
  public function getQuantity() {
    return $this->quantity;
  }
  
  public function setQuantity($value) {
    $this->quantity = $value;
  }
  
  public function getValue() {
    return $this->value;
  }
  
  public function setValue($value) {
    $this->value = $value;
  }
  
  public function getInventory() {
    return $this->inventory;
  }
  
  public function setInventory($value) {
    $this->inventory = $value;
  }
}

final class Inventory {
  private $db;
  private $id;
  
  public function __construct($db, $id) {
    $this->db = $db;
    $this->id = $id;
  }
  
  public function getID() {
    return $this->id;
  }
  
  // ================= ITEM MANIPULATION ===================
  
  public function getItemInstances() {
    $stmt = $this->db->prepare("
SELECT
  inventory_item.id,
  inventory_item.item_id,
  item.name as name,
  item.has_quantity as has_quantity,
  inventory_item.quantity,
  inventory_item.value
FROM inventory_item
JOIN item
  ON item.id = inventory_item.item_id
WHERE inventory_id = :id");
    $stmt->bindValue(":id", $this->id);
    $stmt->execute();
    $items = array();
    while ($row = $stmt->fetch()) {
      $itemdef = new Item($this->db, $row["item_id"]);
      $itemdef->setInternal($row["name"], $row["has_quantity"]);
      $item = new ItemInstance($this->db, $row["id"]);
      $item->setInternal(
        $this,
        $itemdef,
        $row["quantity"],
        $row["value"]);
      $items[] = $item;
    }
    return $items;
  }
  
  public function addItemInstance(ItemInstance $item) {
    $stmt = $this->db->prepare("
UPDATE inventory_item SET inventory_id = :inventory_id WHERE id = :id");
    $stmt->bindValue(":id", $item->getID());
    $stmt->bindValue(":inventory_id", $this->getID());
    $stmt->execute();
  }
  
  public function removeItemInstance(ItemInstance $item) {
    $stmt = $this->db->prepare("
DELETE FROM inventory_item WHERE id = :id");
    $stmt->bindValue(":id", $item->getID());
    $stmt->execute();
  }
  
  // ================= CLONING INVENTORY ===================
  
  public function cloneInventory() {
    // Create a new inventory.
    $stmt = $this->db->query("
INSERT INTO inventory (id) VALUES (null);");
    $stmt->execute();
    $id = $this->db->lastInsertId();
    
    // Clone all the item entries.
    $stmt = $this->db->prepare("
INSERT INTO inventory_item
(inventory_id, item_id, quantity, value)
SELECT :new_id, b.item_id, b.quantity, b.value
FROM inventory_item AS b
WHERE b.inventory_id = :old_id");
    $stmt->bindValue(":old_id", $this->getID());
    $stmt->bindValue(":new_id", $id);
    
    // TODO: Clone the item attributes.
    
    return $id;
  }
  
  public function __clone() {
    $new_id = $this->cloneInventory();
    return new Inventory($this->db, $new_id);
  }
  
  // ===================== RENDERING =======================
  
  public function render() {
    $items = $this->getItemInstances();
    echo '<ul>';
    foreach ($items as $iteminst) {
      echo '<li>';
      if ($iteminst->getItem()->getHasQuantity()) {
        echo $iteminst->getQuantity().' ';
      }
      echo $iteminst->getItem()->getName();
      $notes = $iteminst->getValue();
      if (trim($notes) != "") {
        echo ' - <em>'.$notes.'</em>';
      }
      echo '</li>';
    }
    echo '</ul>';
  }
  
  // ============== LOADS / IMPLICIT CREATES ===============
  
  public static function loadFromPlayer($db, $player_id) {
    $stmt = $db->prepare("
SELECT inventory_id FROM player
WHERE id = :id");
    $stmt->bindValue(":id", $player_id);
    $stmt->execute();
    $data = $stmt->fetch();
    
    // Create inventory for player if needed.
    if ($data["inventory_id"] === null) {
      $stmt = $db->query("
INSERT INTO inventory (id) VALUES (null);");
      $stmt->execute();
      $id = $db->lastInsertId();
      $stmt = $db->prepare("
UPDATE player SET inventory_id = :inventory_id WHERE id = :id");
      $stmt->bindValue(":id", $player_id);
      $stmt->bindValue(":inventory_id", $id);
      $stmt->execute();
      return new Inventory($db, $id);
    }
    
    return new Inventory($db, $data["inventory_id"]);
  }
  
  public static function loadFromStoreTemplate($db, $store_template_id) {
    $stmt = $db->prepare("
SELECT inventory_id FROM store_template
WHERE id = :id");
    $stmt->bindValue(":id", $store_template_id);
    $stmt->execute();
    $data = $stmt->fetch();
    
    // Create inventory for store template if needed.
    if ($data["inventory_id"] === null) {
      $stmt = $db->query("
INSERT INTO inventory (id) VALUES (null);");
      $stmt->execute();
      $id = $db->lastInsertId();
      $stmt = $db->prepare("
UPDATE store_template SET inventory_id = :inventory_id WHERE id = :id");
      $stmt->bindValue(":id", $store_template_id);
      $stmt->bindValue(":inventory_id", $id);
      $stmt->execute();
      return new Inventory($db, $id);
    }
    
    return new Inventory($db, $data["inventory_id"]);
  }
  
  public static function loadFromStore($db, $store_id) {
    $stmt = $db->prepare("
SELECT inventory_id, store_template_id FROM store
WHERE id = :id");
    $stmt->bindValue(":id", $store_id);
    $stmt->execute();
    $data = $stmt->fetch();
    
    // Clone the inventory from the store template if needed.
    if ($data["inventory_id"] === null) {
      $template_inv = self::loadFromStoreTemplate(
        $db,
        $data["store_template_id"]);
      $new_inv = $template_inv->cloneInventory();
      $stmt = $db->prepare("
UPDATE store SET inventory_id = :inventory_id WHERE id = :id");
      $stmt->bindValue(":id", $store_id);
      $stmt->bindValue(":inventory_id", $new_inv->getID());
      $stmt->execute();
      return $new_inv;
    }
    
    return new Inventory($db, $data["inventory_id"]);
  }
}

function find_inventory_related_url($db, $player_id) {
  $stmt = $db->prepare("
SELECT id
FROM player
WHERE id = :id");
  $stmt->bindValue(":id", $player_id);
  $stmt->execute();
  $result = $stmt->fetch();
  if ($result) return '/player.php?id='.$result["id"];
  return null;
}

function find_inventory_related_name($db, $player_id) {
  $stmt = $db->prepare("
SELECT id, name
FROM player
WHERE id = :id");
  $stmt->bindValue(":id", $player_id);
  $stmt->execute();
  $result = $stmt->fetch();
  if ($result) return $result["name"];
  return null;
}
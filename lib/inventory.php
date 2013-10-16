<?php

final class Item {
  private $db;
  private $id;
  
  public function __construct($db, $id) {
    $this->db = $db;
    $this->id = $id;
  }
  
  public function getID() {
    return $this->id;
  }
  
  public function getName() {
    $stmt = $this->db->prepare("SELECT name FROM item WHERE id = :id");
    $stmt->bindValue(":id", $this->id);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result["name"];
  }
  
  public function getHasQuantity() {
    $stmt = $this->db->prepare("SELECT has_quantity FROM item WHERE id = :id");
    $stmt->bindValue(":id", $this->id);
    $stmt->execute();
    $result = $stmt->fetch();
    return (bool)$result["has_quantity"];
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
}

final class ItemInstance {
  private $db;
  private $id;
  
  public function __construct($db, $id) {
    $this->db = $db;
    $this->id = $id;
  }
  
  public function getID() {
    return $this->id;
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
  
  public function getItems() {
    $stmt = $this->db->prepare("
SELECT id FROM inventory_item WHERE inventory_id = :id");
    $stmt->bindValue(":id", $this->id);
    $stmt->execute();
    $items = array();
    while ($row = $stmt->fetch()) {
      $items[] = new ItemInstance($this->db, $row["id"]);
    }
    return $items;
  }
  
  public function addItem(ItemInstance $item) {
    $stmt = $this->db->prepare("
UPDATE inventory_item SET inventory_id = :inventory_id WHERE id = :id");
    $stmt->bindValue(":id", $item->getID());
    $stmt->bindValue(":inventory_id", $this->getID());
    $stmt->execute();
  }
  
  public function removeItem(ItemInstance $item) {
    $stmt = $this->db->prepare("
DELETE FROM inventory_item WHERE id = :id");
    $stmt->bindValue(":id", $item->getID());
    $stmt->execute();
  }
  
  // ================= CLONING INVENTORY ===================
  
  public function cloneInventory() {
    // Create a new inventory.
    $stmt = $this->db->query("
INSERT INTO inventory (inventory_id) VALUES (null);");
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
INSERT INTO inventory (inventory_id) VALUES (null);");
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
INSERT INTO inventory (inventory_id) VALUES (null);");
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
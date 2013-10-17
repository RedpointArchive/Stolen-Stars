<?php

/**
 * Based off the Phabricator LiskDAO class, although not as complex
 * or performant since we're using it in a much more simple application.
 */
abstract class DAO {
  
  private $db;
  protected $id;
  
  public function __construct($db) {
    $this->db = $db;
    $this->id = -1;
  }
  
  public function __call($method, $args) {
    return $this->call($method, $args);
  }
  
  protected function getRelationships() {
    return array();
  }
  
  protected function getTableName() {
    static $name = null;
    if (!isset($name)) {
      $name = get_class($this);
      if (substr($name, strlen($name) - 3) == "DAO") {
        $name = substr($name, 0, strlen($name) - 3);
      }
      $name = strtolower($name);
    }
    return $name;
  }
  
  public function getProperties() {
    static $properties = null;
    if (!isset($properties)) {
      $class = new ReflectionClass(get_class($this));
      $properties = array();
      foreach ($class->getProperties(ReflectionProperty::IS_PROTECTED) as $p) {
        $normalized = strtolower($p->getName());
        $normalized = str_replace("_", "", $normalized);
        $properties[$normalized] = $p->getName();
      }
    }
    if (count($properties) === 0) {
      throw new Exception("Invalid property list!");
    }
    return $properties;
  }
  
  protected function attemptImplicitLoad($name) {
    $properties = $this->getProperties();
    $field = $name."_id";
    if ($this->$field === null) {
      return null;
    }
    $obj = new $name($this->db);
    $obj->load($this->$field);
    return $obj;
  }
  
  protected function readField($name) {
    $properties = $this->getProperties();
    if (!array_key_exists($name, $properties) &&
        array_key_exists($name."id", $properties)) {
      return $this->attemptImplicitLoad($name);
    }
    $field = $properties[$name];
    return $this->$field;
  }
  
  protected function writeField($name, $value) {
    $properties = $this->getProperties();
    $field = $properties[$name];
    $this->$field = $value;
  }
  
  final protected function call($method, $args) {
    if (substr($method, 0, 3) === 'get') {
      $field = strtolower(substr($method, 3));
      return $this->readField($field);
    } elseif (substr($method, 0, 3) === 'set') {
      $field = strtolower(substr($method, 3));
      $this->writeField($field, $args[0]);
      return $this;
    }
    
    throw new Exception("Unknown method on DAO.");
  }
  
  public function load($id) {
    $sql = "SELECT ";
    $columns = array();
    foreach ($this->getProperties() as $key => $value) {
      $columns[] = "a.$value";
    }
    $sql .= implode(",", $columns);
    $sql .= " FROM ";
    $sql .= $this->getTableName()." AS a";
    $sql .= " WHERE id = :id";
    trigger_error("load - ".$sql);
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    $result = $stmt->fetch();
    foreach ($this->getProperties() as $key => $value) {
      $this->writeField($key, $result[$value]);
    }
    return $this;
  }
  
  public function loadAll() {
    $sql = "SELECT ";
    $columns = array();
    foreach ($this->getProperties() as $key => $value) {
      $columns[] = "a.$value";
    }
    $sql .= implode(",", $columns);
    $sql .= " FROM ";
    $sql .= $this->getTableName()." AS a";
    trigger_error("loadall - ".$sql);
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $class = get_class($this);
    $results = array();
    while ($row = $stmt->fetch()) {
      $result = new $class($this->db);
      foreach ($this->getProperties() as $key => $value) {
        $result->writeField($key, $row[$value]);
      }
      $results[] = $result;
    }
    return $results;
  }
  
  public function save() {
    $sql = "";
    if ($this->id === -1) {
      $insertcols = array();
      $insertvals = array();
      foreach ($this->getProperties() as $key => $value) {
        if ($key === "id") {
          continue;
        }
        $insertcols[] = $value;
        $insertvals[] = ":$value";
      }
      
      // insert
      $sql .= "INSERT INTO ";
      $sql .= $this->getTableName();
      $sql .= " (";
      $sql .= implode(", ", $insertcols);
      $sql .= ") VALUES (";
      $sql .= implode(", ", $insertvals);
      $sql .= ");";
    } else {
      $assignments = array();
      foreach ($this->getProperties() as $key => $value) {
        if ($value !== "id") {
          $assignments[] = "$value = :$value";
        }
      }
      
      // update
      $sql .= "UPDATE ";
      $sql .= $this->getTableName();
      $sql .= " SET ";
      $sql .= implode(", ", $assignments);
      $sql .= " WHERE id = :id;";
    }
    trigger_error("save - ".$sql);
    
    $stmt = $this->db->prepare($sql);
    foreach ($this->getProperties() as $key => $value) {
      if ($this->id !== -1 || $value !== "id") {
        $stmt->bindValue(":$value", $this->readField($key));
      }
    }
    $stmt->execute();
    
    if ($this->id === -1) {
      $this->id = $this->db->lastInsertID();
    }
  }
}

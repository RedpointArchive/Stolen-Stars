<?php

final class CRUD {
  
  const EDITOR_TEXT = "text";
  const EDITOR_NUMBER = "number";
  const EDITOR_LOOKUP = "lookup";
  const EDITOR_BOOLEAN = "boolean";
  const EDITOR_PASSWORD = "password";
  const EDITOR_DESCRIPTION = "description";
   
  private $db;
  private $obj;
  private $id;
  private $class_name;
  private $return_url;
  private $editors;
  private $log_source;
  
  public static function renderEditLink($type, $id) {
    echo '<a href="/edit_anything.php?';
    echo 'class='.$type.'&amp;';
    echo 'id='.$id.'&amp;';
    echo 'r='.urlencode($_SERVER['REQUEST_URI']).'">Edit</a>';
  }

  public function __construct($db, $class, $id) {
    $this->db = $db;
    $this->id = $id;
    $this->obj = new $class($db);
    if ($this->id != -1) {
      $this->obj->load($this->id);
    }
    $this->class_name = $class;
    $this->editors = array();
  }
  
  public function getFormAction() {
    return $_SERVER['REQUEST_URI'];
  }
  
  public function getReturnURL() {
    return $this->return_url;
  }
  
  public function setReturnURL($value) {
    $this->return_url = $value;
  }
  
  public function getLogSource() {
    return $this->log_source;
  }
  
  public function setLogSource($value) {
    $this->log_source = $value;
  }
  
  public function setEditor($name, $type) {
    $this->editors[$name] = $type;
  }
  
  public function getEditor($name) {
    $editor = $this->editors[$name];
    $get_method = "get".str_replace("_", "", $name);
    $value = $this->obj->$get_method();
    switch ($editor) {
      case self::EDITOR_TEXT:
        echo '<input type="text" name="'.$name.'" value="'.$value.'" />';
        break;
      case self::EDITOR_NUMBER:
        echo '<input type="number" name="'.$name.'" value="'.$value.'" />';
        break;
      case self::EDITOR_PASSWORD:
        echo '<input type="password" name="'.$name.'" value="" placeholder="Leave blank to not change" />';
        break;
      case self::EDITOR_BOOLEAN:
        if ($value) {
          echo '<input type="checkbox" name="'.$name.'" value="true" checked="checked" />';
        } else {
          echo '<input type="checkbox" name="'.$name.'" value="true" />';
        }
        break;
      case self::EDITOR_LOOKUP:
        // Determine what objects to lookup based on the name.
        $class_name = str_replace("_", "", substr($name, 0, strlen($name) - 2));
        $instance = new $class_name($this->db);
        $options = $instance->loadAll();
        echo '<select name="'.$name.'">';
        foreach ($options as $option) {
          $selected = '';
          if ($option->getID() == $value) {
            $selected = ' selected="selected"';
          }
          echo '<option value="'.$option->getID().'"'.$selected.'>'.
            $option->getName().'</option>';
        }
        echo '</select>';
        break;
      case self::EDITOR_DESCRIPTION:
        echo '<textarea name="'.$name.'" style="width: 80%;" rows="20">';
        echo $value;
        echo '</textarea>';
        break;
    }
  }
  
  public function handleSave() {
    // Find all appropriate $_POST entries.
    foreach ($_POST as $key => $value) {
      if (isset($this->editors[$key])) {
        if ($this->editors[$key] == self::EDITOR_PASSWORD) {
          if (empty($value)) {
            // If the password fields is left blank, we don't
            // set it at all.
            continue;
          }
        }
        if (in_array($key, $this->obj->getProperties())) {
          $set_method = "set".str_replace("_", "", $key);
          $this->obj->$set_method($value);
        }
      }
    }
    
    // Search all editors because EDITOR_BOOLEAN is not
    // present when unchecked.
    foreach ($this->editors as $name => $editor) {
      if ($editor == self::EDITOR_BOOLEAN) {
        $set_method = "set".str_replace("_", "", $name);
        $this->obj->$set_method(isset($_POST[$name]));
      }
    }
    
    // Save the object.
    $this->obj->save();
    
    if ($this->getLogSource() !== null) {
      $get_method = "get".$this->getLogSource();
      create_log($this->db, $this->obj->$get_method().' was edited');
    }
    
    // Redirect.
    if ($this->getReturnURL() !== null) {
      header('Location: '.$this->getReturnURL());
    } else {
      header('Location: /');
    }
    die();
  }
  
  public function handleEdit() {
    if (array_key_exists('submit', $_POST)) {
      $this->handleSave();
      return;
    }
    echo '<h1>';
    if ($this->id == -1) {
      echo 'New';
    } else {
      echo 'Edit';
    }
    echo ' '.ucwords($this->class_name).'</h1>';
    if ($this->getReturnURL() !== null) {
      echo '<a href="'.$this->getReturnURL().'">Back / Cancel Changes</a>';
    }
    echo '<form action="'.$this->getFormAction().'" method="post">';
    echo '<h2>'.ucwords($this->class_name).'</h2>';
    echo '<table>';
    echo '  <tr>';
    echo '    <th width="200">Key</th>';
    echo '    <th>Value</th>';
    echo '  </tr>';
    foreach ($this->obj->getProperties() as $key => $value) {
      if ($value === "id") {
        continue;
      }
      if (!array_key_exists($value, $this->editors)) {
        continue;
      }
      if ($this->editors[$value] === self::EDITOR_DESCRIPTION) {
        continue;
      }
      echo '  <tr>';
      echo '    <td>'.ucwords(str_replace("_", " ", $value)).'</td>';
      echo '    <td>';
      echo $this->getEditor($value);
      echo '    </td>';
      echo '  </tr>';
    }
    echo '</table>';
    foreach ($this->obj->getProperties() as $key => $value) {
      if ($value === "id") {
        continue;
      }
      if (!array_key_exists($value, $this->editors)) {
        continue;
      }
      if ($this->editors[$value] === self::EDITOR_DESCRIPTION) {
        echo '<h2>'.ucwords(str_replace("_", " ", $value)).'</h2>';
        echo $this->getEditor($value);
      }
    }
    echo '<h2>SAVE CHANGES</h2>';
    echo '<input type="submit" name="submit" value="Save All Changes!" />';
    echo '</form>';
  }

}
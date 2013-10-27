<?php

final class CRUD {
  
  const EDITOR_TEXT = "text";
  const EDITOR_NUMBER = "number";
  const EDITOR_LOOKUP = "lookup";
  const EDITOR_BOOLEAN = "boolean";
  const EDITOR_PASSWORD = "password";
  const EDITOR_DESCRIPTION = "description";
  const EDITOR_DATE = "date";
  const EDITOR_PERCENT = "percent";
  const EDITOR_COLOR = "color";
  
  const DATETIME_FORMAT = "Y-m-d\TH:i";
   
  private $db;
  private $obj;
  private $id;
  private $class_name;
  private $return_url;
  private $editors;
  private $log_source;
  private $fixed_values;
  private $friendly_names;
  private $defaults;
  private $edit_message_prefix;
  private $edit_message_suffix;
  private $allow_nulls;
  private $require_manage;
  
  public static function getEditLink($text, $type, $id, $additional) {
    if ($additional == null) {
      $additional = array();
    }
    $result = '<a href="/edit.php?';
    $result .= 'class='.$type.'&amp;';
    $result .= 'id='.$id.'&amp;';
    $result .= 'r='.urlencode($_SERVER['REQUEST_URI']);
    foreach ($additional as $key => $value) {
      $result .= '&amp;'.$key.'='.urlencode($value);
    }
    $result .= '">'.$text.'</a>';
    return $result;
  }
  
  public static function getNewLink($text, $type, $additional) {
    echo self::getEditLink($text, $type, -1, $additional);
  }
  
  public static function renderEditLinkWithText($text, $type, $id, $additional) {
    echo self::getEditLink($text, $type, $id, $additional);
  }
  
  public static function renderNewLinkWithText($text, $type, $additional) {
    echo self::getEditLink($text, $type, -1, $additional);
  }
  
  public static function renderEditLink($type, $id, $additional = null) {
    echo self::getEditLink('Edit', $type, $id, $additional);
  }
  
  public static function renderNewLink($type, $additional = null) {
    echo self::getEditLink('New '.ucwords($type), $type, -1, $additional);
  }

  public function __construct($db, $class, $id) {
    $this->db = $db;
    $this->id = (int)$id;
    $this->obj = new $class($db);
    if ($this->id !== -1) {
      $this->obj->load($this->id);
    }
    $this->class_name = $class;
    $this->editors = array();
    $this->fixed_values = array();
    $this->friendly_names = array();
    $this->defaults = array();
    $this->edit_message_prefix = "";
    $this->edit_message_suffix = " was edited";
    $this->allow_nulls = array();
    $this->require_manage = array();
  }
  
  public function getFormAction() {
    return $_SERVER['REQUEST_URI'];
  }
  
  public function getReturnURL() {
    return $this->return_url;
  }
  
  public function allowNull($name) {
    $this->allow_nulls[$name] = true;
  }
  
  public function preventNull($name) {
    $this->allow_nulls[$name] = false;
  }
  
  public function requireManage($name) {
    $this->require_manage[$name] = true;
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
  
  public function setFixedValue($name, $value) {
    $this->fixed_values[$name] = $value;
  }
  
  public function setFriendlyName($name, $value) {
    $this->friendly_names[$name] = $value;
  }
  
  public function setDefault($name, $value) {
    $this->defaults[$name] = $value;
  }
  
  public function setEditMessage($prefix, $suffix) {
    $this->edit_message_prefix = $prefix;
    $this->edit_message_suffix = $suffix;
  }
  
  private function canEdit($name) {
    if ($this->obj instanceof ManagedDAO) {
      foreach ($this->require_manage as $field => $require) {
        if ($field === $name) {
          if ($require && !$this->obj->canManage()) {
            return false;
          }
        }
      }
    }
    return true;
  }
  
  public function getEditor($name) {
    $editor = $this->editors[$name];
    $get_method = "get".str_replace("_", "", $name);
    $value = $this->obj->$get_method();
    if ($this->id === -1) {
      if (isset($this->defaults[$name])) {
        $value = $this->defaults[$name];
      }
    }
    $d = '';
    if (!$this->canEdit($name)) {
      $d = ' disabled="disabled"';
    }
    switch ($editor) {
      case self::EDITOR_TEXT:
        echo '<input type="text" name="'.$name.'" value="'.$value.'"'.$d.' />';
        break;
      case self::EDITOR_NUMBER:
        echo '<input type="number" name="'.$name.'" value="'.$value.'"'.$d.' />';
        break;
      case self::EDITOR_PERCENT:
        echo '<input type="number" step="0.01" min="0" max="1" name="'.$name.'" value="'.$value.'"'.$d.' />';
        break;
      case self::EDITOR_COLOR:
        echo '<input type="color" name="'.$name.'" value="'.$value.'"'.$d.' />';
        break;
      case self::EDITOR_PASSWORD:
        echo '<input type="password" name="'.$name.'" value=""'.$d.' placeholder="Leave blank to not change" />';
        break;
      case self::EDITOR_BOOLEAN:
        if ($value) {
          echo '<input type="checkbox" name="'.$name.'" value="true"'.$d.' checked="checked" />';
        } else {
          echo '<input type="checkbox" name="'.$name.'" value="true"'.$d.' />';
        }
        break;
      case self::EDITOR_LOOKUP:
        // Determine what objects to lookup based on the name.
        $relationships = $this->obj->getRelationships();
        if (isset($relationships[$name])) {
          $class_name = $relationships[$name];
        } else {
          $class_name = str_replace("_", "", substr($name, 0, strlen($name) - 2));
        }
        $instance = new $class_name($this->db);
        $options = $instance->loadAll();
        echo '<select name="'.$name.'"'.$d.'>';
        if (isset($this->allow_nulls[$name]) && $this->allow_nulls[$name]) {
          $attrs = '';
          if ($value === null) {
            $attrs .= ' selected="selected"';
          }
          echo '<option value=""'.$attrs.'>(none)</option>';
        }
        foreach ($options as $option) {
          $attrs = '';
          if ($option->getID() == $value) {
            $attrs .= ' selected="selected"';
          }
          if ($option->getID() == $this->obj->getID() && strtolower($class_name) == strtolower($this->class_name)) {
            // Don't allow the user to pick the same object as ourself.
            $attrs .= ' disabled="disabled"';
          }
          echo '<option value="'.$option->getID().'"'.$attrs.'>'.
            $option->getName().'</option>';
        }
        echo '</select>';
        break;
      case self::EDITOR_DESCRIPTION:
        echo '<textarea name="'.$name.'"'.$d.' style="width: 80%;" rows="20">';
        echo $value;
        echo '</textarea>';
        break;
      case self::EDITOR_DATE:
        echo '<input type="datetime-local" name="'.$name.'"'.$d.' value="'.date(self::DATETIME_FORMAT, $value).'" />';
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
          if ($this->editors[$key] == self::EDITOR_DATE) {
            $value = strtotime($value);
          }
          if ($this->editors[$key] == self::EDITOR_LOOKUP) {
            if (empty($value)) {
              $value = null;
            }
          }
          if ($this->canEdit($key)) {
            $this->obj->$set_method($value);
          }
        }
      }
    }
    
    // Search all editors because EDITOR_BOOLEAN is not
    // present when unchecked.
    foreach ($this->editors as $name => $editor) {
      if ($editor == self::EDITOR_BOOLEAN) {
        $set_method = "set".str_replace("_", "", $name);
        if ($this->canEdit($name)) {
          $this->obj->$set_method(isset($_POST[$name]));
        }
      }
    }
    
    // Save the object.
    $this->obj->save();
    
    if ($this->getLogSource() !== null) {
      $name = null;
      $log_source = $this->getLogSource();
      if (is_callable($log_source)) {
        $name = $log_source($this->obj);
      } else {
        $get_method = "get".$this->getLogSource();
        $name = $this->obj->$get_method();
      }
      if (!empty($name)) {
        create_log(
          $this->db,
          $this->edit_message_prefix.$name.$this->edit_message_suffix);
      }
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
      $name = null;
      if (isset($this->friendly_names[$value])) {
        $name = $this->friendly_names[$value];
      } else {
        $name = ucwords(str_replace("_", " ", $value));
        
        // Remove any trailing " Id"
        if (substr($name, strlen($name) - 3) == ' Id') {
          $name = substr($name, 0, strlen($name) - 3);
        }
      }
      echo '  <tr>';
      echo '    <td width="200">'.$name.'</td>';
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
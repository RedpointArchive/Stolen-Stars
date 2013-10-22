<?php

final class Log extends DAO {
  protected $user_id;
  protected $created;
  protected $content;
}

function create_log($db, $content) {
  global $auth;
  $stmt = $db->prepare("
INSERT INTO log
(user_id, created, content)
VALUES
(:user_id, :created, :content)");
  $stmt->bindValue(":user_id", $auth->getUser()->getID());
  $stmt->bindValue(":created", time());
  $stmt->bindValue(":content", $content);
  $stmt->execute();
}

function print_log($db) {
  $stmt = $db->prepare("
SELECT created, user.username, log.user_id, content FROM log
LEFT JOIN user ON user.id = log.user_id
ORDER BY created DESC");
  $stmt->execute();
  echo '<table style="width: 100%;">
  <tr>
    <th style="text-align: left; width: 300px;">Date</th>
    <th style="text-align: left;">User</th>
    <th style="text-align: left;">Message</th>
  </tr>';
  while ($row = $stmt->fetch()) {
    $username = '(not logged)';
    if ($row['user_id'] !== null) {
      $username = $row['username'];
    }
    echo '<tr>
    <td>'.date(DATE_RFC822, $row['created']).'</td>
    <td>'.$username.'</td>
    <td>'.$row["content"].'</td>
  </tr>';
  }
  echo '</table>';
}

?>
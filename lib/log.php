<?php

function create_log($db, $content) {
  $stmt = $db->prepare("
INSERT INTO log
(created, content)
VALUES
(:created, :content)");
  $stmt->bindValue(":created", time());
  $stmt->bindValue(":content", $content);
  $stmt->execute();
}

function print_log($db) {
  $stmt = $db->prepare("
SELECT created, content FROM log
ORDER BY created DESC");
  $stmt->execute();
  echo '<table style="width: 100%;">
  <tr>
    <th style="text-align: left; width: 300px;">Date</th>
    <th style="text-align: left;">Message</th>
  </tr>';
  while ($row = $stmt->fetch()) {
    echo '<tr>
    <td>'.date(DATE_RFC822, $row['created']).'</td>
    <td>'.$row["content"].'</td>
  </tr>';
  }
  echo '</table>';
}

?>
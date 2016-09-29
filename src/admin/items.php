<?php
require '../include.php';

$item = new ItemDAO($db);
$items = $item->loadAll();

?>
<h1>Manage Items</h1>
<a href="/admin/">Back</a>
<h2>Item List</h2>
<table>
  <tr>
    <th>Name</th>
    <th>Has Quantity?</th>
    <th>Actions</th>
  </tr>
<?php
foreach ($items as $item) {
?>
  <tr>
    <td><?php echo $item->getName(); ?></td>
    <td><?php echo ($item->getHasQuantity() ? "yes" : "no"); ?></td>
    <td><a href="/admin/edit_item.php?id=<?php echo $item->getID(); ?>&amp;r=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Edit</a></td>
  </tr>
<?php
}
?>
</table>
<br />
<a href="/admin/edit_item.php?id=-1&amp;r=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
  Create Item
</a>
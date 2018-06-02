<?php
$sql = "SELECT a.created, a.comments FROM k8_announcement a
	LEFT OUTER JOIN k8_user u ON a.role_id = u.role_id
	WHERE ( u.id = " . $_SESSION['id'] . " OR a.role_id IS NULL )
	AND a.top_organization_id = " . $_SESSION['top_organization_id'] . "
        AND a.is_active = 1 
      	ORDER BY created DESC
      	LIMIT 0, 10
      	";
if ($result = mysql_query($sql)) {}else { error_log($sql);}
?>
<ul class="widget_ul">
    <?php
    while ($row = mysql_fetch_assoc($result)) {
        ?>
        <li><?php echo $row['created']; ?> : <?php echo $row['comments']; ?></li>
    <?php } ?>
</ul>
<?php
$sql = "SELECT MAX(lv.id) AS last_visited_id, tp.variable AS top_page_name, lv.top_page_id AS top_page_id FROM k8_last_visited lv
	LEFT OUTER JOIN k8_top_page tp ON tp.id = lv.top_page_id
	WHERE user_id = " . $_SESSION['id'] . "
	AND top_organization_id = " . $_SESSION['top_organization_id'] . "
      	GROUP BY lv.top_page_id
      	ORDER BY MAX(lv.id) DESC
      	LIMIT 0, 9
      	";
if ($result = mysql_query($sql)) {}else { error_log($sql);}
?>
<ul class="widget_ul">
    <?php
    while ($row = mysql_fetch_assoc($result)) {
        ?>
        <li><a href="index.php?page=<?php echo $row['top_page_id']; ?>" target="_self"><?php echo $_SESSION['main_language']->$row['top_page_name']; ?></a></li>
    <?php } ?>
</ul>
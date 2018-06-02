<ul class="widget_ul">
    <?php
    $data = getAlerts000();
    $iterator = 0;
    foreach ($data as $alert) {
        $iterator++;
        if ($iterator == 10) {
            break;
        }
        ?>
        <li><?php echo $alert['alert']; ?> - <?php echo $alert['visit_page']; ?></li>
    <?php } ?>
</ul>
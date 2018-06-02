<ul id="nav">
    <li><a href="index.php"><img class="icon" src="media/home.png"><?php echo $_SESSION['main_language']->home; ?></a></li>
    <li><a href="#"><img class="icon" src="media/modules.png"><?php echo $_SESSION['main_language']->modules; ?></a>
        <ul>
            <?php showModulesMenu(); ?>
        </ul>
    </li>
    <li><a href="#"><img class="icon" src="media/help.png"><?php echo $_SESSION['main_language']->help; ?></a>
        <ul>
            <li><a href="http://khalifacomputergroup.com/" target="_blank"><?php echo $_SESSION['main_language']->submit_support_ticket; ?></a></li>
        </ul>
    </li>
    <li><a href="#"><img class="icon" src="media/my-account.png"><?php echo $_SESSION['main_language']->my_account; ?></a>
        <ul>
            <li><a href="index.php?page=2"><?php echo $_SESSION['main_language']->edit_my_info; ?></a></li>
        </ul>
    </li>
    <li><a href="index.php?page=1"><img class="icon" src="media/logout.png"><?php echo $_SESSION['main_language']->logout; ?></a>
    </li>
</ul>
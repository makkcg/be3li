<?php
startPage();
?>
<div class="col1">
    <?php
    $error_message = "";
    if (isset($_POST) && isset($_POST['secret']) && $_POST['secret'] == "gndhjkerfjk") {

        if ($_POST['name'] == '') {
            $error_message = $_SESSION['main_language']->name_is_mandatory;
        }

        if (isset($_POST['new_password']) && $_POST['new_password'] != '') {
            if ($_POST['new_password'] != $_POST['confirm_password']) {
                $error_message = $_SESSION['main_language']->password_mismatch;
            }
            if (!isset($_POST['old_password']) || $_POST['old_password'] == '') {
                $error_message = $_SESSION['main_language']->wrong_old_password;
            }
            $result = mysql_query("SELECT password FROM k8_user WHERE id = '" . $_SESSION['id']
                    . "' AND top_organization_id = '" . $_SESSION['top_organization_id']
                    . "' AND is_active = 1 ");
            $row = mysql_fetch_assoc($result);
            $old_password = crypt($_POST['old_password'], $row['password']);
            if ($old_password != $row['password']) {
                $error_message = $_SESSION['main_language']->wrong_old_password;
            }
        }

        if ($error_message == "") {
            if (isset($_POST['new_password']) && $_POST['new_password'] != '') {
                $error_message = updateAllInfo();
            } else {
                $error_message = updateNamesOnly();
            }
        }
    }

    function updateAllInfo() {
        $date = getFormatedDateTime();
        $password = crypt($_POST["new_password"], $salt);
        $sql = "UPDATE k8_user SET password = '" . $password . "', name = '" .
                $_POST['name'] . "', name_tr = '" . $_POST['name_tr'] . "', top_language_id = '" . $_POST['top_language_id'] . "'" 
                    . " , modified = '" . $date . "', modified_by = '" . $_SESSION['id'] . "' 
                    WHERE id = '" . $_SESSION['id'] . "'
                    ";
        if (!mysql_query($sql)) { error_log($sql); }
        $audit_data = "Changed his password" .
                " , name = " . $_POST['name'] . ", name_tr = " . $_POST['name_tr']. " , top_language_id = " . $_POST['top_language_id'];
        $sql = "INSERT INTO k8_audit VALUES(NULL, '" . $_SESSION['id'] . "', '" . $_GET['page']
                . "', '" . "k8_user" . "', " . $_SESSION["top_organization_id"] . ", '" . $_SESSION['main_language']->edit . "', '" . $audit_data
                . "', '" . getFormatedDateTime() . "', '" . $_SESSION['timezone'] . "')";
        if (!mysql_query($sql)) { error_log($sql); }
        return $_SESSION['main_language']->info_updated_successfully;
    }

    function updateNamesOnly() {
        $date = getFormatedDateTime();
        $password = crypt($_POST["new_password"], $salt);
        $sql = "UPDATE k8_user SET name = '" .
                $_POST['name'] . "', name_tr = '" . $_POST['name_tr'] . "', top_language_id = '" . $_POST['top_language_id'] . "'" 
                    . " , modified = '" . $date . "', modified_by = '" . $_SESSION['id'] . "' 
                    WHERE id = '" . $_SESSION['id'] . "'
                    ";
        if (!mysql_query($sql)) { error_log($sql); }
        $audit_data = "name = " . $_POST['name'] . " , name_tr = " . $_POST['name_tr'] . " , top_language_id = " . $_POST['top_language_id'];
        $sql = "INSERT INTO k8_audit VALUES(NULL, '" . $_SESSION['id'] . "', '" . $_GET['page']
                . "', '" . "k8_user" . "', " . $_SESSION["top_organization_id"] . ", '" . $_SESSION['main_language']->edit . "', '" . $audit_data
                . "', '" . getFormatedDateTime() . "', '" . $_SESSION['timezone'] . "')";
        if (!mysql_query($sql)) { error_log($sql); }
        return $_SESSION['main_language']->info_updated_successfully;
    }
    ?>

    <?php
    $sql = "SELECT mt.name, mt.name_tr, mt.email, mt.password, mt.top_language_id AS top_language_id FROM k8_user mt "
            . " WHERE mt.id = " . $_SESSION['id']
            . " AND mt.top_organization_id = " . $_SESSION['top_organization_id']
            . " AND mt.is_active = 1 ";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($error_message != "") {
        echo "<p class='error_message'>" . $error_message . "</p>";
    }
    ?>

    <form method="post" name="change_pass">
        <label ><?php echo $_SESSION['main_language']->name; ?>: <span class="mandatory">*</span></label>
        <input name="name"  value="<?php echo $row["name"]; ?>" autocomplete="off" >
        <label ><?php echo $_SESSION['main_language']->name_tr; ?>:</label>
        <input name="name_tr"  value="<?php echo $row["name_tr"]; ?>" autocomplete="off" >
        <label ><?php echo $_SESSION['main_language']->main_language; ?>:</label>
        <input type="hidden" name="top_language_id"  value="<?php echo $row['top_language_id']; ?>" autocomplete="off" readonly>
        <input type="text" name="lang"  value="English" autocomplete="off" readonly>
        <label ><?php echo $_SESSION['main_language']->email; ?>:</label>
        <input name="email"  value="<?php echo $row["email"]; ?>" readonly >
        <label ><?php echo $_SESSION['main_language']->old_password; ?>:</label>
        <input name="old_password" type="password" autocomplete="off">
        <label ><?php echo $_SESSION['main_language']->new_password; ?>:</label>
        <input name="new_password" type="password" autocomplete="off" >
        <label ><?php echo $_SESSION['main_language']->confirm_password; ?>:</label>
        <input name="confirm_password" type="password" autocomplete="off" >
        <div class="sep"></div>
        <input type="hidden" name="secret" value="gndhjkerfjk">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
    </form>

</div>

<?php
endPage();
?>
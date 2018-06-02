<?php
$sql = "SELECT details FROM k8_configuration "
        . " WHERE variable = 'use_letterhead' "
        . " AND top_organization_id = " . $_SESSION['top_organization_id'];
if ($result = mysql_query($sql)) {}else { error_log($sql);}
$row = mysql_fetch_assoc($result);

if (isset($_GET) && isset($_GET['printout']) && isset($_GET['printout']) && $_GET['printout'] != "") {
    if (substr($_GET['printout'], -3) == "_tr") {
        $file_name = $language = $_SESSION['secondary_language'];
    } else {
        $language = $_SESSION['main_language'];
    }
    $temp = $_SESSION['main_language'];
    $_SESSION['main_language'] = $language;
    include "includes/header.php";
    $_SESSION['main_language'] = $temp;

    $page_title = str_replace("_tr", "", $_GET['printout']);
    ?>
    <div id="a4">
        <div id="a4_inner">
            <?php if ($row['details'] == 0) { ?>
                <div id="a4_header">
                    <div id="logo">
                        <img src="
                        <?php
                        if ($_SESSION['top_organization_logo'] != '') {
                            echo "data:image/png;base64," . $_SESSION['top_organization_logo'];
                        } else {
                            echo "media/logo-wide.png";
                        }
                        ?>
                             ">
                    </div>
                    <div id="a4_title">
                        <h2><?php echo $language->$page_title; ?></h2>
                    </div>
                </div>
            <?php } else { ?>
                <div id="a4_header_letterhead"><h2><?php echo $language->$page_title; ?></h2></div>
            <?php } ?>
            <?php
            include "printouts/" . $page_title .= ".php";
            $sql_footer = "SELECT details FROM k8_configuration "
                    . " WHERE variable = '".$page_title."' "
                    . " AND top_organization_id = " . $_SESSION['top_organization_id'];
            $result_footer = mysql_query($sql_footer);
            $row_footer = mysql_fetch_assoc($result_footer);
            $footer = $row_footer['details'];
            ?>        
            <div class="a4_footer"><?php echo $footer ?></div>
            <div class="clear"></div>
        </div>
    </div>
<?php } ?>
<?php

function dbConnect() {
    global $database_name;
    global $database_user;
    global $database_pass;
    global $database_host;
    mysql_connect($database_host, $database_user, $database_pass)or
            die("Cannot connect to database : " . mysql_error());
    mysql_select_db("$database_name")or die("cannot select DB");
    mysql_query("SET NAMES 'utf8'");
}

function getDefaultSelectLines1($table) {
    $select = " , mt.comments AS comments, mt.top_organization_id AS top_organization_id, 
    mt.created, crea.name AS createdby_id, mt.modified, modi.name AS modifiedby_id, 
    mt.is_active AS is_active FROM " . $table . " mt ";
    return $select;
}

function getDefaultSelectLines2() {
    $select = " 
    LEFT OUTER JOIN k8_user crea ON crea.id = mt.created_by 
    LEFT OUTER JOIN k8_user modi ON modi.id = mt.modified_by  
    WHERE mt.top_organization_id = " . $_SESSION['top_organization_id'] . " ";
    if ($_SESSION['top_role_access_type_id'] % 20 > 10) {
        $select .= " AND mt.created_by = " . $_SESSION['id'] . " ";
    }
    return $select;
}

function getDefaultConditions() {
    $condition = array();
    $condition["column"] = "is_active";
    $condition["op"] = "eq";
    $condition["value"] = "0";
    $condition["class"] = "deleted_row";
    $conditions[] = $condition;
    return $conditions;
}

function setDefaultTimeZone($timezone) {
    date_default_timezone_set($timezone);
}

function getFormatedDateTime() {
    global $date_time_format;
    return date($date_time_format);
}

function getFormatedDate() {
    global $date_format;
    return date($date_format);
}

function startPage() {
    if (isset($_GET) && isset($_GET['page']) && $_GET['page'] > 1) {
                $result = mysql_query("SELECT p.variable AS page_name , f.variable AS 
                folder_name FROM k8_top_page p
                LEFT OUTER JOIN k8_top_folder f ON p.top_folder_id = f.id
                WHERE p.id = " . $_GET['page']);
                $row = mysql_fetch_assoc($result);
                $full_name =  $_SESSION['main_language']->$row['folder_name'] . " -> " .
                $_SESSION['main_language']->$row['page_name'];
                $page_title = $_SESSION['main_language']->$row['page_name'];
            } else {
                $full_name = $_SESSION['main_language']->home;
                $page_title = "cloudMINI | cloudSoftware";
            }
    $_SESSION['page_title'] = $page_title;
    include "includes/header.php";
    ?>
    <div id="container">
        <div id="header">
            <div id="logo">
                <a href="index.php"><img class="logo_image" src="
                    <?php
                    if ($_SESSION['top_organization_logo'] != '') {
                        echo "data:image/png;base64," . $_SESSION['top_organization_logo'];
                    } else {
                        echo "media/logo-wide.png";
                    }
                    ?>
                                         "></a>
            </div>
            <div id="mainmenu">
                <?php include "includes/menu.php"; ?>
            </div>
        </div>
        <h2 class="page_title">
            <?php
            echo $full_name;
            ?></h2>
        <div class="user_info">
            <?php echo $_SESSION['main_language']->you_are_logged_in_as; ?> <a href="index.php?page=2">
                <?php echo $_SESSION['name']; ?></a><br/>
            <span class="last_activity"><?php echo $_SESSION['main_language']->last_activity; ?>: 
                <?php echo getFormatedDateTime() . " " . $_SESSION['timezone'] ?> </span>
        </div>
        <div class="clear">
        </div>
        <?php
    }

    function endPage() {
        ?>
        <div class="clear">
        </div>
        <?php include "includes/footer.php"; ?>
    </div>
    <?php
}

function randString($length) {
    $str = '';
    $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count - 1)];
    }
    return $str;
}

function randNumber($length) {
    $str = '';
    $charset = '0123456789';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count - 1)];
    }
    return $str;
}

function generateSalt() {
    return '$2a$14$' . randString(22);
}

function showModulesMenu() {
    $sql = "SELECT fi.id AS page_id, fi.variable AS page_name, fo.variable AS folder_name FROM k8_top_page fi
      	LEFT OUTER JOIN k8_top_folder fo ON fo.id = fi.top_folder_id
      	WHERE fi.id IN 
      	(
      		SELECT ra.top_page_id FROM k8_role_access ra
      		INNER JOIN k8_user u ON u.role_id = ra.role_id 
      		WHERE u.id = " . $_SESSION['id'] . " 
                AND ra.top_organization_id = " . $_SESSION['top_organization_id'] . " 
                AND ra.is_active = 1 
      	)
      	AND fi.top_folder_id != 0
      	ORDER BY fi.id ASC
      	";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $last_folder_name = "";
    $number_of_modules_shown = 0;
    while ($row = mysql_fetch_assoc($result)) {
        if ($row['folder_name'] != $last_folder_name) {
            if ($last_folder_name != "") {
                echo "</ul>";
            }
            $number_of_modules_shown++;
            $last_folder_name = $row['folder_name'];
            echo "<li><a class='has-submenu' href='#'>" . $_SESSION['main_language']->$row['folder_name']
            . "</a><ul>";
        }
        echo "<li><a href='index.php?page=" . $row['page_id'] . "'>"
        . $_SESSION['main_language']->$row['page_name'] . "</a></li>";
    }
    if ($number_of_modules_shown > 0) {
        echo "</ul>";
    }
}

function showPageWithoutCheck($top_page_id) {
    $sql = "INSERT INTO k8_last_visited VALUES(NULL," . $_SESSION['id'] . ","
            . $top_page_id . "," . $_SESSION['top_organization_id'] . ")";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    
    $sql = "SELECT url FROM k8_top_page WHERE id = " . $top_page_id;
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    include $row['url'];
}

function showPage($top_page_id) {
    if (userHasAccess($top_page_id)) {
        $sql = "INSERT INTO k8_last_visited VALUES(NULL," . $_SESSION['id'] . "," . $top_page_id
                . "," . $_SESSION['top_organization_id'] . ")";
        if (!mysql_query($sql)) { error_log($sql); }
        $result = mysql_query("SELECT url FROM k8_top_page WHERE id = " . $top_page_id);
        $row = mysql_fetch_assoc($result);
        include $row['url'];
        return true;
    } else {
        return false;
    }
}

function newIdColumn() {
    return array(title => $_SESSION['main_language']->id, name => "id", dbname => "mt.id",
        width => 70, hidden => false, resizable => true, editable => false, export=> false);
}

function newLinkColumn($title, $dbname, $link, $target) {
    return array(title => $title, name => $dbname, dbname => $dbname, linkoptions => "target='" . $target . "'",
        link => $link, width => 100, hidden => false, resizable => true, editable => false, export => false,
        "align" => "center", search => false, sortable => false);
}

function newTextColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 200, hidden => false,
        resizable => true, editable => true);
}

function newCustomTextColumn($title, $name, $dbname) {
    return array(title => $title, name => $name, dbname => $dbname, width => 200, hidden => false,
        resizable => true, editable => true);
}

function newFormulaColumn($title, $name) {
    return array(title => $title, name => $name, dbname => $name, width => 200, hidden => false,
        resizable => true, editable => false, search => false, sortable => false);
}

function newHiddenColumn($dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $name, name => $name, dbname => $dbname, width => 200, hidden => true,
        resizable => true, editable => true, export => false);
}

function newCustomHiddenColumn($name, $dbname) {
    return array(title => $name, name => $name, dbname => $dbname, width => 200, hidden => true,
        resizable => true, editable => false, export => false);
}

function newFooterColumn($dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $name, name => $name, dbname => $dbname, width => 200, hidden => true,
        resizable => true, editable => false, export => false);
}

function newPasswordColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 200, hidden => false,
        resizable => true, editable => true, formatter => "password", edittype => "password");
}

function newDateTimeColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 200, hidden => false,
        resizable => true, editable => true, formatter => "datetime",
        formatoptions => array("srcformat" => 'Y-m-d H:i:s', "newformat" => 'Y-m-d H:i:s'));
}


function newDateColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, formatter => "date",
        editrules => array(required => false),
        "formatoptions" => array("srcformat" => 'Y-m-d', "newformat" => 'Y-m-d'));
}

function newSelectColumn($title, $name, $dbname, $data_table, &$grid, $custom_sql = "") {
    if ($custom_sql != "") {
        $select = $custom_sql;
    } elseif (substr($data_table, 0, 6) == "k8_top") {
        $select = "select distinct id as k, name as v from " . $data_table . " ORDER BY name ASC";
    } else {
        $select = "select distinct id as k, name as v from " . $data_table . " WHERE top_organization_id = "
                . $_SESSION['top_organization_id'] . " AND is_active = 1 ORDER BY name ASC";
    } return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, edittype => select,
        editoptions => array(value => "NULL:;" . $grid->get_dropdown_values($select)) );
}

function newCustomSelectColumn($title, $name, $dbname, $edit_values) {
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, edittype => select,
        editoptions => array(value => $edit_values));
}

function newCustomSelectColumnWithFormatter($title, $name, $dbname, $edit_values) {
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, edittype => select, stype => select,
        formatter => select, editoptions => array(value => $edit_values),
        searchoptions => array(value => "NULL:" . $_SESSION['main_language']->null . ";" . $edit_values));
}

function newMandatoryCustomSelectColumnWithFormatter($title, $name, $dbname, $edit_values) {
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, edittype => select, stype => select,
        formatter => select, editoptions => array(value => $edit_values), editrules => array(required => true),
        searchoptions => array(value => "NULL:" . $_SESSION['main_language']->null . ";" . $edit_values));
}

function newCheckColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 100,
        hidden => false, resizable => true, editable => true, edittype => select,
        formatter => select, stype => select, editoptions => array(value => "NULL:;1:"
            . $_SESSION['main_language']->yes . ";0:" . $_SESSION['main_language']->no . ""),
        searchoptions => array(value => "NULL:" . $_SESSION['main_language']->null . ";1:"
            . $_SESSION['main_language']->yes . ";0:" . $_SESSION['main_language']->no . ""));
}

function newCustomCheckColumn($title, $name, $dbname) {
    return array(title => $title, name => $name, dbname => $dbname, width => 100,
        hidden => false, resizable => true, editable => true, edittype => select,
        formatter => select, stype => select, editoptions => array(value => "NULL:;1:"
            . $_SESSION['main_language']->yes . ";0:" . $_SESSION['main_language']->no . ""),
        searchoptions => array(value => "NULL:" . $_SESSION['main_language']->null . ";1:"
            . $_SESSION['main_language']->yes . ";0:" . $_SESSION['main_language']->no . ""));
}

function newImageColumn($title, $dbname, $url) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    $html = "<img src='" . $url . "'>";
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => false, search => false,
        sortable => false, "default" => $html);
}

function newTextAreaColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 400,
        hidden => false, resizable => true, editable => true, edittype => "textarea");
}

function newMandatoryTextAreaColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 400,
        hidden => false, resizable => true, editable => true, edittype => "textarea", editrules => array(required => true));
}

function newMandatoryTextColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, editrules => array(required => true));
}

function newMandatoryCustomTextColumn($title, $name, $dbname) {
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, editrules => array(required => true));
}

function newMandatoryHiddenColumn($dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $name, name => $name, dbname => $dbname, width => 200,
        hidden => true, resizable => true, editable => true, editrules => array(required => true));
}

function newMandatoryPasswordColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, formatter => "password",
        edittype => "password", editrules => array(required => true));
}

function newMandatoryDateTimeColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, formatter => "datetime",
        editrules => array(required => true),
        "formatoptions" => array("srcformat" => 'Y-m-d H:i:s', "newformat" => 'Y-m-d H:i:s'));
}

function newMandatoryDateColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, formatter => "date",
        editrules => array(required => true),
        "formatoptions" => array("srcformat" => 'Y-m-d', "newformat" => 'Y-m-d'));
}
////////////////////////////////////////////////////////////////////////////
function newMandatorySelectColumnX($title, $name, $dbname,  $custom_sql ) {
 	$result= mysql_query($custom_sql);
	$temp="";
	$ss="";
	$arr=array();
	while( $row=mysql_fetch_assoc($result))
	{
		$temp=$temp.$row[$name].":".$row[$name].";";
	}
	if(strlen($temp)>0) $temp=substr($temp,0,strlen($temp)-1);
 	//	$temp="مصر:مصر;السعودية:السعودية";
	
		 return array(title => $title, name => $name, dbname => $dbname, width => 150,
        hidden => false, resizable => true, editable => true, edittype => select,
        formatter => select, stype => select, editoptions => array(value => $temp),
        searchoptions => array(value => $temp),
        editrules => array(required => false));
}

//////////////////////////////////////////////////////////////////

function newMandatorySelectColumn($title, $name, $dbname, $data_table, &$grid, $custom_sql = "") {
    if ($custom_sql != "") {
        $select = $custom_sql;
    } elseif (substr($data_table, 0, 6) == "k8_top") {
        $select = "select distinct id as k, name as v from " . $data_table . " ORDER BY name ASC";
    } else {
        $select = "select distinct id as k, name as v from " . $data_table .
                " WHERE top_organization_id = " . $_SESSION['top_organization_id']
                . " AND is_active = 1 ORDER BY name ASC";
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 200,
        hidden => false, resizable => true, editable => true, edittype => select,
        editoptions => array(value => $grid->get_dropdown_values($select)),
        editrules => array(required => true));
}

function newMandatoryCheckColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 100,
        hidden => false, resizable => true, editable => true, edittype => select,
        formatter => select, stype => select, editoptions => array(value => "1:" . $_SESSION['main_language']->yes . ";0:" . $_SESSION['main_language']->no . ""),
        searchoptions => array(value => "NULL:" . $_SESSION['main_language']->null . ";1:" . $_SESSION['main_language']->yes . ";0:" . $_SESSION['main_language']->no . ""),
        editrules => array(required => true));
}
function newMandatoryCheckColumnDefaultNo($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 100,
        hidden => false, resizable => true, editable => true, edittype => select,
        formatter => select, stype => select, editoptions => array(value => "0:" . $_SESSION['main_language']->no  . ";1:" .$_SESSION['main_language']->yes  . ""),
        searchoptions => array(value => "NULL:" . $_SESSION['main_language']->null . ";1:" . $_SESSION['main_language']->yes . ";0:" . $_SESSION['main_language']->no . ""),
        editrules => array(required => true));
}
function newIsActiveColumn($title, $dbname) {
    $substrings = explode(".", $dbname);
    if (count($substrings) > 1) {
        $name = $substrings[1];
    } else {
        $name = $dbname;
    }
    return array(title => $title, name => $name, dbname => $dbname, width => 100,
        hidden => false, resizable => true, editable => false, edittype => select,
        formatter => select, stype => select, editoptions => array(value => "1:" . $_SESSION['main_language']->yes . ";0:" . $_SESSION['main_language']->no . ""),
        searchoptions => array(value => "1:" . $_SESSION['main_language']->yes . ";0:" . $_SESSION['main_language']->no . "", "defaultValue" => '1', "searchhidden" => true,
            "sopt" => array("eq"))
        , editrules => array(required => true));
}

function addAutocompleteColumn(&$columns, $search_on, $update_col_name, $update_col_dbname, $title, $name, $dbname, $data_table, $condition = "") {
    if (substr($data_table, 0, 6) == "k8_top") {
        $autocomplete_sql = "SELECT id AS k, " . $search_on . " AS v FROM " . $data_table;
        if ($condition != ""){
            $autocomplete_sql .= " WHERE " . $condition;
        }
    } else {
        $autocomplete_sql = "SELECT id AS k, " . $search_on . " AS v FROM " . $data_table . " WHERE top_organization_id = "
                . $_SESSION['top_organization_id'] . " AND is_active = 1";
        if ($condition != ""){
            $autocomplete_sql .= " AND " . $condition;
        }
    }
    $columns[] = array(title => $title, name => $update_col_name, dbname => $update_col_dbname,
        hidden => true, editable => true, export=>false);
    $columns[] = array(title => $title, name => $name, dbname => $dbname, width => 300,
        hidden => false, resizable => true, editable => true, editrules => array(required => true),
        formatter => autocomplete, formatoptions => array("sql" => $autocomplete_sql,
            "search_on" => $search_on, "update_field" => $update_col_name));
}

function addMandatoryUploadImageColumns(&$columns, $title, $name, $dbname, $upload_dir = "uploads") {
    $columns[] = array(title => $title, name => $name, dbname => $dbname,
        edittype => "file", upload_dir => $upload_dir, editable => true, editrules => array("ifexist"=>"rename", required => true), 
        show => array("view" => false, "list"=>false,"edit"=>true,"add"=>true));
    $subfolderURL= getbaseURL0_orSubfolder1(1);
    $columns[] = array(title => $title, name => $name . "_display", dbname => $name . "_display", width => 300,
        hidden => false, resizable => true, editable => false,
        condition => array('$row["'.$name.'"] == ""', $_SESSION['main_language']->none, "<img height=100 src='{".$name."}'>"),
        show => array("view" => true, "list"=>true,"edit"=>false,"add"=>false), search => false, sortable => false);
		
}

function addUploadDocumentColumns(&$columns, $title, $name, $dbname, $upload_dir = "uploads") {
    $columns[] = array(title => $title, name => $name, dbname => $dbname,
        edittype => "file", upload_dir => "uploads", editable => true, editrules => array("ifexist"=>"rename", required => false), 
        show => array("view" => false, "list"=>false,"edit"=>true,"add"=>true));
    
    $columns[] = array(title => $title, name => $name . "_display", dbname => $name . "_display", width => 300,
        hidden => false, resizable => true, editable => false,
        condition => array('$row["'.$name.'"] == ""', $_SESSION['main_language']->none, "<a href='/{".$name."}' target='_blank'>" .$_SESSION['main_language']->download . "</a>"),
        show => array("view" => true, "list"=>true,"edit"=>false,"add"=>false), search => false, sortable => false);
}

function defaultImageUploadAddEventHandler($upload_file_path) {      
    if ($upload_file_path){ 
        $extension = pathinfo(realpath($upload_file_path), PATHINFO_EXTENSION); 
        if ($extension <> "jpeg" && $extension <> "gif" && $extension <> "jpg" && $extension <> "bmp" && $extension <> "png"){ 
            unlink(realpath($upload_file_path)); 
            phpgrid_error($_SESSION['main_language']->allowed_extentions_are_jpeg_jpg_png_gif_bmp_only); 
        }
        if (filesize(realpath($upload_file_path) > (200000000))){
            phpgrid_error($_SESSION['main_language']->file_size_should_not_exceed_200_kb);
        }
	

    }
	
} 

function defaultImageUploadUpdateEventHandler($upload_file_path, $id, $table, $column_name) {      
    if ($upload_file_path){ 
        if (filesize(realpath($upload_file_path) > (200000000))){
            phpgrid_error($_SESSION['main_language']->file_size_should_not_exceed_200_kb);
        }
        $extension = pathinfo(realpath($upload_file_path), PATHINFO_EXTENSION); 
        if ($extension <> "jpeg" && $extension <> "gif" && $extension <> "jpg" && $extension <> "bmp" && $extension <> "png"){ 
            unlink(realpath($upload_file_path)); 
            phpgrid_error($_SESSION['main_language']->allowed_extentions_are_jpeg_jpg_png_gif_bmp_only); 
        } 

    }
	

	
    $sql = "SELECT ".$column_name." FROM ".$table." WHERE id = ".$id . " "
            . "AND top_organization_id = ".$_SESSION['top_organization_id']." "
            . "AND is_active = 1";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row[$column_name] != $upload_file_path){
        unlink(realpath($row[$column_name])); 
    }
} 

function defaultImageUploadDeleteEventHandler($id, $table, $column_name) { 
    $sql = "SELECT ".$column_name." FROM ".$table." WHERE id = ".$id . " "
            . "AND top_organization_id = ".$_SESSION['top_organization_id']." "
            . "AND is_active = 1";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    unlink(realpath($row[$column_name])); 
} 

function defaultDocumentUploadAddEventHandler($upload_file_path) {      
    if ($upload_file_path){ 
        if (filesize(realpath($upload_file_path) > (1000000000))){
            phpgrid_error($_SESSION['main_language']->file_size_should_not_exceed_1_mb);
        }
    }
} 

function defaultDocumentUploadUpdateEventHandler($upload_file_path, $id, $table, $column_name) {      
    if ($upload_file_path){ 
        if (filesize(realpath($upload_file_path) > (1000000000))){
            phpgrid_error($_SESSION['main_language']->file_size_should_not_exceed_1_mb);
        }
    }
    $sql = "SELECT ".$column_name." FROM ".$table." WHERE id = ".$id . " "
            . "AND top_organization_id = ".$_SESSION['top_organization_id']." "
            . "AND is_active = 1";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row[$column_name] != $upload_file_path){
        unlink(realpath($row[$column_name])); 
    }
} 

function defaultDocumentUploadDeleteEventHandler($id, $table, $column_name) { 
    $sql = "SELECT ".$column_name." FROM ".$table." WHERE id = ".$id . " "
            . "AND top_organization_id = ".$_SESSION['top_organization_id']." "
            . "AND is_active = 1";
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    unlink(realpath($row[$column_name])); 
} 

function addDependantColumns(&$columns, $col1_title, $col1_name, $col1_dbname, $col2_title, $col2_name, 
        $col2_dbname, $col1_table, $col2_table, $foreign_key, $show_on_edit, &$grid, &$options, $required = "false") {

    $edit_values_update_sql = "SELECT distinct mt.id AS k, mt." . $show_on_edit
            . "  AS v FROM " . $col2_table . " mt "
            . " WHERE mt." . $foreign_key . " = '{" . $col1_name . "}' ";
    $edit_values_sql = "SELECT distinct mt.id AS k, mt." . $show_on_edit
            . "  AS v FROM " . $col2_table . " mt ";

    if (substr($data_table, 0, 6) != "k8_top") {
        $edit_values_update_sql .= " AND mt.top_organization_id = " . $_SESSION['top_organization_id']
                . " AND mt.is_active = 1 ORDER BY mt." . $show_on_edit . " ASC";
        $edit_values_sql .= " WHERE mt.top_organization_id = " . $_SESSION['top_organization_id']
                . " AND mt.is_active = 1 ORDER BY mt." . $show_on_edit . " ASC";
    }

    $edit_values = "NULL:" . $_SESSION['main_language']->null . ";" . $grid->get_dropdown_values($edit_values_sql);

    $new_column = newSelectColumn($col1_title, $col1_name, $col1_dbname, $col1_table, $grid);
    $new_column['editoptions']['onchange'] = array(sql => $edit_values_update_sql, update_field => $col2_name);
    $new_column["editrules"] = array("required" => $required);
    $columns[] = $new_column;

    $new_column = newCustomSelectColumn($col2_title, $col2_name, $col2_dbname, $edit_values);
    $new_column["editoptions"]["onload"]["sql"] = $edit_values_update_sql;
    $new_column["editrules"] = array("required" => $required);
    $columns[] = $new_column;

    $options["add_options"]["afterShowForm"] = "function(){ dependantColumnsShowHide(); }";
    $options["edit_options"]["afterShowForm"] = "function(){ dependantColumnsShowHide(); }";
    return dependantColumnsShowHideScript($col1_name, $col2_name);
}

function dependantColumnsShowHideScript($col1_name, $col2_name) {
    $script = "
                if ($('#" . $col1_name . "').val() == 'NULL')
                {
                        document.getElementById( 'tr_" . $col2_name . "' ).style.display = 'none';
                        document.getElementById( 'tr_" . $col2_name . "' ).value = 'NULL';
                }
               
                $('#" . $col1_name . "').change(function(){
                        if ($('#" . $col1_name . "').val() == 'NULL')
                        {
                                document.getElementById( 'tr_" . $col2_name . "' ).style.display = 'none';
                                document.getElementById( 'tr_" . $col2_name . "' ).value = 'NULL';
                        }
                        else
                        {
                                document.getElementById( 'tr_" . $col2_name . "' ).style.display = 'table-row';
                        }
                });
     ";
    return $script;
}

function createDependantColumnsShowHideScript($dependantColumnsShowHideScripts){
    $final_script = "
     <script>
        function dependantColumnsShowHide()
        {";
    foreach ($dependantColumnsShowHideScripts AS $script){
        $final_script .= " " . $script . " ";
    }
    $final_script .= "
        }
        </script>";
    return $final_script;
}

////custom code made by KCG
function initializejsgridfilterfield_value($grid_name, $column_name,$inital_val) {
    $script = "
        <script>

			var mygrid_id=$('#".$grid_name."')
			var grid_field_id=$('#".$column_name."')
			grid_field_id.val('".$inital_val."')
			mygrid_id.trigger('reloadGrid');
            
        </script>   
    ";
    return $script;
}
////end custom code made by KCG

function addThreeDependantColumns(&$columns, $col1_title, $col1_name, $col1_dbname, $col2_title, $col2_name, $col2_dbname, $col3_title, $col3_name, $col3_dbname, $col1_table, $col2_table, $col3_table, $foreign_key, $show_on_edit, &$grid, &$options, $required1 = "false", $required2 = "false", $required3 = "false", $custom_sql_for_first_column = "") {

    $edit_values_update_sql2 = "SELECT distinct mt.id AS k, mt." . $show_on_edit
            . "  AS v FROM " . $col2_table . " mt "
            . " WHERE mt." . $foreign_key . " = '{" . $col1_name . "}' ";
    $edit_values_sql2 = "SELECT distinct mt.id AS k, mt." . $show_on_edit
            . "  AS v FROM " . $col2_table . " mt ";
    $edit_values_update_sql3 = "SELECT distinct mt.id AS k, mt." . $show_on_edit
            . "  AS v FROM " . $col3_table . " mt "
            . " WHERE mt." . $foreign_key . " = '{" . $col1_name . "}' ";
    $edit_values_sql3 = "SELECT distinct mt.id AS k, mt." . $show_on_edit
            . "  AS v FROM " . $col3_table . " mt ";

    if (substr($data_table, 0, 6) != "k8_top") {
        $edit_values_update_sql2 .= " AND mt.top_organization_id = " . $_SESSION['top_organization_id']
                . " AND mt.is_active = 1 ORDER BY mt.name ASC";
        $edit_values_sql2 .= " WHERE mt.top_organization_id = " . $_SESSION['top_organization_id']
                . " AND mt.is_active = 1 ORDER BY mt.name ASC";
        $edit_values_update_sql3 .= " AND mt.top_organization_id = " . $_SESSION['top_organization_id']
                . " AND mt.is_active = 1 ORDER BY mt.name ASC";
        $edit_values_sql3 .= " WHERE mt.top_organization_id = " . $_SESSION['top_organization_id']
                . " AND mt.is_active = 1 ORDER BY mt.name ASC";
    }

    $edit_values2 = "NULL:" . $_SESSION['main_language']->null . ";" . $grid->get_dropdown_values($edit_values_sql2);
    $edit_values3 = "NULL:" . $_SESSION['main_language']->null . ";" . $grid->get_dropdown_values($edit_values_sql3);

    $new_column = newSelectColumn($col1_title, $col1_name, $col1_dbname, $col1_table, $grid, $custom_sql_for_first_column);
    $new_column['editoptions']['onchange'] = array(sql => $edit_values_update_sql2, callback => "update_fields");
    $new_column["editrules"] = array("required" => $required1);
    $columns[] = $new_column;

    $new_column = newCustomSelectColumn($col2_title, $col2_name, $col2_dbname, $edit_values2);
    $new_column["editoptions"]["onload"]["sql"] = $edit_values_update_sql2;
    $new_column["editrules"] = array("required" => $required2);
    $columns[] = $new_column;

    $new_column = newCustomSelectColumn($col3_title, $col3_name, $col3_dbname, $edit_values3);
    $new_column["editoptions"]["onload"]["sql"] = $edit_values_update_sql3;
    $new_column["editrules"] = array("required" => $required3);
    $columns[] = $new_column;

    $options["add_options"]["afterShowForm"] = "function(){ dependantColumnsShowHide_" . $col1_name . $col2_name . $col3_name . "(); }";
    $options["edit_options"]["afterShowForm"] = "function(){ dependantColumnsShowHide_" . $col1_name . $col2_name . $col3_name . "(); }";
}

function threeDependantColumnsShowHideScript($col1_name, $col2_name, $col3_name) {
    $script = "
     <script>
        function dependantColumnsShowHide_" . $col1_name . $col2_name . $col3_name . "()
        {
                if ($('#" . $col1_name . "').val() == 'NULL')
                {
                        document.getElementById( 'tr_" . $col2_name . "' ).style.display = 'none';
                        document.getElementById( 'tr_" . $col2_name . "' ).value = 'NULL';
                        document.getElementById( 'tr_" . $col3_name . "' ).style.display = 'none';
                        document.getElementById( 'tr_" . $col3_name . "' ).value = 'NULL';
                }
               
                $('#" . $col1_name . "').change(function(){
                        if ($('#" . $col1_name . "').val() == 'NULL')
                        {
                                document.getElementById( 'tr_" . $col2_name . "' ).style.display = 'none';
                                document.getElementById( 'tr_" . $col2_name . "' ).value = 'NULL';
                                document.getElementById( 'tr_" . $col3_name . "' ).style.display = 'none';
                                document.getElementById( 'tr_" . $col3_name . "' ).value = 'NULL';
                        }
                        else
                        {
                                document.getElementById( 'tr_" . $col2_name . "' ).style.display = 'table-row';
                                document.getElementById( 'tr_" . $col3_name . "' ).style.display = 'table-row';
                        }
                });
        }
        
        function update_fields() {   
            o = jQuery('select[name=" . $col2_name . "]').get(); 
            o.event = 'onload'; fx_get_dropdown(o,'" . $col2_name . "');         

            o = jQuery('select[name=" . $col3_name . "]').get(); 
            o.event = 'onload'; fx_get_dropdown(o,'" . $col3_name . "');     
        } 
        </script>
        
     ";
    return $script;
}

function addDefaultColumns(&$columns, &$grid) {
    $new_column = newTextAreaColumn($_SESSION['main_language']->comments, "mt.comments");
    $columns[] = $new_column;

    $new_column = newDateTimeColumn($_SESSION['main_language']->created, "mt.created");
    $new_column["editable"] = "false";
    $new_column['export'] = false;
    $columns[] = $new_column;

    $new_column = newSelectColumn($_SESSION['main_language']->created_by, "createdby_id", "crea.name", "k8_user", $grid);
    $new_column["editable"] = "false";
    $new_column['export'] = false;
    $columns[] = $new_column;

    $new_column = newDateTimeColumn($_SESSION['main_language']->modified, "mt.modified");
    $new_column["editable"] = "false";
    $new_column['export'] = false;
    $columns[] = $new_column;

    $new_column = newSelectColumn($_SESSION['main_language']->modified_by, "modifiedby_id", "modi.name", "k8_user", $grid);
    $new_column["editable"] = "false";
    $new_column['export'] = false;
    $columns[] = $new_column;

    $new_column = newHiddenColumn("u.top_organization_id");
    $new_column["editable"] = "false";
    $new_column['export'] = false;
    $columns[] = $new_column;

    $new_column = newIsActiveColumn($_SESSION['main_language']->is_active, "mt.is_active");
    $new_column["editable"] = "false";
    $new_column['export'] = false;
    $columns[] = $new_column;
}

function userHasAccess($top_page_id) {
    $sql = "    SELECT ra.top_page_id, ra.top_role_access_type_id from k8_role_access ra
      		INNER JOIN k8_user u ON u.role_id = ra.role_id 
      		WHERE u.id = " . $_SESSION['id'] . "
      		AND ra.top_page_id = " . $top_page_id . "
      		AND ra.top_organization_id = " . $_SESSION['top_organization_id'];
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $count = mysql_num_rows($result);
    $row = mysql_fetch_assoc($result);
    $_SESSION['top_role_access_type_id'] = $row['top_role_access_type_id'];
    return $count == "1";
}

function defaultInsertEventHandler(&$data) {
    $data['params']['top_organization_id'] = $_SESSION['top_organization_id'];
    $data["params"]["created_by"] = $_SESSION['id'];
    $data["params"]["created"] = getFormatedDateTime();
}

function defaultAfterInsertEventHandler(&$data, $table) {
    $sql = "INSERT INTO k8_audit VALUES(NULL, '" . $_SESSION['id'] . "', '" . $_GET['page']
            . "', '" . $table . "', " . $data["id"] . ", '" . $_SESSION['main_language']->add . "', '" . prepareAuditData($data)
            . "', '" . getFormatedDateTime() . "', '" . $_SESSION['timezone'] . "')";
    if (!mysql_query($sql)) { error_log($sql); }
}

function defaultUpdateEventHandler(&$data, $table) {
    $sql = "SELECT mt.is_active AS is_active FROM " . $table . " mt WHERE id = " . $data['id']
            . " AND mt.top_organization_id = " . $_SESSION['top_organization_id'];
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row['is_active'] == '0') {
        phpgrid_error($_SESSION['main_language']->cannot_edit_deleted_records);
    }
    $data["params"]["modified_by"] = $_SESSION['id'];
    $data["params"]["modified"] = getFormatedDateTime();
    $sql = "INSERT INTO k8_audit VALUES(NULL, '" . $_SESSION['id'] . "', '" . $_GET['page']
            . "', '" . $table . "', " . $data["id"] . ", '" . $_SESSION['main_language']->edit . "', '" . prepareAuditData($data)
            . "', '" . getFormatedDateTime() . "', '" . $_SESSION['timezone'] . "')";
    if (!mysql_query($sql)) { error_log($sql); }
}

function defaultDeleteEventHandler(&$data, $table) {
    $sql = "SELECT mt.is_active AS is_active FROM " . $table . " mt WHERE id = " . $data['id']
            . " AND top_organization_id = " . $_SESSION['top_organization_id'];
    if ($result = mysql_query($sql)) {}else { error_log($sql);}
    $row = mysql_fetch_assoc($result);
    if ($row['is_active'] == '0') {
        phpgrid_error($_SESSION['main_language']->record_already_deleted);
    }
    $sql = "UPDATE " . $table . " SET is_active = 0 WHERE id = " . $data["id"];
    if (!mysql_query($sql)) { error_log($sql); }
    $sql = "INSERT INTO k8_audit VALUES(NULL, '" . $_SESSION['id'] . "', '" . $_GET['page']
            . "', '" . $table . "', " . $data["id"] . ", '" . $_SESSION['main_language']->delete . "', '" . prepareAuditData($data)
            . "', '" . getFormatedDateTime() . "', '" . $_SESSION['timezone'] . "')";
    if (!mysql_query($sql)) { error_log($sql); }
}

function prepareAuditData($data) {
    $output = var_export($data, true);
    $output = preg_replace('/\s+/', '', $output);
    $words_to_replace = array("'", ",", "params=>", "array", ")", "(");
    $words_to_replace_with = array("", " | ", "", "", "", "");
    $output = str_replace($words_to_replace, $words_to_replace_with, $output);
    $output = str_replace(" |  | ", " |", $output);
    $output = str_replace("=>", " : ", $output);
    $output = str_replace("|", ",", $output);
    return rtrim($output, " ,");
}

function getDefaultActions($export_enabled = true) {
    switch ($_SESSION['top_role_access_type_id'] % 10) {
        case 1:
            return array("add" => false, "edit" => false, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);
        case 2:
            return array("add" => true, "edit" => false, "delete" => false, "rowactions" => false,
                "export" => $export_enabled, "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);
        case 3:
            return array("add" => true, "edit" => true, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);
        case 4:
            return array("add" => true, "edit" => false, "delete" => true, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);
        case 5:
            return array("add" => true, "edit" => true, "delete" => true, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);
        case 6:
            return array("add" => false, "edit" => true, "delete" => true, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);
        case 7:
            return array("add" => false, "edit" => true, "delete" => false, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);
        case 8:
            return array("add" => false, "edit" => false, "delete" => true, "rowactions" => false,
                "autofilter" => true, "search" => "advance", 
                "export_excel" => $export_enabled, "export_csv" => $export_enabled);
    }
}

function getDefaultOptions($caption) {
    $options = getDefaultOptionsWithoutSearch($caption);
    $filters = <<< SEARCH_JSON
{ 
    "groupOp":"AND",
    "rules":[
      {"field":"is_active","op":"eq","data":"1"}
     ]
}
SEARCH_JSON;
    $options["search"] = true;
    //$options["postData"] = array("filters" => $filters);
    return $options;
}

function getDefaultOptionsWithoutSearch($caption) {
    $options = array(
        caption => $caption,
        shrinkToFit => false,
        height => 350,
        autowidth => true,
        rowNum => 50,
        multiselect => false,
        ignoreCase => true,
        resizable => false,
        hiddengrid => false,
        sortname => id,
        view_options => array(width => 500, modal => false),
        export => array( "filename" => $caption, "sheetname" => $caption, "heading"=> $caption, 
            "orientation" => "landscape", "paper" => "a4", range => filtered),
        sortorder => desc,
        cellEdit => false,
        form => array(nav => true),
        edit_options => array(reloadAfterSubmit => true, width => "600px", modal => false, 
            success_msg_bulk => $_SESSION['main_language']->edit_success,
            success_msg => $_SESSION['main_language']->edit_success,
            afterShowForm => "function(){ jQuery('input').attr('autocomplete','off'); }"),
        add_options => array(reloadAfterSubmit => true, width => "600px", modal => false, 
            success_msg => $_SESSION['main_language']->add_success,
            afterShowForm => "function(){ jQuery('input').attr('autocomplete','off'); }"),
        delete_options => array(success_msg => $_SESSION['main_language']->delete_success),
        form => array(position => "left"),
        loadtext => $_SESSION['main_language']->load_text
    );
    if ($_SESSION['main_language']->lang_system_direction == "rtl") {
        $options["direction"] = "rtl";
        $options["form"] = array(position => "center");
    }
    return $options;
}

function displayGrid(&$grid, $table, $select, $columns, $options, $actions, $events, $conditions, $error_message = "") {
    $grid->table = $table;
    $grid->select_command = $select;
    $grid->set_columns($columns);
    $grid->set_options($options);
    $grid->set_actions($actions);
    $grid->set_events($events);
    $grid->set_conditional_css($conditions);
    $out = $grid->render("list");
    startPage();
    if ($error_message != "") {
        echo "<p class='error_message'>" . $error_message . "</p>";
    }
    echo $out;
    endPage();
}

function displayTwoGrids(&$g1, $table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1, &$g2, $table2, $select2, $columns2, $options2, $actions2, $events2, $conditions2, $error_message = "") {
    $g1->table = $table1;
    $g1->select_command = $select1;
    $g1->set_columns($columns1);
    $options1["subgridparams"] = "id";
    $options1["detail_grid_id"] = "list2";
    $options1["height"] = "200";
    $g1->set_options($options1);
    $g1->set_actions($actions1);
    $g1->set_events($events1);
    $g1->set_conditional_css($conditions1);
    $g2->table = $table2;
    $g2->select_command = $select2;
    $g2->set_columns($columns2);
    $options2["height"] = "200";
    $options2['caption'] = $options1['caption'] . " > " . $options2['caption'];
    $g2->set_options($options2);
    $g2->set_actions($actions2);
    $g2->set_events($events2);
    $g2->set_conditional_css($conditions2);
    $out1 = $g1->render("list1");
    $out2 = $g2->render("list2");
    startPage();
    if ($error_message != "") {
        echo "<p class='error_message'>" . $error_message . "</p>";
    }
    echo $out1;
    echo "<br/>";
    echo $out2;
    endPage();
}

function displayTwoIndependentGrids(&$g1, $table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1, &$g2, $table2, $select2, $columns2, $options2, $actions2, $events2, $conditions2, $error_message = "") {
    $g1->table = $table1;
    $g1->select_command = $select1;
    $g1->set_columns($columns1);
    $options1["height"] = "200";
    $g1->set_options($options1);
    $g1->set_actions($actions1);
    $g1->set_events($events1);
    $g1->set_conditional_css($conditions1);
    $g2->table = $table2;
    $g2->select_command = $select2;
    $g2->set_columns($columns2);
    $options2["height"] = "200";
    $g2->set_options($options2);
    $g2->set_actions($actions2);
    $g2->set_events($events2);
    $g2->set_conditional_css($conditions2);
    $out1 = $g1->render("list1");
    $out2 = $g2->render("list2");
    startPage();
    if ($error_message != "") {
        echo "<p class='error_message'>" . $error_message . "</p>";
    }
    echo $out1;
    echo "<br/>";
    echo $out2;
    endPage();
}

function displayThreeGrids(&$g1, $table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1, &$g2, $table2, $select2, $columns2, $options2, $actions2, $events2, $conditions2, &$g3, $table3, $select3, $columns3, $options3, $actions3, $events3, $conditions3, $error_message = "") {
    $g1->table = $table1;
    $g1->select_command = $select1;
    $g1->set_columns($columns1);
    $options1["subgridparams"] = "id";
    $options1["detail_grid_id"] = "list2";
    $options1["height"] = "200";
    $g1->set_options($options1);
    $g1->set_actions($actions1);
    $g1->set_events($events1);
    $g1->set_conditional_css($conditions1);
    $g2->table = $table2;
    $g2->select_command = $select2;
    $g2->set_columns($columns2);
    $options2["subgridparams"] = "id";
    $options2["detail_grid_id"] = "list3";
    $options2["height"] = "200";
    $options2['caption'] = $options1['caption'] . " > " . $options2['caption'];
    $g2->set_options($options2);
    $g2->set_actions($actions2);
    $g2->set_events($events2);
    $g2->set_conditional_css($conditions2);
    $g3->table = $table3;
    $g3->select_command = $select3;
    $g3->set_columns($columns3);
    $options3["height"] = "150";
    $options3['caption'] = $options2['caption'] . " > " . $options3['caption'];
    $g3->set_options($options3);
    $g3->set_actions($actions3);
    $g3->set_events($events3);
    $g3->set_conditional_css($conditions3);
    $out1 = $g1->render("list1");
    $out2 = $g2->render("list2");
    $out3 = $g3->render("list3");
    startPage();
    if ($error_message != "") {
        echo "<p class='error_message'>" . $error_message . "</p>";
    }
    echo $out1;
    echo "<br/>";
    echo $out2;
    echo "<br/>";
    echo $out3;
    endPage();
}

function displayThreeGridsNoParent(&$g1, $table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1, &$g2, $table2, $select2, $columns2, $options2, $actions2, $events2, $conditions2, &$g3, $table3, $select3, $columns3, $options3, $actions3, $events3, $conditions3, $error_message = "") {
    $g1->table = $table1;
    $g1->select_command = $select1;
    $g1->set_columns($columns1);
    $options1["height"] = "200";
    $g1->set_options($options1);
    $g1->set_actions($actions1);
    $g1->set_events($events1);
    $g1->set_conditional_css($conditions1);
    $g2->table = $table2;
    $g2->select_command = $select2;
    $g2->set_columns($columns2);
    $options2["height"] = "200";
    $g2->set_options($options2);
    $g2->set_actions($actions2);
    $g2->set_events($events2);
    $g2->set_conditional_css($conditions2);
    $g3->table = $table3;
    $g3->select_command = $select3;
    $g3->set_columns($columns3);
    $options3["height"] = "200";
    $g3->set_options($options3);
    $g3->set_actions($actions3);
    $g3->set_events($events3);
    $g3->set_conditional_css($conditions3);
    $out1 = $g1->render("list1");
    $out2 = $g2->render("list2");
    $out3 = $g3->render("list3");
    startPage();
    if ($error_message != "") {
        echo "<p class='error_message'>" . $error_message . "</p>";
    }
    echo $out1;
    echo "<br/>";
    echo $out2;
    echo "<br/>";
    echo $out3;
    endPage();
}

function displayThreeGridsOneParent(&$g1, $table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1, &$g2, $table2, $select2, $columns2, $options2, $actions2, $events2, $conditions2, &$g3, $table3, $select3, $columns3, $options3, $actions3, $events3, $conditions3, $error_message = "") {
    $g1->table = $table1;
    $g1->select_command = $select1;
    $g1->set_columns($columns1);
    $options1["subgridparams"] = "id";
    $options1["detail_grid_id"] = "list2,list3";
    $options1["height"] = "200";
    $g1->set_options($options1);
    $g1->set_actions($actions1);
    $g1->set_events($events1);
    $g1->set_conditional_css($conditions1);

    $g2->table = $table2;
    $g2->select_command = $select2;
    $g2->set_columns($columns2);
    $options2["height"] = "150";
    $options2['caption'] = $options1['caption'] . " > " . $options2['caption'];
    $g2->set_options($options2);
    $g2->set_actions($actions2);
    $g2->set_events($events2);
    $g2->set_conditional_css($conditions2);

    $g3->table = $table3;
    $g3->select_command = $select3;
    $g3->set_columns($columns3);
    $options3["height"] = "150";
    $options3['caption'] = $options1['caption'] . " > " . $options3['caption'];
    $g3->set_options($options3);
    $g3->set_actions($actions3);
    $g3->set_events($events3);
    $g3->set_conditional_css($conditions3);

    $out1 = $g1->render("list1");
    $out2 = $g2->render("list2");
    $out3 = $g3->render("list3");
    startPage();
    if ($error_message != "") {
        echo "<p class='error_message'>" . $error_message . "</p>";
    }
    echo $out1;
    echo "<br/>";
    echo $out2;
    echo "<br/>";
    echo $out3;
    endPage();
}

function displayFourGridsOneParent(&$g1, $table1, $select1, $columns1, $options1, $actions1, $events1, $conditions1, &$g2, $table2, $select2, $columns2, $options2, $actions2, $events2, $conditions2, &$g3, $table3, $select3, $columns3, $options3, $actions3, $events3, $conditions3, &$g4, $table4, $select4, $columns4, $options4, $actions4, $events4, $conditions4, $error_message = "") {
    $g1->table = $table1;
    $g1->select_command = $select1;
    $g1->set_columns($columns1);
    $options1["subgridparams"] = "id";
    $options1["detail_grid_id"] = "list2,list3,list4";
    $options1["height"] = "200";
    $g1->set_options($options1);
    $g1->set_actions($actions1);
    $g1->set_events($events1);
    $g1->set_conditional_css($conditions1);

    $g2->table = $table2;
    $g2->select_command = $select2;
    $g2->set_columns($columns2);
    $options2["height"] = "150";
    $options2['caption'] = $options1['caption'] . " > " . $options2['caption'];
    $g2->set_options($options2);
    $g2->set_actions($actions2);
    $g2->set_events($events2);
    $g2->set_conditional_css($conditions2);

    $g3->table = $table3;
    $g3->select_command = $select3;
    $g3->set_columns($columns3);
    $options3["height"] = "150";
    $options3['caption'] = $options1['caption'] . " > " . $options3['caption'];
    $g3->set_options($options3);
    $g3->set_actions($actions3);
    $g3->set_events($events3);
    $g3->set_conditional_css($conditions3);

    $g4->table = $table4;
    $g4->select_command = $select4;
    $g4->set_columns($columns4);
    $options4["height"] = "150";
    $options4['caption'] = $options1['caption'] . " > " . $options4['caption'];
    $g4->set_options($options4);
    $g4->set_actions($actions4);
    $g4->set_events($events4);
    $g4->set_conditional_css($conditions4);

    $out1 = $g1->render("list1");
    $out2 = $g2->render("list2");
    $out3 = $g3->render("list3");
    $out4 = $g4->render("list4");

    startPage();
    if ($error_message != "") {
        echo "<p class='error_message'>" . $error_message . "</p>";
    }
    echo $out1;
    echo "<br/>";
    echo $out2;
    echo "<br/>";
    echo $out3;
    echo "<br/>";
    echo $out4;
    endPage();
}

function getDefaultEvents($number = "") {
    return array(
        on_insert => array("insertRowInG" . $number, null, true),
        on_after_insert => array("afterInsertRowInG" . $number, null, true),
        on_after_update => array("afterUpdateRowInG" . $number, null, true),
        on_update => array("updateRowInG" . $number, null, true),
        on_delete => array("deleteRowInG" . $number, null, false)
    );
}

function isNumber($string) {
    return (string) (double) $string == $string;
}

function isPositiveNumber($string) {
    return isNumber($string) && (double) $string > 0;
}

function isPositiveNumberOrZero($string) {
    return isNumber($string) && (double) $string >= 0;
}

function isNegativeNumber($string) {
    return isNumber($string) && (double) $string < 0;
}

function refreshParentGridAfterAnyAction(&$options) {
    $options["delete_options"]["afterSubmit"] = "function(){jQuery('#list1').trigger('reloadGrid',[{jqgrid_page:1}]); return [true, ''];}";
    $options["edit_options"]["afterSubmit"] = "function(){jQuery('#list1').trigger('reloadGrid',[{jqgrid_page:1}]); return [true, ''];}";
    $options["add_options"]["afterSubmit"] = "function(){jQuery('#list1').trigger('reloadGrid',[{jqgrid_page:1}]); return [true, ''];}";
}

function userHasEexecutePrivilege() {
    return $_SESSION['top_role_access_type_id'] > 20;
}

function isPastRegardlessTime($time) {
    $date_from_time_string = substr($time, 0, strpos($time, ' '));
    $date = getFormatedDateTime();
    $current_date_string = substr($date, 0, strpos($date, ' '));
    return (strtotime($date_from_time_string) < strtotime($current_date_string));
}

function isFutureRegardlessTime($time) {
    $date_from_time_string = substr($time, 0, strpos($time, ' '));
    $date = getFormatedDateTime();
    $current_date_string = substr($date, 0, strpos($date, ' '));
    return (strtotime($date_from_time_string) > strtotime($current_date_string));
}

function getNumberWithoutSign($number) {
    if ($number < 0) {
        return 0 - $number;
    } else {
        return $number;
    }
}

function footerRowScript($list_number, $column_name) {
    $script = "
        <script>
            var opts = { 'loadComplete': function () {
                var grid = $('#list" . $list_number . "');
                sum = grid.jqGrid('getCol', '" . $column_name . "');
                if (sum == '') sum = '0';
                grid.jqGrid('footerData','set', {id:' " . $_SESSION['main_language']->total . " : '+sum[0]});
                $('#gbox_list" . $list_number . " tr.footrow td').hide();
                $('#gbox_list" . $list_number . " tr.footrow td:eq(0)').show();
            }}
        </script>   
    ";
    return $script;
}

function addToDate($date, $days) {
    $date = strtotime("+".$days." days", strtotime($date));
    return  date("Y-m-d", $date);
}

function addToDateTime($datetime, $minutes) {
    $date = strtotime("+".$minutes." minutes", strtotime($date));
    return  date("Y-m-d H:i:s", $date);
}

function subFromDate($date, $days) {
    $date = strtotime("-".$days." days", strtotime($date));
    return  date("Y-m-d", $date);
}

function subFromDateTime($datetime, $days) {
    $date = strtotime("-".$days." minutes", strtotime($date));
    return  date("Y-m-d H:i:s", $date);
}

function disableEditingInAllColumns($columns) {
    $new_columns = array();
    foreach ($columns AS $col) {
        $col['editable'] = false;
        $new_columns[] = $col;
    }
    return $new_columns;
}

function disableEditingInAllColumnsExceptOne($columns, $name) {
    $new_columns = array();
    foreach ($columns AS $col) {
        if ($col['name'] != $name) {
            $col['editable'] = false;
        }
        $new_columns[] = $col;
    }
    return $new_columns;
}

function disableEditingInAllColumnsExceptTwo($columns, $name1, $name2) {
    $new_columns = array();
    foreach ($columns AS $col) {
        if ($col['name'] != $name1 && $col['name'] != $name2) {
            $col['editable'] = false;
        }
        $new_columns[] = $col;
    }
    return $new_columns;
}

function getYearFromDate($date){
    return date("Y", strtotime($date));
}

function getMonthFromDate($date){
    return date("m", strtotime($date));
}
/////newly added function for getting base url 
function getbaseURL0_orSubfolder1($option01) {
	if($option01==1){
		    $sql = "SELECT URLsubfolder from configuration";
		if ($result = mysql_query($sql)) {}else { error_log($sql);}
		$row = mysql_fetch_assoc($result);
		$returnval=$row['URLsubfolder'];
		$_SESSION['URLsubfolder'] = $row['URLsubfolder'];
	}else if($option01==0){
		$sql = "SELECT baseUrl from configuration";
		if ($result = mysql_query($sql)) {}else { error_log($sql);}
		$row = mysql_fetch_assoc($result);
		$returnval=$row['baseUrl'];
		$_SESSION['baseUrl'] = $row['baseUrl'];
	}
    return $returnval;
}
function UpdateProductStock($id,$quantity,$addsub)
{
	$sql="SELECT stock from product where id=".$id;
	$result = mysql_query($sql); 
	$row = mysql_fetch_assoc($result);
	$stock=intval($row['stock']);
	if(intval($addsub)>0) $stock= $stock+intval($quantity);
	else $stock= $stock-intval($quantity);
		
		$sqll="update product set stock=".$stock." WHERE id=".$id;
		$res = mysql_query($sqll); 
		
	
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php 
        global $secret_key;
        if (isset($_SESSION['secret']) && $_SESSION['secret'] == $secret_key){
            echo $_SESSION["page_title"] . " | " . $_SESSION["top_organization_name"]; 
        }else {
            echo "Back Office | Be3li MLM | Cloud System";
        }
        ?></title>
        <meta name="Keywords" content="Be3li Company, Retail Cloud System, eCommerce by Khalifa Computer Group" />
        <meta name="Description" content="Back Office ERP for MLM & Small Business, Powered by Khalifa Computer Group" />
        <link rel="shortcut icon" href="media/favicon.ico" type="image/x-icon" />
        <meta charset="UTF-8">
            
            <link rel="stylesheet" type="text/css" media="screen" href="
                  <?php echo $_SESSION['top_theme_url']; ?>/jquery-ui.custom.css"></link>     
            <link rel="stylesheet" type="text/css" media="screen" 
                  href="includes/lib/js/jqgrid/css/ui.jqgrid.css"></link>   
            <link href='css/menu.css' rel='stylesheet' type='text/css'></link> 
            <link href='css/grid.css' rel='stylesheet' type='text/css'></link>  
            <link href='css/style.css' rel='stylesheet' type='text/css'></link>
            <link href='css/mobile.css' rel='stylesheet' type='text/css'></link>
            <?php if ($_SESSION['main_language']->lang_system_direction == "rtl") {
                ?><link href='css/rtl.css' rel='stylesheet' type='text/css'><?php } ?>
                <link href='<?php echo $_SESSION['top_theme_url']; ?>/theme.css' rel='stylesheet' 
                      type='text/css'>

                    <!--<script src="includes/lib/js/jquery.min.js" type="text/javascript"></script> -->	
					<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
					<script src="https://code.jquery.com/jquery-migrate-1.4.1.min.js"></script>
                    <script src="includes/lib/js/jqgrid/js/i18n/<?php echo $_SESSION['grid_locale_file']; ?>" 
                    type="text/javascript"></script> 
                    <script src="includes/lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript">
                    </script>     
                    <script src="includes/lib/js/themes/jquery-ui.custom.min.js" type="text/javascript">
                    </script>
                    </head>
                    <body>
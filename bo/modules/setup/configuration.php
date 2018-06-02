<?php
startPage();
?>
<div class="col1">
    <!--------------------------------Base URL----------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['baseUrl']) && $_POST['secret'] == "4unf9unufru49fnr91") {
        if ($_POST['baseUrl'] == "" ) {
            echo "<p class='error_message'>Base URL could not be empty it should be http://domainOrIP/system_folder.</p>";
        } else {
            $sql = "UPDATE configuration SET baseUrl = '" . $_POST['baseUrl'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT baseUrl FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Base URL (http://domain.com_OR_ip): 
            <span class="mandatory">*</span></label>
        <input type="text" name="baseUrl" value="<?php echo $row['baseUrl']; ?>" >
        <input type="hidden" name="secret" value="4unf9unufru49fnr91">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <!--------------------------------Subfolder of Base URL----------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['URLsubfolder']) && $_POST['secret'] == "4unf9unufru49fnr91") {
        if ($_POST['URLsubfolder'] == "" ) {
            echo "<p class='error_message'>Base URL could not be empty it should be http://domainOrIP/system_folder.</p>";
        } else {
            $sql = "UPDATE configuration SET URLsubfolder = '" . $_POST['URLsubfolder'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT URLsubfolder FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Base URL (http://xxxxcom/subfolder/) -> Subfolder (SUBFOLDER): 
            <span class="mandatory">*</span></label>
        <input type="text" name="URLsubfolder" value="<?php echo $row['URLsubfolder']; ?>" >
        <input type="hidden" name="secret" value="4unf9unufru49fnr91">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['registration_fees']) && $_POST['secret'] == "4unf9unufru49fnr9") {
        if ($_POST['registration_fees'] == "" || !(isPositiveNumber($_POST['registration_fees']))) {
            echo "<p class='error_message'>Registration Fees must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET registration_fees = '" . $_POST['registration_fees'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT registration_fees FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Registration Fees: 
            <span class="mandatory">*</span></label>
        <input type="text" name="registration_fees" value="<?php echo $row['registration_fees']; ?>" >
        <input type="hidden" name="secret" value="4unf9unufru49fnr9">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['dcp_limit']) && $_POST['secret'] == "nfyryb4fy48bf84rfb84bf") {
        if ($_POST['dcp_limit'] == "" || !(isPositiveNumber($_POST['dcp_limit']))) {
            echo "<p class='error_message'>DCP Limit must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET dcp_limit = '" . $_POST['dcp_limit'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT dcp_limit FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >DCP Limit: 
            <span class="mandatory">*</span></label>
        <input type="text" name="dcp_limit" value="<?php echo $row['dcp_limit']; ?>" >
        <input type="hidden" name="secret" value="nfyryb4fy48bf84rfb84bf">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
	<!------------------------------------------------------------------------------------------------->
	
    <?php
    if (isset($_POST) && isset($_POST['new_reg_com']) && $_POST['secret'] == "sdhfbsdifbsidfbiu38fn") {
        if ($_POST['new_reg_com'] == "" || !(isPositiveNumber($_POST['new_reg_com']))) {
            echo "<p class='error_message'>Referral Registraion Commission must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET new_reg_com = '" . $_POST['new_reg_com'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT new_reg_com FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Referral New Registraion Commission: 
            <span class="mandatory">*</span></label>
        <input type="text" name="new_reg_com" value="<?php echo $row['new_reg_com']; ?>" >
        <input type="hidden" name="secret" value="sdhfbsdifbsidfbiu38fn">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
		<!------------------------------------------------------------------------------------------------->
	
    <?php
    if (isset($_POST) && isset($_POST['retail_qualify_com']) && $_POST['secret'] == "sdhfbsdifbsidfbiu38fn") {
		
        if ($_POST['retail_qualify_com'] == "" || !(isPositiveNumber($_POST['retail_qualify_com']))) {
            echo "<p class='error_message'>Referral Qualifying Retail Commission must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET retail_qualify_com = '" . $_POST['retail_qualify_com'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT retail_qualify_com FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Referral Down IR Qualify Retail Commission: 
            <span class="mandatory">*</span></label>
        <input type="text" name="retail_qualify_com" value="<?php echo $row['retail_qualify_com']; ?>" >
        <input type="hidden" name="secret" value="sdhfbsdifbsidfbiu38fn">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <!------------------------------------------------------------------------------------------------->
	
    <?php
    if (isset($_POST) && isset($_POST['retail_parent_commission']) && $_POST['secret'] == "sdhfbsdifbsidfbiu38fn") {
        if ($_POST['retail_parent_commission'] == "" || !(isPositiveNumber($_POST['retail_parent_commission']))) {
            echo "<p class='error_message'>Retail Parent Commission must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET retail_parent_commission = '" . $_POST['retail_parent_commission'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT retail_parent_commission FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Retail Parent Commission: 
            <span class="mandatory">*</span></label>
        <input type="text" name="retail_parent_commission" value="<?php echo $row['retail_parent_commission']; ?>" >
        <input type="hidden" name="secret" value="sdhfbsdifbsidfbiu38fn">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['binary_qualify_fees001']) && $_POST['secret'] == "8ybd8ybdf83ybf38ybf2e2edsss") {
        //if ($_POST['binary_qualify_fees001'] == "" || !(isPositiveNumber($_POST['binary_qualify_fees001']))) {
		if ($_POST['binary_qualify_fees001'] == "" || ($_POST['binary_qualify_fees001']<0)) {
		  echo "<p class='error_message'>Binary Qualification Fees 001 must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET binary_qualify_fees001 = '" . $_POST['binary_qualify_fees001'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT binary_qualify_fees001 FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Binary Qualification Fees 001: 
            <span class="mandatory">*</span></label>
        <input type="text" name="binary_qualify_fees001" value="<?php echo $row['binary_qualify_fees001']; ?>" >
        <input type="hidden" name="secret" value="8ybd8ybdf83ybf38ybf2e2edsss">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['binary_qualify_fees002']) && $_POST['secret'] == "38bef38beu93ube93ubef93ubf9u3bf") {
        //if ($_POST['binary_qualify_fees002'] == "" || !(isPositiveNumber($_POST['binary_qualify_fees002']))) {
			if ($_POST['binary_qualify_fees002'] == "" || ($_POST['binary_qualify_fees002']<0)) {
            echo "<p class='error_message'>Binary Qualification Fees 002 must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET binary_qualify_fees002 = '" . $_POST['binary_qualify_fees002'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT binary_qualify_fees002 FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Binary Qualification Fees 002: 
            <span class="mandatory">*</span></label>
        <input type="text" name="binary_qualify_fees002" value="<?php echo $row['binary_qualify_fees002']; ?>" >
        <input type="hidden" name="secret" value="38bef38beu93ube93ubef93ubf9u3bf">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['binary_qualify_fees003']) && $_POST['secret'] == "hdifbe9d8hbe9hbe9hfb") {
       // if ($_POST['binary_qualify_fees003'] == "" || !(isPositiveNumber($_POST['binary_qualify_fees003']))) {
		if ($_POST['binary_qualify_fees003'] == "" || ($_POST['binary_qualify_fees003']<0)) {
            echo "<p class='error_message'>Binary Qualification Fees 003 must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET binary_qualify_fees003 = '" . $_POST['binary_qualify_fees003'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT binary_qualify_fees003 FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Binary Qualification Fees 003: 
            <span class="mandatory">*</span></label>
        <input type="text" name="binary_qualify_fees003" value="<?php echo $row['binary_qualify_fees003']; ?>" >
        <input type="hidden" name="secret" value="hdifbe9d8hbe9hbe9hfb">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['email_signature']) && $_POST['secret'] == "93yrbf3bhf93hbe9f3nefxxx-") {
        $sql = "UPDATE configuration SET email_signature = '" . $_POST['email_signature'] . "'";
        if (!mysql_query($sql)) {
            error_log($sql);
        }
    }
    $sql = "SELECT email_signature FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Email Signature: 
            <span class="mandatory">*</span></label>
        <textarea name="email_signature"><?php echo $row['email_signature']; ?></textarea>
        <input type="hidden" name="secret" value="93yrbf3bhf93hbe9f3nefxxx-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->



    <!----------------------------------------------------------------------------------------------------->
</div>
<div class="col2">
    <!----------------------------------------------------------------------------------------------------->



    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['retail_qualify_fees']) && $_POST['secret'] == "88uc8ud8cw8chwudb2234") {
        if ($_POST['retail_qualify_fees'] == "" || !(isPositiveNumber($_POST['retail_qualify_fees']))) {
            echo "<p class='error_message'>Retail Qualification Fees must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET retail_qualify_fees = '" . $_POST['retail_qualify_fees'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT retail_qualify_fees FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Retail Qualification Fees: 
            <span class="mandatory">*</span></label>
        <input type="text" name="retail_qualify_fees" value="<?php echo $row['retail_qualify_fees']; ?>" >
        <input type="hidden" name="secret" value="88uc8ud8cw8chwudb2234">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['renewal_fees']) && $_POST['secret'] == "83ybf83ybef8y3bef8yb3ef") {
        if ($_POST['renewal_fees'] == "" || !(isPositiveNumber($_POST['renewal_fees']))) {
            echo "<p class='error_message'>Renewal Fees must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET renewal_fees = '" . $_POST['renewal_fees'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT renewal_fees FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Renewal Fees: 
            <span class="mandatory">*</span></label>
        <input type="text" name="renewal_fees" value="<?php echo $row['renewal_fees']; ?>" >
        <input type="hidden" name="secret" value="83ybf83ybef8y3bef8yb3ef">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['loyalty_start']) && $_POST['secret'] == "6edvgcbedbyfe8d9edbw9999") {
        if ($_POST['loyalty_start'] == "") {
            echo "<p class='error_message'>Incentive Start cannot be left blank.</p>";
        } else {
            $sql = "UPDATE configuration SET loyalty_start = '" . $_POST['loyalty_start'] . "'";
            if ($result = mysql_query($sql)) {
                
            } else {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT loyalty_start FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Incentive Start Date:  (ex: 2015-12-01)
            <span class="mandatory">*</span></label>
        <input type="text" name="loyalty_start" value="<?php echo $row['loyalty_start']; ?>" >
        <input type="hidden" name="secret" value="6edvgcbedbyfe8d9edbw9999">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['loyalty_end']) && $_POST['secret'] == "88ffff8f8f8f8fs8fs8dsd") {
        if ($_POST['loyalty_end'] == "") {
            echo "<p class='error_message'>Incentive End cannot be left blank.</p>";
        } else {
            $sql = "UPDATE configuration SET loyalty_end = '" . $_POST['loyalty_end'] . "'";
            if ($result = mysql_query($sql)) {
                
            } else {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT loyalty_end FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Incentive End Date: (ex: 2016-03-31)
            <span class="mandatory">*</span></label>
        <input type="text" name="loyalty_end" value="<?php echo $row['loyalty_end']; ?>" >
        <input type="hidden" name="secret" value="88ffff8f8f8f8fs8fs8dsd">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['loyalty_target']) && $_POST['secret'] == "38yfb38eybf83yefb38eyr777e") {
        if ($_POST['loyalty_target'] == "" || !(isPositiveNumber($_POST['loyalty_target']))) {
            echo "<p class='error_message'>Incentive Target must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET loyalty_target = '" . $_POST['loyalty_target'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT loyalty_target FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Incentive Target: 
            <span class="mandatory">*</span></label>
        <input type="text" name="loyalty_target" value="<?php echo $row['loyalty_target']; ?>" >
        <input type="hidden" name="secret" value="38yfb38eybf83yefb38eyr777e">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['loyalty_reward']) && $_POST['secret'] == "4484923-485757383") {
        if ($_POST['loyalty_reward'] == "" || !(isPositiveNumber($_POST['loyalty_reward']))) {
            echo "<p class='error_message'>Incentive Reward must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET loyalty_reward = '" . $_POST['loyalty_reward'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT loyalty_reward FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Incentive Reward: 
            <span class="mandatory">*</span></label>
        <input type="text" name="loyalty_reward" value="<?php echo $row['loyalty_reward']; ?>" >
        <input type="hidden" name="secret" value="4484923-485757383">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
    <?php
    if (isset($_POST) && isset($_POST['loyalty_delay']) && $_POST['secret'] == "38rybf83yrbf9y3rbf9u3brf-") {
        if ($_POST['loyalty_delay'] == "" || !(isPositiveNumber($_POST['loyalty_delay']))) {
            echo "<p class='error_message'>Incentive Delay must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET loyalty_delay = '" . $_POST['loyalty_delay'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT loyalty_delay FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Incentive Delay (days): 
            <span class="mandatory">*</span></label>
        <input type="text" name="loyalty_delay" value="<?php echo $row['loyalty_delay']; ?>" >
        <input type="hidden" name="secret" value="38rybf83yrbf9y3rbf9u3brf-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
	<?php
    if (isset($_POST) && isset($_POST['nodaystopayretailcom']) && $_POST['secret'] == "38rybf83yrbf9y3rbf9u3brf-") {
        if ($_POST['nodaystopayretailcom'] == "" || !(isPositiveNumber($_POST['nodaystopayretailcom']))) {
            echo "<p class='error_message'>Incentive Delay must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET nodaystopayretailcom = '" . $_POST['nodaystopayretailcom'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT nodaystopayretailcom FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Number of days to pay the retail commission since order: 
            <span class="mandatory">*</span></label>
        <input type="text" name="nodaystopayretailcom" value="<?php echo $row['nodaystopayretailcom']; ?>" >
        <input type="hidden" name="secret" value="38rybf83yrbf9y3rbf9u3brf-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
	<?php
    if (isset($_POST) && isset($_POST['binarycheckval']) && $_POST['secret'] == "38rybf83yrbf9y3rbf9u3brf-") {
        if ($_POST['binarycheckval'] == "" || !(isPositiveNumber($_POST['binarycheckval']))) {
            echo "<p class='error_message'>Incentive Delay must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET binarycheckval = '" . $_POST['binarycheckval'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT binarycheckval FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Step Check Value (binary): 
            <span class="mandatory">*</span></label>
        <input type="text" name="binarycheckval" value="<?php echo $row['binarycheckval']; ?>" >
        <input type="hidden" name="secret" value="38rybf83yrbf9y3rbf9u3brf-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
	<?php
    if (isset($_POST) && isset($_POST['dcpts_exchrate']) && $_POST['secret'] == "38rybf83yrbf9y3rbf9u3brf-") {
        if ($_POST['dcpts_exchrate'] == "" || !(isPositiveNumber($_POST['dcpts_exchrate']))) {
            echo "<p class='error_message'>Incentive Delay must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET dcpts_exchrate = '" . $_POST['dcpts_exchrate'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT dcpts_exchrate FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Value of each DCpts to E-currency (Exchange Rate): 
            <span class="mandatory">*</span></label>
        <input type="text" name="dcpts_exchrate" value="<?php echo $row['dcpts_exchrate']; ?>" >
        <input type="hidden" name="secret" value="38rybf83yrbf9y3rbf9u3brf-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
	<?php
    if (isset($_POST) && isset($_POST['orderstatuspayretailcomm']) && $_POST['secret'] == "38rybf83yrbf9y3rbf9u3brf-") {
        if ($_POST['orderstatuspayretailcomm'] == "" || !(isPositiveNumber($_POST['orderstatuspayretailcomm']))) {
            echo "<p class='error_message'>Incentive Delay must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET orderstatuspayretailcomm = '" . $_POST['orderstatuspayretailcomm'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT orderstatuspayretailcomm FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Order Status to Pay Retail Commissions e.g.(Delivered): 
            <span class="mandatory">*</span></label>
        <input type="text" name="orderstatuspayretailcomm" value="<?php echo $row['orderstatuspayretailcomm']; ?>" >
        <input type="hidden" name="secret" value="38rybf83yrbf9y3rbf9u3brf-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
	<?php
    if (isset($_POST) && isset($_POST['onelegbalancedpoints']) && $_POST['secret'] == "38rybf83yrbf9y3rbf9u3brf-") {
        if ($_POST['onelegbalancedpoints'] == "" || !(isPositiveNumber($_POST['onelegbalancedpoints']))) {
            echo "<p class='error_message'>Incentive Delay must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET onelegbalancedpoints = '" . $_POST['onelegbalancedpoints'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT onelegbalancedpoints FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Binary Codition for Balanced Leg count (3:3) : 
            <span class="mandatory">*</span></label>
        <input type="text" name="onelegbalancedpoints" value="<?php echo $row['onelegbalancedpoints']; ?>" >
        <input type="hidden" name="secret" value="38rybf83yrbf9y3rbf9u3brf-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
	<?php
    if (isset($_POST) && isset($_POST['dynamicflush_val']) && $_POST['secret'] == "38rybf83yrbf9y3rbf9u3brf-") {
        if ($_POST['dynamicflush_val'] == "" || !(isPositiveNumber($_POST['dynamicflush_val']))) {
            echo "<p class='error_message'>Incentive Delay must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET dynamicflush_val = '" . $_POST['dynamicflush_val'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT dynamicflush_val FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Dynamic Flush Condition for Lower Leg Count : 
            <span class="mandatory">*</span></label>
        <input type="text" name="dynamicflush_val" value="<?php echo $row['dynamicflush_val']; ?>" >
        <input type="hidden" name="secret" value="38rybf83yrbf9y3rbf9u3brf-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
	<?php
    if (isset($_POST) && isset($_POST['cycleRedimtionPoints']) && $_POST['secret'] == "38rybf83yrbf9y3rbf9u3brf-") {
        if ($_POST['cycleRedimtionPoints'] == "" || !(isPositiveNumber($_POST['cycleRedimtionPoints']))) {
            echo "<p class='error_message'>Incentive Delay must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET cycleRedimtionPoints = '" . $_POST['cycleRedimtionPoints'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT cycleRedimtionPoints FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Number of Points to Give for one Cycle (last step5 points) : 
            <span class="mandatory">*</span></label>
        <input type="text" name="cycleRedimtionPoints" value="<?php echo $row['cycleRedimtionPoints']; ?>" >
        <input type="hidden" name="secret" value="38rybf83yrbf9y3rbf9u3brf-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
	<?php
    if (isset($_POST) && isset($_POST['maxNoSteps']) && $_POST['secret'] == "38rybf83yrbf9y3rbf9u3brf-") {
        if ($_POST['maxNoSteps'] == "" || !(isPositiveNumber($_POST['maxNoSteps']))) {
            echo "<p class='error_message'>Incentive Delay must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET maxNoSteps = '" . $_POST['maxNoSteps'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT maxNoSteps FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Maximum Number of Steps (No. checks + cycle) e.g (5): 
            <span class="mandatory">*</span></label>
        <input type="text" name="maxNoSteps" value="<?php echo $row['maxNoSteps']; ?>" >
        <input type="hidden" name="secret" value="38rybf83yrbf9y3rbf9u3brf-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
	<?php
    if (isset($_POST) && isset($_POST['dailycounterbackhistory']) && $_POST['secret'] == "38rybf83yrbf9y3rbf9u3brf-") {
        if ($_POST['dailycounterbackhistory'] == "" || !(isPositiveNumber($_POST['dailycounterbackhistory']))) {
            echo "<p class='error_message'>Incentive Delay must be a positive number.</p>";
        } else {
            $sql = "UPDATE configuration SET dailycounterbackhistory = '" . $_POST['dailycounterbackhistory'] . "'";
            if (!mysql_query($sql)) {
                error_log($sql);
            }
        }
    }
    $sql = "SELECT dailycounterbackhistory FROM configuration ";
    if ($result = mysql_query($sql)) {
        
    } else {
        error_log($sql);
    }
    $row = mysql_fetch_assoc($result);
    ?>
    <form method="post">
        <label >Maximum number of history days to keep in Daily Counter: 
            <span class="mandatory">*</span></label>
        <input type="text" name="dailycounterbackhistory" value="<?php echo $row['dailycounterbackhistory']; ?>" >
        <input type="hidden" name="secret" value="38rybf83yrbf9y3rbf9u3brf-">
        <button type="submit" class="ok"><?php echo $_SESSION['main_language']->update; ?></button>
        </br></br>
        <div class="sep"></div>
        </br>
    </form>
    <!------------------------------------------------------------------------------------------------->
</div>
<?php
endPage();
?>
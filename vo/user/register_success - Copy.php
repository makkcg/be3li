<?php
if (!isset($_GET['new_ir_id'])) {
    header("Location: index.php?page=register_first_step");
}
$sql = "SELECT r.title, r.f_name, r.l_name, b.referral_bu_id, r.a_name, r.registration_date, r.ir_id , CONCAT(referral.title, ' ', referral.f_name, ' ', referral.l_name ) AS referral_name, crd.cardnumber FROM ir r "
        . " LEFT OUTER JOIN bu b ON (b.code = '001' AND b.ir_id = r.ir_id) "
		. " INNER JOIN scrachcards crd ON (crd.irid = r.ir_id) "
        . " LEFT OUTER JOIN ir referral ON referral.ir_id = SUBSTRING(b.referral_bu_id,1,8) "
        . " WHERE r.ir_id = '".$_GET['new_ir_id']."'";
$result = $database_manager->query($sql);
$row = mysqli_fetch_assoc($result);

$html_page->writeHeader();

?>

<div id="left-container">
    <div id="logo">
        <a href="index.php?page=dashboard"><img src="images/testlogo.png" style="width: 90%;"></a>
    </div>
</div>
<div id="right-container">
    <div id="top-bar">
    </div>
    <div id="header">
        <div id="page-title">
            <h1>Registration: Success</h1>
        </div>
        <div id="header-menu">
        </div>
    </div>
    <div id="page">

        <h2 style="color: #d79928; font-size: 30px;">Congratulations…</h2>
        <h2>Dear <?php echo $row['title'] . " " . $row['f_name'] . " " . $row['l_name']; ?>,</h2>
        <h2>Welcome to Company.</h2>
        <p>Your virtual office (VO) registration details for your IRship are:</p>

        <table class="order-info">
            <tbody>
                <tr>
                    <th>
                        Your IR ID: 
                    </th>
                    <td>
                        <?php echo $row['ir_id']; ?>
                    </td>
                    <th>
                        Your Referrer IR ID & BU: 
                    </th>
                    <td>
                        <?php echo $row['referral_bu_id']; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Your Name: 
                    </th>
                    <td>
                        <?php echo $row['f_name'] . " " . $row['l_name']; ?>
                    </td>
                    <th>
                        Your Referrer Name: 
                    </th>
                    <td>
                        <?php echo $row['referral_name']; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Your Arabic Name: 
                    </th>
                    <td>
                        <?php echo $row['a_name']; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Date of Registration: 
                    </th>
                    <td>
                        <?php echo $row['registration_date']; ?>
                    </td>
                </tr>
				<tr>
                    <th>
                        Your Visa/debit Card Number: 
                    </th>
                    <td>
                        <?php echo $row['cardnumber']; ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Registration Fees: 
                    </th>
                    <td>
                        PAID for ONE year
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="sep"></div>
        
        <p>We are pleased to welcome you as a new IR of Company. We feel honored that you have chosen us to satisfy your needs and to achieve your dreams through Company opportunity, our slogan is Changing People Lives for better.</p>
        <p>Everything your Virtual Office may need is carried to manage your Shop, we have a great variety of products & services to choose from and to deal with, all at competitive prices. It is our privilege to serve you and to provide you with our best possible care.</p>
        <p>So, we are committed to optimizing our VO to provide the best service that allows our IRs improve their business. We appreciate the trust placed in us and we hope to establish a strong long term business relationship with you.</p>

        <div class="sep dotted"></div>
        <div class="sep"></div>
        <a class="button" href="index.php?page=login">Login Now</a>
        
        
<?php $html_page->writeFooter(); ?>
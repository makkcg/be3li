<style>
    table, th, tr, td, p {
        direction: rtl!important;
        text-align: right!important;
    }
</style>

<?php
include "printouts/printouts_helper.php";

$maintenance_id = $_GET['id'];

$sql = " SELECT p.name AS partner_name, p.tel1 AS partner_tel1, p.tel2 AS partner_tel2, rb.name AS received_by_name, "
        . " mt.product AS product, mt.initial_cost AS cost, mt.problem AS problem, mt.product_details AS product_details, "
        . " mt.date AS date, mt.case_number AS case_number, "
        
        . " mt.bag AS bag, mt.charger AS charger, mt.power_cable AS power_cable, mt.hdd AS hdd, "
        . " mt.battery AS battery, mt.adapter AS adapter, mt.data_cable AS data_cable, mt.cover AS cover  "
        
        . " FROM k8_maintenance mt "
        . " LEFT OUTER JOIN k8_partner p ON p.id = mt.partner_id "
        . " LEFT OUTER JOIN k8_user rb ON rb.id = mt.received_by_id "
        . " WHERE mt.id = " . $maintenance_id;
if ($result = mysql_query($sql)) {}else { error_log($sql);}
$row = mysql_fetch_assoc($result);

$row_organization = getOrganizationRow($_SESSION['top_organization_id']);
$row_branch = getBranchRow($row_organization['o_main_branch_id']);

$addition_items = "";

if ($row['bag'] == "1"){
    $addition_items .= "شنطه" . " , ";
}
if ($row['charger'] == "1"){
    $addition_items .= "شاحن" . " , ";
}
if ($row['power_cable'] == "1"){
    $addition_items .= "كابل كهرباء" . " , ";
}
if ($row['hdd'] == "1"){
    $addition_items .= "هارد" . " , ";
}
if ($row['battery'] == "1"){
    $addition_items .= "بطارية" . " , ";
}
if ($row['adapter'] == "1"){
    $addition_items .= "ادابتر" . " , ";
}
if ($row['data_cable'] == "1"){
    $addition_items .= "كابل بيانات" . " , ";
}
if ($row['cover'] == "1"){
    $addition_items .= "كفر" . " , ";
}

$addition_items = rtrim($addition_items, " , ");

?>

<div class="a4_col1">
    <table>
        <tr>
            <th>اسم العميل  : </th>
            <td><?php echo $row['partner_name']; ?></td>
        </tr>
        <tr>
            <th>تليفون ١ : </th>
            <td><?php echo $row['partner_tel1']; ?></td>
        </tr>
        <tr>
            <th>تليفون ٢ : </th>
            <td><?php echo $row['partner_tel2']; ?></td>
        </tr>
    </table>
</div>
<div class="a4_col2">
    <table>
        <tr>
            <th>التاريخ/الوقت : </th>
            <td><?php echo $row['date']; ?></td>
        </tr>
        <tr>
            <th>رقم # : </th>
            <td><?php echo $row['case_number']; ?></td>
        </tr>
    </table>
    <table>
        <tr>
            <th>
                <?php
                echo $row_organization['o_name'] . "</th></tr><tr><th>" . $row_branch['b_country'] . ", " . $row_branch['b_city'] . ", " .
                $row_branch['b_address1'] . " " . $row_branch['b_address2'];
                ?>
            </th>
        </tr>
    </table>
</div>

<div class="clear"></div><br/>

<table class="a4_lines">
    <tr>
        <td class="a4_small_cell">نوع الجهاز</td>
        <td class="a4_product"><?php echo $row['product']; ?></td>
    </tr>
    <tr>
        <td class="a4_small_cell">تفاصيل الجهاز</td>
        <td class="a4_product"><?php echo $row['product_details'] ?></td>
    </tr>
    <tr>
        <td class="a4_small_cell">مرفقات</td>
        <td class="a4_product"><?php echo $addition_items; ?></td>
    </tr>
    <tr>
        <td class="a4_small_cell">حالة الجهاز عند الإستلام (تقييم مبدئي )</td>
        <td class="a4_product"><?php echo $row['problem']; ?></td>
    </tr>
    <tr>
        <td class="a4_small_cell">التكلفة بناء على التفييم المبدئى</td>
        <td class="a4_product"><?php echo $row['cost']; ?> جنيه مصري</td>
    </tr>
    <tr>
        <td class="a4_small_cell">الموظف المسئول</td>
        <td class="a4_product"><?php echo $row['received_by_name']; ?></td>
    </tr>
</table>
<p style="direction: rtl;font-size: 12px;">
	-	فى حالة زيادة تكلفة التصليح عن المبلغ المقيم أعلاه. يتم الإتصال بالعميل و تعديل قيمة تكلفة التصليح قبل البدء فى أعمال الصيانة .
<br/>
	-	فى حالة إستلام الجهاز لا يعمل ( قاطع باور ) تكون الشركة غير مسئولة عن أى أعطال أخري .
<br/>
	-	مدة الضمان شهر من تاريخ استلام الجهاز (الضمان على صيانة الهارد وير و ليس السوفت وير ) . 
<br/>
	-	الضمان ضد عيوب الصناعة و ليس سوء الإستخدام .
<br/>
	-	الشركة غير مسئولة عن البيانات التى تكون موجودة على الأقراص الصلبة للأجهزة .
<br/>
	-	لا يحق للعميل طلب الجهاز بعد تركه لمدة شهر من تاريخ تحرير هذا الإيصال .
<br/>
	-	لا يتم تسليم الجهاز للعميل إلا بموجب هذا الإيصال .
<br/>
</p>
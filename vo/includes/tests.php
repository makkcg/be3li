<?php
include 'core.php';
include 'database_manager.php';
$database_manager = new DatabaseManager();
$core = new Core($database_manager);echo "<br>-------------------------------------------------------------------------------------<br>";$ref_ir="BE000010";$bu_arr=array("007");$_POST['position']="right";$_POST['business_unit']="007";echo "<p>Testing get first empty slot at ir=$ref_ir and bu=$bu_arr[0]  and position auto </p>";$dd=$core->searchEmpgySlotBelowIR($database_manager,$ref_ir,$bu_arr);if($dd){	echo "<p><strong>IRID </strong>".$dd['irid']."</p>";	echo "<p><strong>BU </strong>".$dd['bu']."</p>";	echo "<p><strong>Position to BU </strong>".$dd['position']."</p>";}elseif($dd==false){	echo "not found";}$referrer_ir_id=$ref_ir;$upline_bu_id = $core->calculateUpline($database_manager, $_POST['position'], $referrer_ir_id, $_POST['business_unit']);echo "<p><strong>Calc Upline IR-BU </strong>".$upline_bu_id."</p>";echo "<br>-------------------------------------------------------------------------------------<br>";$ref_ir="BE000010"; $bu_arr=array("005");$level=1;echo "<p>Testing get first empty slot at ir=$ref_ir and bu=$bu_arr[0] and level=$level and position auto </p>";$resultss=$core->findFreeSlotinDownlinelevels($database_manager,$ref_ir,$bu_arr,$level);//echo $resultss;//print_r($resultss);var_dump($resultss);echo "<br>-------------------------------------------------------------------------------------<br>";
/*
echo "<p>Testing generateNextIRID('AA0000') </p>";
echo $result = $core->generateNextIRID("AA0000");
$expected = "AA0001";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('AA0005') </p>";
echo $result = $core->generateNextIRID("AA0005");
$expected = "AA0006";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('AA0009') </p>";
echo $result = $core->generateNextIRID("AA0009");
$expected = "AA0010";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('AA0050') </p>";
echo $result = $core->generateNextIRID("AA0050");
$expected = "AA0051";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('AA0059') </p>";
echo $result = $core->generateNextIRID("AA0059");
$expected = "AA0060";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('AA0111') </p>";
echo $result = $core->generateNextIRID("AA0111");
$expected = "AA0112";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('AA0500') </p>";
echo $result = $core->generateNextIRID("AA0500");
$expected = "AA0501";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('AA0599') </p>";
echo $result = $core->generateNextIRID("AA0599");
$expected = "AA0600";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('AA5000') </p>";
echo $result = $core->generateNextIRID("AA5000");
$expected = "AA5001";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('AA5999') </p>";
echo $result = $core->generateNextIRID("AA5999");
$expected = "AA6000";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('AA9999') </p>";
echo $result = $core->generateNextIRID("AA9999");
$expected = "AB0000";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('BZ9999') </p>";
echo $result = $core->generateNextIRID("BZ9999");
$expected = "CA0000";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

echo "<p>Testing generateNextIRID('ZZ9999') </p>";
echo $result = $core->generateNextIRID("ZZ9999");
$expected = "AAA0000";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}




echo "<p>Testing calculateUpline('left, PA0102, 001') </p>";
echo $result = $core->calculateUpline($database_manager, "left", "PA0102", "001", 0);
$expected = "PA0103-002";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}


echo "<p>Testing calculateUpline('right, PA0102, 001') </p>";
echo $result = $core->calculateUpline($database_manager, "right", "PA0102", "001", 0);
$expected = "PA0164-003";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}

*/
/*
echo "<p>Testing calculateUpline('left, PA0102, 001') </p>";
echo $result = $core->calculateUpline($database_manager, "left", "PA0102", "001");
$expected = "PA0103-002";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}
*/
/*
echo "<p>Testing calculateUpline('right, PA0102, 001') </p>";
echo $result = $core->calculateUpline($database_manager, "right", "PA0102", "001");
$expected = "PA0164-003";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}
*/

/*
echo "<p>Testing calculateUpline('left, PA0100, 001') </p>";
echo $result = $core->calculateUpline($database_manager, "left", "PA0100", "001");
$expected = "PA0100-002";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}


echo "<p>Testing calculateUpline('left, PA0101, 003') </p>";
echo $result = $core->calculateUpline($database_manager, "left", "PA0101", "003");
$expected = "PA0144-002";
if ($result != $expected){
    echo "<p style='color: red;'>Test Failed, Expected: $expected</p>";
}*/
?>
<?php

require_once 'PopbillFax.php';

$LinkID = 'TESTER';
$SecretKey = 'okH3G1/WZ3w1PMjHDLaWdcWIa/dbTX3eGuqMZ5AvnDE=';

$FaxService = new FaxService($LinkID,$SecretKey);

$FaxService->IsTest(true);

$result = $FaxService->GetUnitCost('1231212312');

var_dump($result);
echo chr(10);

	
$Receivers = array();
	
$Receivers[] = array(
	'rcv' => '11112222',
	'rcvnm' => '수신자성명'
);
	
$Receivers[] = array(
	'rcv' => '11112222',
	'rcvnm' => '수신자성명'
);
	
	
$Files = array('./uploadtest.jpg','./uploadtest.jpg');
	
$ReserveDT = null; //예약전송시 예약시간 yyyyMMddHHmmss 형식
$UserID = 'userid'; //팝빌 사용자 아이디
	
$result = $FaxService->SendFAX('1231212312','07075106766',$Receivers,$Files,$ReserveDT,$UserID);

var_dump($result);
echo chr(10);

$ReceiptNum = '014042117224400001';

$result = $FaxService->GetFaxDetail('1231212312',$ReceiptNum,'userid');

var_dump($result);

echo chr(10);



$result = $FaxService->CancelReserve('1231212312',$ReceiptNum,'userid');

var_dump($result);

echo chr(10);

$result = $FaxService->GetURL('1231212312','userid','BOX');

var_dump($result);

echo chr(10);

?>

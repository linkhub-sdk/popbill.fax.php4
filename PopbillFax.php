<?php
/**
* =====================================================================================
* Class for base module for Popbill API SDK. It include base functionality for
* RESTful web service request and parse json result. It uses Linkhub module
* to accomplish authentication APIs.
*
* This module uses curl and openssl for HTTPS Request. So related modules must
* be installed and enabled.
*
* http://www.linkhub.co.kr
* Author : Kim Seongjun (pallet027@gmail.com)
* Written : 2014-06-23
*
* Thanks for your interest.
* We welcome any suggestions, feedbacks, blames or anything.
* ======================================================================================
*/
require_once 'Popbill/popbill.php';

class FaxService extends PopbillBase {
	
	function FaxService($LinkID,$SecretKey) {
    	parent::PopbillBase($LinkID,$SecretKey);
    	$this->AddScope('160');
    }
    
    //발행단가 확인
    function GetUnitCost($CorpNum) {
    	$result = $this->executeCURL('/FAX/UnitCost', $CorpNum);
    	
    	if(is_a($result,'PopbillException')) return $result;
    	
    	return $result->unitCost;
    }

	/* 팩스 전송 요청
    *	$CorpNum => 발송사업자번호
    *	$Sender	=> 발신번호
    *	$Receviers => 수신처 목록
    *		'rcv'	=> 수신번호
    *		'rcvnm'	=> 수신자 명칭
    *	$FilePaths	=> 전송할 파일경로 문자열 목록, 최대 5개.
    *	$ReserveDT	=> 예약전송을 할경우 전송예약시간 yyyyMMddHHmmss 형식
    *	$UserID	=> 팝빌 회원아이디
    */
	function SendFAX($CorpNum,$Sender,$Receivers = array(),$FilePaths = array(),$ReserveDT = null,$UserID = null) {
		if(empty($Receivers)) {
			return new PopbillException('{"code" : -99999999 , "message" : "수신처 목록이 입력되지 않았습니다."}');
		}
		
		if(empty($FilePaths)) {
			return new PopbillException('{"code" : -99999999 , "message" : "발신파일 목록이 입력되지 않았습니다."}');
	}
		
		$RequestForm = array();
		
		$RequestForm['snd'] = $Sender;
		if(!empty($ReserveDT)) $RequestForm['sndDT'] = $ReserveDT;
		$RequestForm['fCnt'] = count($FilePaths);
		
		$RequestForm['rcvs'] = $Receivers;
	
    	$postdata = array();
    	$postdata['form'] = $this->Linkhub->json_encode($RequestForm);
    	
    	$i = 0;
    	
    	foreach($FilePaths as $FilePath) {
    		$postdata['file['.$i++.']'] = '@'.$FilePath;
    	}
    	
    	$result = $this->executeCURL('/FAX', $CorpNum, $UserID, true,null,$postdata,true);
    	if(is_a($result,'PopbillException')) return $result;
    	
    	return $result->receiptNum;
 		
	}
	
	/* 팩스 전송 내역 확인
    *	$CorpNum => 발송사업자번호
    *	$ReceiptNum	=> 접수번호
    *	$UserID	=> 팝빌 회원아이디
    */
	function GetFaxDetail($CorpNum,$ReceiptNum,$UserID) {
		if(empty($ReceiptNum)) {
			return new PopbillException('{"code" : -99999999 , "message" : "확인할 접수번호를 입력하지 않았습니다."}');
    	}
    	return $this->executeCURL('/FAX/'.$ReceiptNum, $CorpNum,$UserID);	
	}
	
    /* 예약전송 취소
    *	$CorpNum => 발송사업자번호
    *	$ReceiptNum	=> 접수번호
    *	$UserID	=> 팝빌 회원아이디
    */
    function CancelReserve($CorpNum,$ReceiptNum,$UserID) {
    	if(empty($ReceiptNum)) {
    		return new PopbillException('{"code" : -99999999 , "message" : "취소할 접수번호를 입력하지 않았습니다."}');
    	}
    	return $this->executeCURL('/FAX/'.$ReceiptNum.'/Cancel', $CorpNum,$UserID);
    }
    
   /* 팩스 관련 기능 URL 확인
    *	$CorpNum => 발송사업자번호
    *	$UserID	=> 팝빌 회원아이디
    *	$TOGO => URL 위치 아이디
    */
    function GetURL($CorpNum ,$UserID, $TOGO) {
    	$result = $this->executeCURL('/FAX/?TG='.$TOGO,$CorpNum,$UserID);
    	if(is_a($result,'PopbillException')) return $result;
    	return $result->url;
    }
}
?>

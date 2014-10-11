<?php

class ZDate extends DateTime{
	
	const FORM_DATE_FORMAT_PHP = 'd.m.Y';
	const FORM_DATETIME_FORMAT_PHP = 'd.m.Y H:i';
	const DB_DATETIME_FORMAT_PHP = 'Y-m-d H:i:s';
	const DB_DATE_FORMAT_PHP = 'Y-m-d';
	
	const FORM_DATE_FORMAT_YII = 'dd.MM.yyyy';
	const FORM_DATETIME_FORMAT_YII = 'dd.MM.yyyy HH:mm';
	const DB_DATETIME_FORMAT_YII = 'yyyy-MM-dd HH:mm:ss';
	const DB_DATE_FORMAT_YII = 'yyyy-MM-dd';
	
	const DATETIME_REGEX ="^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d.\d\d:\d\d^"; // [- /\.](0[1-9]|1[012])[- /\.](19|20)\d\d/";
	const DATE_REGEX ="^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d^";
	
	public function __construct($datetime){
		parent::__construct($datetime);
	}
	
	public static function translateFormat_php($strdate, $format){	
		if (!(trim($strdate))) return null; //vrne null če je prazen string
		elseif ($time=strtotime($strdate)) return date($format, $time); //vrne fomatiran niz
		else return $strdate; //če ne zna pretvoriti vrne isti niz
	} 
	public static function formDate_php($strdate){
		return ZDate::translateFormat_php($strdate, ZDate::FORM_DATE_FORMAT_PHP);
	}
	public static function formDateTime_php($strdate){
		return ZDate::translateFormat_php($strdate, ZDate::FORM_DATETIME_FORMAT_PHP);
	}
	public static function dbDateTime_php($strdate){
		return ZDate::translateFormat_php($strdate, ZDate::DB_DATETIME_FORMAT_PHP);
	}
	public static function dbDate_php($strdate){
		return ZDate::translateFormat_php($strdate, ZDate::DB_DATE_FORMAT_PHP);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param string $strdate Y-m-d H:i:s
	 * @param string $strtime H:i:s
	 */
//	public static function setTime($strdate, $strtime){
//		return substr($strdate, 0, 11).$strtime;
//	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param string $strdate Y-m-d H:i:s
	 * @return string $strtime
	 */
//	public static function getTime($strdate){
//		return substr($strdate, 11);
//	}
	
	public static function dbNow(){
		return date(self::DB_DATETIME_FORMAT_PHP, time());
	}
	
	public static function formNow(){
		return date(self::FORM_DATETIME_FORMAT_PHP, time());
	}
	
	public static function dbModify($strDate, $strModify){
		if($strDate=="") return null;
		$date = new DateTime($strDate);
		$date->modify($strModify);
		return $date->format(self::DB_DATETIME_FORMAT_PHP);
	}
	
	public static function parseDate(&$string, $format='dd. month yyyy'){
		//echo $string;
						 
		if ($format=='dd. month yyyy'){
			//$regex ="^(0[1-9]|[12][0-9]|3[01])/..[a-zA-Z]*.(19|20)\d\d^";
			$regex ="^(0[1-9]|[12][0-9]|3[01]|)\..?([a-zA-Z]*).((19|20)\d\d)^";
			if (preg_match($regex, $string, $matches))
        	return $matches[3].'-'.self::stMeseca($matches[2]).'-'.$matches[1];
		}
	}
	
	public static function MESECI(){
		return 	$meseci = array('01'=>'januar', 
						 '02'=>'februar',
						 '03'=> 'marec',
						 '04'=>'april',
						 '05'=>'maj',
						 '06'=>'junij',
						 '07'=>'julij',
						 '08'=>'avgust',
						 '09'=>'september',
						 '10'=>'oktober',
						 '11'=>'november',
						 '12'=>'december');
	}
	
	public static function MESECI_ROD(){
		return 	$meseci = array('01'=>'januarja', 
						 '02'=>'februarja',
						 '03'=> 'marca',
						 '04'=>'aprila',
						 '05'=>'maja',
						 '06'=>'junija',
						 '07'=>'julija',
						 '08'=>'avgusta',
						 '09'=>'septembra',
						 '10'=>'oktobra',
						 '11'=>'novembra',
						 '12'=>'decembra');
	}
	
	public static function MESECI_KRATKO(){
		return 	$meseci = array('01'=>'jan', 
						 '02'=>'feb',
						 '03'=> 'mar',
						 '04'=>'apr',
						 '05'=>'maj',
						 '06'=>'jun',
						 '07'=>'jul',
						 '08'=>'avg',
						 '09'=>'sep',
						 '10'=>'okt',
						 '11'=>'nov',
						 '12'=>'dec');
	}
	
	public static function stMeseca($imeMeseca){
		if($mesec=array_search($imeMeseca, self::MESECI())) return $mesec;
		if($mesec=array_search($imeMeseca, self::MESECI_ROD())) return $mesec;
		if($mesec=array_search($imeMeseca, self::MESECI_KRATKO())) return $mesec;
	}
	
	public function imeMesecaKratko(){
		$a=self::MESECI_KRATKO();
		return $a[$this->format('m')];
	}
	
	public static function imeMesecaKtatkoSt($stMeseca){
		$a=self::MESECI_KRATKO();
		return $a[$stMeseca];
	}
	public function imeMeseca(){
		$a=self::MESECI();
		return $a[$this->format('m')];
	}
	
	public static function imeMesecaSt($stMeseca){
		$a=self::MESECI();
		return $a[$stMeseca];
	}
	public function datumDB(){
		return $this->format(self::DB_DATE_FORMAT_PHP);
	}
	
	public function datum(){
		return $this->format(self::FORM_DATE_FORMAT_PHP);
	}
	
	public function ura(){
		$ura = $this->format('H:i');
		if($ura=="00:00") return false;
		else return $ura;
	}
	
	public function __toString(){
		return $this->datum().' '.$this->ura();
	}
	
}
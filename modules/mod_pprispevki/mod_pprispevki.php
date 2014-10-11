<?php defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Število povezanih prispevkov - glede na to koliko jih želimo imeti prikazanih.
$stPrispevkov = $params->get('stprispevkov');

// Jedro povezanih prispevkov.
require_once dirname(__FILE__).'/helper.php';

// Preverimo, če smo na posameznem prispevku in mu dodamo povezane oz. sorodne.
if(JRequest::getVar('prispevek')) {
	$PrispevekId = JRequest::getVar('prispevek');
	
	$Prispevek = new Prispevek($PrispevekId);
	$Povezani = new PovezaniPrispevki($Prispevek,$stPrispevkov);
}
else {
	$Povezani = array();
}

// Nastavi na privzeto predlogo.
require JModuleHelper::getLayoutPath('mod_pprispevki', $params->get('layout', 'default'));

?>

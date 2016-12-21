<?php
/**
 * ZuPago HyBrid (HD) Wallet payment plugin
 *
 *	@author    AKT
 *	@copyright 2016 AK & T Team. All rights reserved.
 *
 *	@license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 *	@version 1.0.1
 *
 *	International Registered Trademark & Property of PrestaShop SA
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/zupago.php');

$zupago = new ZuPago();

define('ZUPAGO_ALT',Configuration::get('ZUPAGO_ALTERNATE'));


$hash=strtoupper(md5($string));
$cart = new Cart(intval($_POST['PAYMENT_REF']));
$customer = new Customer(intval($cart->id_customer));

$currency = $zupago->getCurrency();

if(ZUPAGO_ALT==$_POST['ZUPAYEE_ACC_KEY']
	&& $_POST['ZUPAYEE_ACC']==Configuration::get('ZUPAGO_ACCOUNT')
	&& $_POST['PAYMENT_AMOUNT']==Tools::convertPrice($cart->getOrderTotal(true, 3), $currency)) {
		
	$zupago->validateOrder($_POST['PAYMENT_REF'], _PS_OS_PAYMENT_, $_POST['PAYMENT_AMOUNT'], $zupago->displayName);
}
 else 
 {
	$zupago->validateOrder($_POST['PAYMENT_REF'], _PS_OS_ERROR_, 0, $zupago->displayName, $errors.'<br />');
}
Tools::redirect('order-confirmation.php?id_cart='.$_POST['PAYMENT_REF'].'&key='.$customer->secure_key.'&id_module='.$zupago->id);
?>
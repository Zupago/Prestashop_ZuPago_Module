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

class ZuPago extends PaymentModule
{
	private	$_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'zupago';
		$this->tab = 'Payments';
		$this->version = '1.0.1';
		$this->author = 'AkT';
		
		$this->currencies = true;
		$this->currencies_mode = 'radio';

        parent::__construct();

		$this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('ZuPago HyBrid (HD) Wallet');
        $this->description = $this->l('Receive payments with ZuPago HyBrid (HD) Wallet');
		$this->confirmUninstall = $this->l('Are you sure you want to delete ZuPago HyBrid (HD) Wallet ?');
	}

	public function getZuPagoUrl()
	{
			return 'https://zupago.pe/api';
	}

	public function install()
	{
		if (!parent::install()
			OR !Configuration::updateValue('ZUPAGO_ACCOUNT', 'ZU-123456')
			OR !Configuration::updateValue('ZUPAGO_ACCOUNT_BTC', 'ZB-123456')
			OR !Configuration::updateValue('ZUPAGO_NAME', 'Your Bussiness Name')
			OR !Configuration::updateValue('ZUPAGO_API_KEY', 'Activate at Zupago account')
			OR !$this->registerHook('payment')
			OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('ZUPAGO_ACCOUNT')
			OR !Configuration::deleteByName('ZUPAGO_ACCOUNT_BTC')
			OR !Configuration::deleteByName('ZUPAGO_NAME')
			OR !Configuration::deleteByName('ZUPAGO_API_KEY')
			OR !parent::uninstall())
			return false;
		return true;
	}

	public function getContent()
	{
		$this->_html = '<h2>ZuPago HyBrid (HD) Wallet</h2>';
		if (isset($_POST['submitZuPago']))
		{
			if (empty($_POST['zupago_account']))
				$this->_postErrors[] = $this->l('ZuPago HyBrid (HD) Wallet USD account number is required.');
			if (empty($_POST['zupago_account_btc']))
				$this->_postErrors[] = $this->l('ZuPago HyBrid (HD) Wallet BTC account number is required.');	
			if (empty($_POST['zupago_api_key']))
				$this->_postErrors[] = $this->l('ZuPago HyBrid (HD) Wallet Api Key required.');
			if (!sizeof($this->_postErrors))
			{
				Configuration::updateValue('ZUPAGO_ACCOUNT', strval($_POST['zupago_account']));
				Configuration::updateValue('ZUPAGO_ACCOUNT_BTC', strval($_POST['zupago_account_btc']));
				Configuration::updateValue('ZUPAGO_NAME', strval($_POST['zupago_name']));
				Configuration::updateValue('ZUPAGO_ALTERNATE', strval($_POST['zupago_api_key']));
				$this->displayConf();
			}
			else
				$this->displayErrors();
		}

		$this->displayZuPago();
		$this->displayFormSettings();
		return $this->_html;
	}

	public function displayConf()
	{
		$this->_html .= '
		<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
			'.$this->l('Settings updated').'
		</div>';
	}

	public function displayErrors()
	{
		$nbErrors = sizeof($this->_postErrors);
		$this->_html .= '
		<div class="alert error">
			<h3>'.($nbErrors > 1 ? $this->l('There are') : $this->l('There is')).' '.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors') : $this->l('error')).'</h3>
			<ol>';
		foreach ($this->_postErrors AS $error)
			$this->_html .= '<li>'.$error.'</li>';
		$this->_html .= '
			</ol>
		</div>';
	}
	
	
	public function displayZuPago()
	{
		$this->_html .= '
		<div style="float: right; width: 440px; height: 150px; border: dashed 1px #666; padding: 8px; margin-left: 12px;">
			<h2>'.$this->l('Opening your ZuPago HyBrid (HD) Wallet account').'</h2>
			<div style="clear: both;"></div>
			<p>'.$this->l('By opening your ZuPago HyBrid (HD) Wallet account by clicking on the following image you are helping us identify and develop more modules for PrestaShop and ZuPago:').'</p>
			<p style="text-align: center;"><a href="https://zupago.pe/ref/Z4484971"><img src="../modules/zupago/views/img/prestashop_zupago.png" target="_BLANK" alt="PrestaShop & ZuPago" style="margin-top: 12px;" /></a></p>
			<div style="clear: right;"></div>
		</div>
		<img src="../modules/zupago/views/img/zupago.gif" style="float:left; margin-right:15px;" />
		<b>'.$this->l('This module allows you to receiver payments by ZuPago HyBrid (HD) Wallet.').'</b><br /><br />
		'.$this->l('If the client chooses this payment mode, your ZuPago HyBrid (HD) Wallet account will be automatically credited.').'<br />
		'.$this->l('You need to configure your ZuPago HyBrid (HD) Wallet account first before using this module.').'
		<div style="clear:both;">&nbsp;</div>';
	}

	public function displayFormSettings()
	{
		$conf = Configuration::getMultiple(array('ZUPAGO_ACCOUNT','ZUPAGO_ACCOUNT_BTC','ZUPAGO_NAME','ZUPAGO_ALTERNATE'));
		$zupago_account = array_key_exists('zupago_account', $_POST) ? $_POST['zupago_account'] : (array_key_exists('ZUPAGO_ACCOUNT', $conf) ? $conf['ZUPAGO_ACCOUNT'] : '');
		$zupago_account_btc = array_key_exists('zupago_account_btc', $_POST) ? $_POST['zupago_account_btc'] : (array_key_exists('ZUPAGO_ACCOUNT_BTC', $conf) ? $conf['ZUPAGO_ACCOUNT_BTC'] : '');
		$zupago_name = array_key_exists('zupago_name', $_POST) ? $_POST['zupago_name'] : (array_key_exists('ZUPAGO_NAME', $conf) ? $conf['ZUPAGO_NAME'] : '');
		$zupago_api_key = array_key_exists('zupago_api_key', $_POST) ? $_POST['zupago_api_key'] : (array_key_exists('ZUPAGO_ALTERNATE', $conf) ? $conf['ZUPAGO_ALTERNATE'] : '');

		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post" style="clear: both;">
		<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Settings').'</legend>
			<label>'.$this->l('ZUPAGO USD Account').'</label>
			<div class="margin-form"><input type="text" size="33" name="zupago_account" value="'.htmlentities($zupago_account, ENT_COMPAT, 'UTF-8').'" /></div><br/>
			<label>'.$this->l('ZUPAGO BTC Account').'</label>
			<div class="margin-form"><input type="text" size="33" name="zupago_account_btc" value="'.htmlentities($zupago_account_btc, ENT_COMPAT, 'UTF-8').'" /></div><br/>
			<label>'.$this->l('Merchant Name').'</label>
			<div class="margin-form"><input type="text" size="33" name="zupago_name" value="'.htmlentities($zupago_name, ENT_COMPAT, 'UTF-8').'" /></div><br/>
			<label>'.$this->l('ZUPAGO API KEY').'</label>
			<div class="margin-form"><input type="text" size="33" name="zupago_api_key" value="'.htmlentities($zupago_api_key, ENT_COMPAT, 'UTF-8').'" /></div><br/>
			<br /><center><input type="submit" name="submitZuPago" value="'.$this->l('Update settings').'" class="button" /></center>
		</fieldset>
		</form><br /><br />
		<fieldset class="width3">
			<legend><img src="../img/admin/warning.gif" />'.$this->l('Information').'</legend>
			- <b>'.$this->l('Auto Return').'</b> : '.$this->l('Off').',<br>
			- <b>'.$this->l('Payment Data Transfer').'</b> '.$this->l('to').' <b>Off</b>.<br><br>
			'.$this->l('In').' <i>'.$this->l('Profile > Selling Preferences > Postage Calculations').'</i><br>
			- check <b>'.$this->l('Click here to allow transaction-based shipping values to override the profile shipping settings listed above').'</b><br><br>
			<b style="color: red;">'.$this->l('All PrestaShop currencies must be also configured</b> inside Profile > Financial Information > Currency balances').'<br>
		</fieldset>';
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return ;

		global $cookie,$smarty;

		$address = new Address(intval($params['cart']->id_address_invoice));
		$customer = new Customer(intval($params['cart']->id_customer));
		$currency = $this->getCurrency();
		$zupago_account = Configuration::get('ZUPAGO_ACCOUNT');
		$zupago_account_btc = Configuration::get('ZUPAGO_ACCOUNT_BTC');
		$zupago_name = Configuration::get('ZUPAGO_NAME');
		$zupago_jk = Configuration::get('ZUPAGO_ALTERNATE');

		if (!Validate::isLoadedObject($address) OR !Validate::isLoadedObject($customer) OR !Validate::isLoadedObject($currency))
			return $this->l('ZuPago error: (invalid address or customer)');
			
		$products = $params['cart']->getProducts();

		foreach ($products as $key => $product)
		{
			$products[$key]['name'] = str_replace('"', '\'', $product['name']);
			if (isset($product['attributes']))
				$products[$key]['attributes'] = str_replace('"', '\'', $product['attributes']);
			$products[$key]['name'] = htmlentities(utf8_decode($product['name']));
			$products[$key]['zupagoAmount'] = number_format(Tools::convertPrice($product['price_wt'], $currency), 2, '.', '');
		}
		
		$smarty->assign(array(
			'address' => $address,
			'country' => new Country(intval($address->id_country)),
			'customer' => $customer,
			'ZUPAYEE_ACC' => $zupago_account,
			'ZUPAYEE_ACC_KEY' => $zupago_jk,
			'ZUPAYEE_ACC_BTC' => $zupago_account_btc,
			'ZUPAYEE_NAME' => $zupago_name,
			'CURRENCY_TYPE'=>$currency->iso_code,
			'zupagoUrl' => $this->getZuPagoUrl(),
			'PAYMENT_AMOUNT' => number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 3), $currency), 2, '.', ''),
			'PAYMENT_REF' => intval($params['cart']->id),
			'SUCCESS_URL' => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/zupago/validation.php',
			'CANCEL_URL' => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'index.php',
			'this_path' => $this->_path
		));

		return $this->display(__FILE__, '/views/templates/hook/zupago.tpl');
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;

		return $this->display(__FILE__, '/views/templates/hook/confirmation.tpl');
	}

	function validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod = 'Unknown', $message = NULL, $extraVars = array(), $currency_special = NULL, $dont_touch_amount = false)
	{			
		if (!$this->active)
			return ;

		$currency = $this->getCurrency();
		$cart = new Cart(intval($id_cart));
		$cart->id_currency = $currency->id;
		$cart->save();
		parent::validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod, $message, $extraVars, $currency_special, true);
	}
}

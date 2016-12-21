<p class="payment_module">
	<a href="javascript:$('#zupago_form').submit();" title="{l s='Pay with ZuPago HyBrid (HD) Wallet' mod='zupago'}">
		<img src="{$module_template_dir}/views/img/zupago.gif" alt="{l s='Pay with ZuPago HyBrid (HD) Wallet' mod='zupago'}" />
		{l s='Pay ZuPago HyBrid (HD) Wallet' mod='zupago'}
	</a>
</p>

<form action="{$zupagoUrl}" method="post" id="zupago_form" class="hidden">
	<input type="hidden" name="ZUPAYEE_ACC" value="{$ZUPAYEE_ACC}" />
	<input type="hidden" name="ZUPAYEE_ACC_BTC" value="{$ZUPAYEE_ACC_BTC}" />
	<input type="hidden" name="ZUPAYEE_ACC_KEY" value="{$ZUPAYEE_ACC_KEY}" />
	<input type="hidden" name="ZUPAYEE_NAME" value="{$ZUPAYEE_NAME}" />
	<input type="hidden" name="PAYMENT_AMOUNT" value="{$PAYMENT_AMOUNT}" />
	<input type="hidden" name="CURRENCY_TYPE" value="{$CURRENCY_TYPE}" />
	<input type="hidden" name="PAYMENT_REF" value="{$PAYMENT_REF}" />
	<input type="hidden" name="SUCCESS_URL" value="{$SUCCESS_URL}" />
	<input type="hidden" name="CANCEL_URL" value="{$CANCEL_URL}" />
</form>


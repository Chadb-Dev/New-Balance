/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_OPPCw
 * 
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
        	
			{
			    type: 'oppcw_generic',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_generic-method'
			},
			{
			    type: 'oppcw_americanexpress',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_americanexpress-method'
			},
			{
			    type: 'oppcw_boleto',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_boleto-method'
			},
			{
			    type: 'oppcw_cartebleue',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_cartebleue-method'
			},
			{
			    type: 'oppcw_dankort',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_dankort-method'
			},
			{
			    type: 'oppcw_creditcard',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_creditcard-method'
			},
			{
			    type: 'oppcw_diners',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_diners-method'
			},
			{
			    type: 'oppcw_directdebitssepa',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_directdebitssepa-method'
			},
			{
			    type: 'oppcw_discovercard',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_discovercard-method'
			},
			{
			    type: 'oppcw_eps',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_eps-method'
			},
			{
			    type: 'oppcw_giropay',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_giropay-method'
			},
			{
			    type: 'oppcw_ideal',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_ideal-method'
			},
			{
			    type: 'oppcw_jcb',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_jcb-method'
			},
			{
			    type: 'oppcw_klarnainvoice',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_klarnainvoice-method'
			},
			{
			    type: 'oppcw_maestro',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_maestro-method'
			},
			{
			    type: 'oppcw_mastercard',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_mastercard-method'
			},
			{
			    type: 'oppcw_openinvoice',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_openinvoice-method'
			},
			{
			    type: 'oppcw_payolutionelv',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_payolutionelv-method'
			},
			{
			    type: 'oppcw_payolutionins',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_payolutionins-method'
			},
			{
			    type: 'oppcw_paypal',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_paypal-method'
			},
			{
			    type: 'oppcw_paysafecard',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_paysafecard-method'
			},
			{
			    type: 'oppcw_prepayment',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_prepayment-method'
			},
			{
			    type: 'oppcw_skrill',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_skrill-method'
			},
			{
			    type: 'oppcw_sofortueberweisung',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_sofortueberweisung-method'
			},
			{
			    type: 'oppcw_visa',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_visa-method'
			},
			{
			    type: 'oppcw_vpay',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_vpay-method'
			},
			{
			    type: 'oppcw_paydirekt',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_paydirekt-method'
			},
			{
			    type: 'oppcw_bcmc',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_bcmc-method'
			},
			{
			    type: 'oppcw_poli',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_poli-method'
			},
			{
			    type: 'oppcw_entercash',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_entercash-method'
			},
			{
			    type: 'oppcw_afterpaypaylater',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_afterpaypaylater-method'
			},
			{
			    type: 'oppcw_afterpaydirectdebit',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_afterpaydirectdebit-method'
			},
			{
			    type: 'oppcw_paytrail',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_paytrail-method'
			},
			{
			    type: 'oppcw_trustly',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_trustly-method'
			},
			{
			    type: 'oppcw_trustpay',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_trustpay-method'
			},
			{
			    type: 'oppcw_postfinancecard',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_postfinancecard-method'
			},
			{
			    type: 'oppcw_onecard',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_onecard-method'
			},
			{
			    type: 'oppcw_przelewy24',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_przelewy24-method'
			},
			{
			    type: 'oppcw_yandex',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_yandex-method'
			},
			{
			    type: 'oppcw_tenpay',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_tenpay-method'
			},
			{
			    type: 'oppcw_daopay',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_daopay-method'
			},
			{
			    type: 'oppcw_alipay',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_alipay-method'
			},
			{
			    type: 'oppcw_cashuprepaid',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_cashuprepaid-method'
			},
			{
			    type: 'oppcw_debitmastercard',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_debitmastercard-method'
			},
			{
			    type: 'oppcw_debitvisa',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_debitvisa-method'
			},
			{
			    type: 'oppcw_chinaunionpay',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_chinaunionpay-method'
			},
			{
			    type: 'oppcw_interac',
			    component: 'Customweb_OPPCw/js/view/payment/method-renderer/oppcw_interac-method'
			}
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
<?php
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

namespace Customweb\OPPCw\Model\Config\Source;

class CountryRestriction implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		return [
			['value' => 'AT', 'label' => __('Austria')],
			['value' => 'BE', 'label' => __('Belgium')],
			['value' => 'CZ', 'label' => __('Czech Republic')],
			['value' => 'DE', 'label' => __('Germany')],
			['value' => 'HU', 'label' => __('Hungary')],
			['value' => 'IT', 'label' => __('Italy')],
			['value' => 'NL', 'label' => __('Netherlands')],
			['value' => 'PL', 'label' => __('Poland')],
			['value' => 'SK', 'label' => __('Slovak Republic')],
			['value' => 'ES', 'label' => __('Spain')],
			['value' => 'CH', 'label' => __('Switzerland')],
			['value' => 'GB', 'label' => __('United Kingdom')],
		];
	}
}

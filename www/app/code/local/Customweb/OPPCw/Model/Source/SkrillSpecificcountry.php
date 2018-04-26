<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
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
 */

class Customweb_OPPCw_Model_Source_SkrillSpecificcountry
{
	public function toOptionArray()
	{
		$options = array(
			array('value' => 'AD', 'label' => Mage::helper('adminhtml')->__("Andorra")),
			array('value' => 'AE', 'label' => Mage::helper('adminhtml')->__("United Arab Emirates")),
			array('value' => 'AF', 'label' => Mage::helper('adminhtml')->__("Afghanistan")),
			array('value' => 'AG', 'label' => Mage::helper('adminhtml')->__("Antigua & Barbuda")),
			array('value' => 'AI', 'label' => Mage::helper('adminhtml')->__("Anguilla")),
			array('value' => 'AL', 'label' => Mage::helper('adminhtml')->__("Albania")),
			array('value' => 'AM', 'label' => Mage::helper('adminhtml')->__("Armenia")),
			array('value' => 'AN', 'label' => Mage::helper('adminhtml')->__("Netherlands Antilles")),
			array('value' => 'AO', 'label' => Mage::helper('adminhtml')->__("Angola")),
			array('value' => 'AQ', 'label' => Mage::helper('adminhtml')->__("Antarctica")),
			array('value' => 'AR', 'label' => Mage::helper('adminhtml')->__("Argentina")),
			array('value' => 'AS', 'label' => Mage::helper('adminhtml')->__("American Samoa")),
			array('value' => 'AT', 'label' => Mage::helper('adminhtml')->__("Austria")),
			array('value' => 'AU', 'label' => Mage::helper('adminhtml')->__("Australia")),
			array('value' => 'AW', 'label' => Mage::helper('adminhtml')->__("Aruba")),
			array('value' => 'AX', 'label' => Mage::helper('adminhtml')->__("Aland Islands")),
			array('value' => 'AZ', 'label' => Mage::helper('adminhtml')->__("Azerbaijan")),
			array('value' => 'BA', 'label' => Mage::helper('adminhtml')->__("Bosnia & Herzegovina")),
			array('value' => 'BB', 'label' => Mage::helper('adminhtml')->__("Barbados")),
			array('value' => 'BD', 'label' => Mage::helper('adminhtml')->__("Bangladesh")),
			array('value' => 'BE', 'label' => Mage::helper('adminhtml')->__("Belgium")),
			array('value' => 'BF', 'label' => Mage::helper('adminhtml')->__("Burkina Faso")),
			array('value' => 'BG', 'label' => Mage::helper('adminhtml')->__("Bulgaria")),
			array('value' => 'BH', 'label' => Mage::helper('adminhtml')->__("Bahrain")),
			array('value' => 'BI', 'label' => Mage::helper('adminhtml')->__("Burundi")),
			array('value' => 'BJ', 'label' => Mage::helper('adminhtml')->__("Benin")),
			array('value' => 'BM', 'label' => Mage::helper('adminhtml')->__("Bermuda")),
			array('value' => 'BN', 'label' => Mage::helper('adminhtml')->__("Brunei Darussalam")),
			array('value' => 'BO', 'label' => Mage::helper('adminhtml')->__("Bolivia")),
			array('value' => 'BR', 'label' => Mage::helper('adminhtml')->__("Brazil")),
			array('value' => 'BS', 'label' => Mage::helper('adminhtml')->__("Bahamas")),
			array('value' => 'BT', 'label' => Mage::helper('adminhtml')->__("Bhutan")),
			array('value' => 'BV', 'label' => Mage::helper('adminhtml')->__("Bouvet Island")),
			array('value' => 'BW', 'label' => Mage::helper('adminhtml')->__("Botswana")),
			array('value' => 'BY', 'label' => Mage::helper('adminhtml')->__("Belarus")),
			array('value' => 'BZ', 'label' => Mage::helper('adminhtml')->__("Belize")),
			array('value' => 'CA', 'label' => Mage::helper('adminhtml')->__("Canada")),
			array('value' => 'CC', 'label' => Mage::helper('adminhtml')->__("Cocos (Keeling) Islands")),
			array('value' => 'CD', 'label' => Mage::helper('adminhtml')->__("Zaire")),
			array('value' => 'CF', 'label' => Mage::helper('adminhtml')->__("Central African Republic")),
			array('value' => 'CG', 'label' => Mage::helper('adminhtml')->__("Congo")),
			array('value' => 'CH', 'label' => Mage::helper('adminhtml')->__("Switzerland")),
			array('value' => 'CI', 'label' => Mage::helper('adminhtml')->__("Cote D'ivoire")),
			array('value' => 'CK', 'label' => Mage::helper('adminhtml')->__("Cook Islands")),
			array('value' => 'CL', 'label' => Mage::helper('adminhtml')->__("Chile")),
			array('value' => 'CM', 'label' => Mage::helper('adminhtml')->__("Cameroon")),
			array('value' => 'CN', 'label' => Mage::helper('adminhtml')->__("China")),
			array('value' => 'CO', 'label' => Mage::helper('adminhtml')->__("Colombia")),
			array('value' => 'CR', 'label' => Mage::helper('adminhtml')->__("Costa Rica")),
			array('value' => 'CU', 'label' => Mage::helper('adminhtml')->__("Cuba")),
			array('value' => 'CV', 'label' => Mage::helper('adminhtml')->__("Cape Verde")),
			array('value' => 'CX', 'label' => Mage::helper('adminhtml')->__("Christmas Island")),
			array('value' => 'CY', 'label' => Mage::helper('adminhtml')->__("Cyprus")),
			array('value' => 'CZ', 'label' => Mage::helper('adminhtml')->__("Czech Republic")),
			array('value' => 'DE', 'label' => Mage::helper('adminhtml')->__("Germany")),
			array('value' => 'DJ', 'label' => Mage::helper('adminhtml')->__("Djibouti")),
			array('value' => 'DK', 'label' => Mage::helper('adminhtml')->__("Denmark")),
			array('value' => 'DM', 'label' => Mage::helper('adminhtml')->__("Dominica")),
			array('value' => 'DO', 'label' => Mage::helper('adminhtml')->__("Dominican Republic")),
			array('value' => 'DZ', 'label' => Mage::helper('adminhtml')->__("Algeria")),
			array('value' => 'EC', 'label' => Mage::helper('adminhtml')->__("Ecuador")),
			array('value' => 'EE', 'label' => Mage::helper('adminhtml')->__("Estonia")),
			array('value' => 'EG', 'label' => Mage::helper('adminhtml')->__("Egypt")),
			array('value' => 'EH', 'label' => Mage::helper('adminhtml')->__("Western Sahara")),
			array('value' => 'ER', 'label' => Mage::helper('adminhtml')->__("Eritrea")),
			array('value' => 'ES', 'label' => Mage::helper('adminhtml')->__("Spain")),
			array('value' => 'ET', 'label' => Mage::helper('adminhtml')->__("Ethiopia")),
			array('value' => 'FI', 'label' => Mage::helper('adminhtml')->__("Finland")),
			array('value' => 'FJ', 'label' => Mage::helper('adminhtml')->__("Fiji")),
			array('value' => 'FK', 'label' => Mage::helper('adminhtml')->__("Falkland Islands")),
			array('value' => 'FM', 'label' => Mage::helper('adminhtml')->__("Micronesia")),
			array('value' => 'FO', 'label' => Mage::helper('adminhtml')->__("Faroe Islands")),
			array('value' => 'FR', 'label' => Mage::helper('adminhtml')->__("France")),
			array('value' => 'GA', 'label' => Mage::helper('adminhtml')->__("Gabon")),
			array('value' => 'GB', 'label' => Mage::helper('adminhtml')->__("United Kingdom")),
			array('value' => 'GD', 'label' => Mage::helper('adminhtml')->__("Grenada")),
			array('value' => 'GE', 'label' => Mage::helper('adminhtml')->__("Georgia")),
			array('value' => 'GF', 'label' => Mage::helper('adminhtml')->__("French Guiana")),
			array('value' => 'GG', 'label' => Mage::helper('adminhtml')->__("Guernsey")),
			array('value' => 'GH', 'label' => Mage::helper('adminhtml')->__("Ghana")),
			array('value' => 'GI', 'label' => Mage::helper('adminhtml')->__("Gibraltar")),
			array('value' => 'GL', 'label' => Mage::helper('adminhtml')->__("Greenland")),
			array('value' => 'GM', 'label' => Mage::helper('adminhtml')->__("Gambia")),
			array('value' => 'GN', 'label' => Mage::helper('adminhtml')->__("Guinea")),
			array('value' => 'GP', 'label' => Mage::helper('adminhtml')->__("Guadeloupe")),
			array('value' => 'GQ', 'label' => Mage::helper('adminhtml')->__("Equatorial Guinea")),
			array('value' => 'GR', 'label' => Mage::helper('adminhtml')->__("Greece")),
			array('value' => 'GS', 'label' => Mage::helper('adminhtml')->__("S.Georgia & S.Sandwich Islands")),
			array('value' => 'GT', 'label' => Mage::helper('adminhtml')->__("Guatemala")),
			array('value' => 'GU', 'label' => Mage::helper('adminhtml')->__("Guam")),
			array('value' => 'GW', 'label' => Mage::helper('adminhtml')->__("Guinea-Bissau")),
			array('value' => 'GY', 'label' => Mage::helper('adminhtml')->__("Guyana")),
			array('value' => 'HK', 'label' => Mage::helper('adminhtml')->__("Hong Kong")),
			array('value' => 'HM', 'label' => Mage::helper('adminhtml')->__("Heard & McDonald Islands")),
			array('value' => 'HN', 'label' => Mage::helper('adminhtml')->__("Honduras")),
			array('value' => 'HR', 'label' => Mage::helper('adminhtml')->__("Croatia")),
			array('value' => 'HT', 'label' => Mage::helper('adminhtml')->__("Haiti")),
			array('value' => 'HU', 'label' => Mage::helper('adminhtml')->__("Hungary")),
			array('value' => 'ID', 'label' => Mage::helper('adminhtml')->__("Indonesia")),
			array('value' => 'IE', 'label' => Mage::helper('adminhtml')->__("Ireland")),
			array('value' => 'IL', 'label' => Mage::helper('adminhtml')->__("Israel")),
			array('value' => 'IM', 'label' => Mage::helper('adminhtml')->__("Isle of Man")),
			array('value' => 'IN', 'label' => Mage::helper('adminhtml')->__("India")),
			array('value' => 'IO', 'label' => Mage::helper('adminhtml')->__("British Indian Ocean Territory")),
			array('value' => 'IQ', 'label' => Mage::helper('adminhtml')->__("Iraq")),
			array('value' => 'IR', 'label' => Mage::helper('adminhtml')->__("Iran")),
			array('value' => 'IS', 'label' => Mage::helper('adminhtml')->__("Iceland")),
			array('value' => 'IT', 'label' => Mage::helper('adminhtml')->__("Italy")),
			array('value' => 'JE', 'label' => Mage::helper('adminhtml')->__("Jersey")),
			array('value' => 'JM', 'label' => Mage::helper('adminhtml')->__("Jamaica")),
			array('value' => 'JO', 'label' => Mage::helper('adminhtml')->__("Jordan")),
			array('value' => 'JP', 'label' => Mage::helper('adminhtml')->__("Japan")),
			array('value' => 'KE', 'label' => Mage::helper('adminhtml')->__("Kenya")),
			array('value' => 'KG', 'label' => Mage::helper('adminhtml')->__("Kyrgyzstan")),
			array('value' => 'KH', 'label' => Mage::helper('adminhtml')->__("Cambodia")),
			array('value' => 'KI', 'label' => Mage::helper('adminhtml')->__("Kiribati")),
			array('value' => 'KM', 'label' => Mage::helper('adminhtml')->__("Comoros")),
			array('value' => 'KN', 'label' => Mage::helper('adminhtml')->__("Saint Kitts & Nevis")),
			array('value' => 'KP', 'label' => Mage::helper('adminhtml')->__("Korea")),
			array('value' => 'KR', 'label' => Mage::helper('adminhtml')->__("Korea")),
			array('value' => 'KW', 'label' => Mage::helper('adminhtml')->__("Kuwait")),
			array('value' => 'KY', 'label' => Mage::helper('adminhtml')->__("Cayman Islands")),
			array('value' => 'KZ', 'label' => Mage::helper('adminhtml')->__("Kazakhstan")),
			array('value' => 'LA', 'label' => Mage::helper('adminhtml')->__("Laos")),
			array('value' => 'LB', 'label' => Mage::helper('adminhtml')->__("Lebanon")),
			array('value' => 'LC', 'label' => Mage::helper('adminhtml')->__("Saint Lucia")),
			array('value' => 'LI', 'label' => Mage::helper('adminhtml')->__("Liechtenstein")),
			array('value' => 'LK', 'label' => Mage::helper('adminhtml')->__("Sri Lanka")),
			array('value' => 'LR', 'label' => Mage::helper('adminhtml')->__("Liberia")),
			array('value' => 'LS', 'label' => Mage::helper('adminhtml')->__("Lesotho")),
			array('value' => 'LT', 'label' => Mage::helper('adminhtml')->__("Lithuania")),
			array('value' => 'LU', 'label' => Mage::helper('adminhtml')->__("Luxembourg")),
			array('value' => 'LV', 'label' => Mage::helper('adminhtml')->__("Latvia")),
			array('value' => 'LY', 'label' => Mage::helper('adminhtml')->__("Libya")),
			array('value' => 'MA', 'label' => Mage::helper('adminhtml')->__("Morocco")),
			array('value' => 'MC', 'label' => Mage::helper('adminhtml')->__("Monaco")),
			array('value' => 'MD', 'label' => Mage::helper('adminhtml')->__("Moldova")),
			array('value' => 'MG', 'label' => Mage::helper('adminhtml')->__("Madagascar")),
			array('value' => 'MH', 'label' => Mage::helper('adminhtml')->__("Marshall Islands")),
			array('value' => 'MK', 'label' => Mage::helper('adminhtml')->__("Macedonia")),
			array('value' => 'ML', 'label' => Mage::helper('adminhtml')->__("Mali")),
			array('value' => 'MM', 'label' => Mage::helper('adminhtml')->__("Myanmar")),
			array('value' => 'MN', 'label' => Mage::helper('adminhtml')->__("Mongolia")),
			array('value' => 'MO', 'label' => Mage::helper('adminhtml')->__("Macau")),
			array('value' => 'MP', 'label' => Mage::helper('adminhtml')->__("Northern Mariana Islands")),
			array('value' => 'MQ', 'label' => Mage::helper('adminhtml')->__("Martinique")),
			array('value' => 'MR', 'label' => Mage::helper('adminhtml')->__("Mauritania")),
			array('value' => 'MS', 'label' => Mage::helper('adminhtml')->__("Montserrat")),
			array('value' => 'MT', 'label' => Mage::helper('adminhtml')->__("Malta")),
			array('value' => 'MU', 'label' => Mage::helper('adminhtml')->__("Mauritius")),
			array('value' => 'MV', 'label' => Mage::helper('adminhtml')->__("Maldives")),
			array('value' => 'MW', 'label' => Mage::helper('adminhtml')->__("Malawi")),
			array('value' => 'MX', 'label' => Mage::helper('adminhtml')->__("Mexico")),
			array('value' => 'MY', 'label' => Mage::helper('adminhtml')->__("Malaysia")),
			array('value' => 'MZ', 'label' => Mage::helper('adminhtml')->__("Mozambique")),
			array('value' => 'NA', 'label' => Mage::helper('adminhtml')->__("Namibia")),
			array('value' => 'NC', 'label' => Mage::helper('adminhtml')->__("New Caledonia")),
			array('value' => 'NE', 'label' => Mage::helper('adminhtml')->__("Niger")),
			array('value' => 'NF', 'label' => Mage::helper('adminhtml')->__("Norfolk Island")),
			array('value' => 'NG', 'label' => Mage::helper('adminhtml')->__("Nigeria")),
			array('value' => 'NI', 'label' => Mage::helper('adminhtml')->__("Nicaragua")),
			array('value' => 'NL', 'label' => Mage::helper('adminhtml')->__("Netherlands")),
			array('value' => 'NO', 'label' => Mage::helper('adminhtml')->__("Norway")),
			array('value' => 'NP', 'label' => Mage::helper('adminhtml')->__("Nepal")),
			array('value' => 'NR', 'label' => Mage::helper('adminhtml')->__("Nauru")),
			array('value' => 'NU', 'label' => Mage::helper('adminhtml')->__("Niue")),
			array('value' => 'NZ', 'label' => Mage::helper('adminhtml')->__("New Zealand")),
			array('value' => 'OM', 'label' => Mage::helper('adminhtml')->__("Oman")),
			array('value' => 'PA', 'label' => Mage::helper('adminhtml')->__("Panama")),
			array('value' => 'PE', 'label' => Mage::helper('adminhtml')->__("Peru")),
			array('value' => 'PF', 'label' => Mage::helper('adminhtml')->__("French Polynesia")),
			array('value' => 'PG', 'label' => Mage::helper('adminhtml')->__("Papua New Guinea")),
			array('value' => 'PH', 'label' => Mage::helper('adminhtml')->__("Philippines")),
			array('value' => 'PK', 'label' => Mage::helper('adminhtml')->__("Pakistan")),
			array('value' => 'PL', 'label' => Mage::helper('adminhtml')->__("Poland")),
			array('value' => 'PM', 'label' => Mage::helper('adminhtml')->__("St. Pierre & Miquelon")),
			array('value' => 'PN', 'label' => Mage::helper('adminhtml')->__("Pitcairn")),
			array('value' => 'PR', 'label' => Mage::helper('adminhtml')->__("Puerto Rico")),
			array('value' => 'PT', 'label' => Mage::helper('adminhtml')->__("Portugal")),
			array('value' => 'PW', 'label' => Mage::helper('adminhtml')->__("Palau")),
			array('value' => 'PY', 'label' => Mage::helper('adminhtml')->__("Paraguay")),
			array('value' => 'QA', 'label' => Mage::helper('adminhtml')->__("Qatar")),
			array('value' => 'RE', 'label' => Mage::helper('adminhtml')->__("Reunion")),
			array('value' => 'RO', 'label' => Mage::helper('adminhtml')->__("Romania")),
			array('value' => 'RU', 'label' => Mage::helper('adminhtml')->__("Russian Federation")),
			array('value' => 'RW', 'label' => Mage::helper('adminhtml')->__("Rwanda")),
			array('value' => 'SA', 'label' => Mage::helper('adminhtml')->__("Saudi Arabia")),
			array('value' => 'SB', 'label' => Mage::helper('adminhtml')->__("Solomon Islands")),
			array('value' => 'SC', 'label' => Mage::helper('adminhtml')->__("Seychelles")),
			array('value' => 'SD', 'label' => Mage::helper('adminhtml')->__("Sudan")),
			array('value' => 'SE', 'label' => Mage::helper('adminhtml')->__("Sweden")),
			array('value' => 'SG', 'label' => Mage::helper('adminhtml')->__("Singapore")),
			array('value' => 'SI', 'label' => Mage::helper('adminhtml')->__("Slovenia")),
			array('value' => 'SJ', 'label' => Mage::helper('adminhtml')->__("Svalbard & Jan Mayen Islands")),
			array('value' => 'SK', 'label' => Mage::helper('adminhtml')->__("Slovak Republic")),
			array('value' => 'SL', 'label' => Mage::helper('adminhtml')->__("Sierra Leone")),
			array('value' => 'SM', 'label' => Mage::helper('adminhtml')->__("San Marino")),
			array('value' => 'SN', 'label' => Mage::helper('adminhtml')->__("Senegal")),
			array('value' => 'SO', 'label' => Mage::helper('adminhtml')->__("Somalia")),
			array('value' => 'SR', 'label' => Mage::helper('adminhtml')->__("Suriname")),
			array('value' => 'ST', 'label' => Mage::helper('adminhtml')->__("Sao Tome & Principe")),
			array('value' => 'SV', 'label' => Mage::helper('adminhtml')->__("El Salvador")),
			array('value' => 'SY', 'label' => Mage::helper('adminhtml')->__("Syria")),
			array('value' => 'SZ', 'label' => Mage::helper('adminhtml')->__("Swaziland")),
			array('value' => 'TC', 'label' => Mage::helper('adminhtml')->__("Turks & Caicos Islands")),
			array('value' => 'TD', 'label' => Mage::helper('adminhtml')->__("Chad")),
			array('value' => 'TF', 'label' => Mage::helper('adminhtml')->__("French Southern Territories")),
			array('value' => 'TG', 'label' => Mage::helper('adminhtml')->__("Togo")),
			array('value' => 'TH', 'label' => Mage::helper('adminhtml')->__("Thailand")),
			array('value' => 'TJ', 'label' => Mage::helper('adminhtml')->__("Tajikistan")),
			array('value' => 'TK', 'label' => Mage::helper('adminhtml')->__("Tokelau")),
			array('value' => 'TM', 'label' => Mage::helper('adminhtml')->__("Turkmenistan")),
			array('value' => 'TN', 'label' => Mage::helper('adminhtml')->__("Tunisia")),
			array('value' => 'TO', 'label' => Mage::helper('adminhtml')->__("Tonga")),
			array('value' => 'TP', 'label' => Mage::helper('adminhtml')->__("East Timor")),
			array('value' => 'TR', 'label' => Mage::helper('adminhtml')->__("Turkey")),
			array('value' => 'TT', 'label' => Mage::helper('adminhtml')->__("Trinidad & Tobago")),
			array('value' => 'TV', 'label' => Mage::helper('adminhtml')->__("Tuvalu")),
			array('value' => 'TW', 'label' => Mage::helper('adminhtml')->__("Taiwan")),
			array('value' => 'TZ', 'label' => Mage::helper('adminhtml')->__("Tanzania")),
			array('value' => 'UA', 'label' => Mage::helper('adminhtml')->__("Ukraine")),
			array('value' => 'UG', 'label' => Mage::helper('adminhtml')->__("Uganda")),
			array('value' => 'US', 'label' => Mage::helper('adminhtml')->__("United States")),
			array('value' => 'UY', 'label' => Mage::helper('adminhtml')->__("Uruguay")),
			array('value' => 'UZ', 'label' => Mage::helper('adminhtml')->__("Uzbekistan")),
			array('value' => 'VA', 'label' => Mage::helper('adminhtml')->__("Vatican City")),
			array('value' => 'VC', 'label' => Mage::helper('adminhtml')->__("St. Vincent & the Grenadines")),
			array('value' => 'VE', 'label' => Mage::helper('adminhtml')->__("Venezuela")),
			array('value' => 'VG', 'label' => Mage::helper('adminhtml')->__("Virgin Islands")),
			array('value' => 'VI', 'label' => Mage::helper('adminhtml')->__("Virgin Islands")),
			array('value' => 'VN', 'label' => Mage::helper('adminhtml')->__("Viet Nam")),
			array('value' => 'VU', 'label' => Mage::helper('adminhtml')->__("Vanuatu")),
			array('value' => 'WF', 'label' => Mage::helper('adminhtml')->__("Wallis & Futuna Islands")),
			array('value' => 'WS', 'label' => Mage::helper('adminhtml')->__("Samoa")),
			array('value' => 'YE', 'label' => Mage::helper('adminhtml')->__("Yemen")),
			array('value' => 'YT', 'label' => Mage::helper('adminhtml')->__("Mayotte")),
			array('value' => 'ZA', 'label' => Mage::helper('adminhtml')->__("South Africa")),
			array('value' => 'ZM', 'label' => Mage::helper('adminhtml')->__("Zambia")),
			array('value' => 'ZW', 'label' => Mage::helper('adminhtml')->__("Zimbabwe")),
			array('value' => 'BQ', 'label' => Mage::helper('adminhtml')->__("Caribbean Netherlands")),
			array('value' => 'CW', 'label' => Mage::helper('adminhtml')->__("Curaçao")),
			array('value' => 'ME', 'label' => Mage::helper('adminhtml')->__("Montenegro")),
			array('value' => 'RS', 'label' => Mage::helper('adminhtml')->__("Serbia")),
			array('value' => 'SH', 'label' => Mage::helper('adminhtml')->__("Saint Helena, Ascension and Tristan da Cunha")),
			array('value' => 'SS', 'label' => Mage::helper('adminhtml')->__("South Sudan")),
			array('value' => 'SX', 'label' => Mage::helper('adminhtml')->__("Sint Maarten")),
			array('value' => 'TL', 'label' => Mage::helper('adminhtml')->__("Timor-Leste"))
		);
		return $options;
	}
}

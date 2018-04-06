<?php

namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 2.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, http://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class TimebasedCurrencyHelper extends Helper
{
    
    public $helpers = ['MyTime', 'MyHtml', 'MyNumber'];
    
    public function getOrderInformationText($showText)
    {
        $text = '';
        if ($showText) {
            $text = '<p style="clear:both;">* Mouseover zeigt den bezahlten '.Configure::read('appDb.FCS_TIMEBASED_CURRENCY_NAME').'-Betrag an.</p>';
        }
        return $text;
    }
    
    public function getName()
    {
        return Configure::read('appDb.FCS_TIMEBASED_CURRENCY_NAME') . 'konto';
    }
    
    public function formatSecondsToTimebasedCurrency($seconds)
    {
        $hours = round($seconds / 3600, 2);
        return $this->MyHtml->formatAsUnit($hours, Configure::read('appDb.FCS_TIMEBASED_CURRENCY_SHORTCODE'));
    }
    
    public function getTimebasedCurrencyHoursDropdown($maxSeconds, $exchangeRate)
    {
        $stepsInSeconds = 15 * 60;
        $dropdown = [];
        $usedValues = [];
        for($second = 0; $second <= $maxSeconds; $second++) {
            $valueWithEuro = $this->formatSecondsToTimebasedCurrency($second) . ' (' . $this->getCartTimebasedCurrencySecondsAsEuroForDropdown($second, $exchangeRate) . ')';
            $valueWithEuro = str_replace('&nbsp;', ' ', $valueWithEuro);
            if ($second % $stepsInSeconds == 0 && !isset($usedValues[$second])) {
                $dropdown[$second] = $valueWithEuro;
                $usedValues[$second] = true;
            }
        }
        $maxHoursValue = $this->formatSecondsToTimebasedCurrency($maxSeconds);
        $maxHoursValue .= ' (' . $this->getCartTimebasedCurrencySecondsAsEuroForDropdown($maxSeconds, $exchangeRate) . ')';
        $maxHoursValue = str_replace('&nbsp;', ' ', $maxHoursValue);
        
        if (!isset($usedValues[$maxHoursValue])) {
            $dropdown[$this->MyNumber->replaceCommaWithDot((string) $maxSeconds)] = $maxHoursValue;
        }
        $dropdown = array_reverse($dropdown, true);
        return $dropdown;
    }
    
    public function getCartTimebasedCurrencySecondsAsEuroForDropdown($seconds, $exchangeRate)
    {
        return str_replace('&nbsp;', ' ', $this->MyHtml->formatAsEuro(
            $seconds / 3600 *
            $this->MyNumber->replaceCommaWithDot($exchangeRate)
        ));
    }
    
}

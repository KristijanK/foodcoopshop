<?php
/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, http://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */

use Cake\Core\Configure;

$this->element('addScript', [
    'script' => Configure::read('app.jsNamespace') . ".Helper.initDatepicker();
        var datefieldSelector = $('input.datepicker');
        datefieldSelector.datepicker();" . Configure::read('app.jsNamespace') . ".Admin.init();".
        Configure::read('app.jsNamespace') . ".Helper.initTooltip('.payment-approval-comment');".
        Configure::read('app.jsNamespace') . ".Admin.selectMainMenuAdmin('Homepage-Verwaltung', 'Finanzberichte');"
]);
if ($paymentType == 'product') {
    $this->element('highlightRowAfterEdit', [
        'rowIdPrefix' => '#cakePayment-'
    ]);
}
?>

<div class="filter-container">
    <?php echo $this->Form->create(null, ['type' => 'get']); ?>
        <h1><?php echo $title_for_layout; ?></h1>
        <?php echo $this->element('dateFields', ['dateFrom' => $dateFrom, 'dateTo' => $dateTo, 'nameTo' => 'dateTo', 'nameFrom' => 'dateFrom']); ?>
        <?php echo $this->Form->control('customerId', ['type' => 'select', 'label' => '', 'empty' => 'alle Mitglieder', 'options' => $customersForDropdown, 'default' => isset($customerId) ? $customerId: '']); ?>
        <div class="right"></div>
    <?php echo $this->Form->end(); ?>
</div>

<div id="help-container">
    <ul>
        <li>
            Hier findest du die Auswertung für: <?php echo $this->Html->getPaymentText($paymentType); ?>
        </li>
        <li>Gelöschte Einzahlungen werden ausgegraut angeführt.</li>
        <li>Falls die Zahlung von einem anderen Mitglied eingetragen wurde, wird seit v1.3 wird dieses Mitglied in der Spalte "Eingetragen von" angezeigt.</li>
    </ul>
</div>

<ul class="nav nav-tabs">
    <?php
    foreach ($this->Html->getPaymentTexts() as $pt => $paymentText) {
        $btnClass = '';
        if ($pt == $this->request->getParam('pass')[0]) {
            $btnClass = 'active';
        }
        // show deposit report also for cash configuration
        if ($this->Html->paymentIsCashless() || in_array($pt, ['deposit', 'member_fee', 'member_fee_flexible'])) {
            echo '<li class="' . $btnClass . '"><a href="' . $this->Slug->getReport($pt) . '?dateFrom=' . $dateFrom . '&dateTo=' . $dateTo . '">' . $paymentText . '</a></li>';
        }
    }
?>
</ul>

<?php

echo '<table class="list">';
echo '<tr class="sort">';
$colspan = 3;
if ($paymentType == 'product') {
    echo '<th style="width:25px;"></th>';
    echo '<th style="width:50px;">' . $this->Paginator->sort('Payments.approval', 'Status') . '</th>';
    $colspan = $colspan + 2;
}
echo '<th>' . $this->Paginator->sort('Customers.' . Configure::read('app.customerMainNamePart'), 'Mitglied') . '</th>';
echo '<th>' . $this->Paginator->sort('Payments.date_add', 'Eingetragen am') . '</th>';
echo '<th>' . $this->Paginator->sort('CreatedBy.' . Configure::read('app.customerMainNamePart'), 'Eingetragen von') . '</th>';
echo '<th>' . $this->Html->getPaymentText($paymentType) . '</th>';
if ($showTextColumn) {
    echo '<th>' . $this->Paginator->sort('Payments.text', 'Text') . '</th>';
}
echo '</tr>';

$i = 0;
$paymentSum = 0;

foreach ($payments as $payment) {
    $rowClass = '';
    $additionalText = '';
    if ($payment->status == APP_DEL) {
        $rowClass = 'deactivated';
        $additionalText = ' (' . $this->Html->getPaymentText($paymentType) . ' gelöscht am ' . $payment->date_changed->i18nFormat(Configure::read('DateFormat.de.DateNTimeShort')) . ' - scheint in der Summe nicht auf)';
    } else {
        $i ++;
        $paymentSum += $payment->amount;
    }

    echo '<tr id="cakePayment-'.$payment->id.'" class="data ' . $rowClass . '">';

    if ($paymentType == 'product') {
        echo '<td>';
            echo $this->Html->getJqueryUiIcon(
                $this->Html->image($this->Html->getFamFamFamPath('page_edit.png')),
                [
                'title' => 'Bearbeiten'
                ],
                $this->Slug->getPaymentEdit($payment->id)
            );
        echo '</td>';
        echo '<td>';
        switch ($payment->approval) {
            case -1:
                echo $this->Html->image(
                    $this->Html->getFamFamFamPath('delete.png'),
                    [
                        'class' => 'payment-approval'
                    ]
                );
                break;
            case 0:
                break;
            case 1:
                echo $this->Html->image(
                    $this->Html->getFamFamFamPath('accept.png'),
                    [
                        'class' => 'payment-approval'
                    ]
                );
                break;
        }
        if ($payment->approval_comment != '') {
            echo '<span class="payment-approval-comment-wrapper">';
            echo $this->Html->getJqueryUiIcon(
                $this->Html->image($this->Html->getFamFamFamPath('user_comment.png')),
                [
                    'class' => 'payment-approval-comment',
                    'title' => $payment->approval_comment
                ],
                'javascript:void(0);'
            );
            echo '</span>';
        }
        echo '</td>';
    }

    echo '<td>';
    if (!empty($payment->manufacturer)) {
        echo $payment->manufacturer->name;
    } else {
        echo $payment->customer->name;
    }
        echo $additionalText;
    echo '</td>';

    echo '<td style="text-align:right;width:135px;">';
        echo $payment->date_add->i18nFormat(Configure::read('DateFormat.de.DateNTimeLongWithSecs'));
    echo '</td>';

    echo '<td>';
    if (!empty($payment->created_by_customer)) {
        echo $payment->created_by_customer->name;
    }
    echo '</td>';

    echo '<td style="text-align:right;">';
        echo $this->Html->formatAsEuro($payment->amount);
    echo '</td>';

    if ($showTextColumn) {
        echo '<td>';
        switch ($paymentType) {
            case 'member_fee':
                echo $this->Html->getMemberFeeTextForFrontend($payment->text);
                break;
            case 'deposit':
                echo $this->Html->getManufacturerDepositPaymentText($payment->text);
                break;
            default:
                echo $payment->text;
        }
        echo '</td>';
    }

    echo '</tr>';
}

echo '<tr>';
echo '<td colspan="'.$colspan.'"><b>' . $i . '</b> Datensätze</td>';
echo '<td style="text-align:right;"><b>' . $this->Html->formatAsEuro($paymentSum) . '</b></td>';
if ($showTextColumn) {
    echo '<td></td>';
}
echo '</tr>';

echo '</table>';

echo '<div class="sc"></div>';

?>
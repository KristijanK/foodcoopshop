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

$this->element('addScript', array(
    'script' => Configure::read('AppConfig.jsNamespace') . ".Admin.init();"
));
?>
<div id="configurations">

        <?php
        $this->element('addScript', array(
        'script' => "$('table.list').show();
        "
        ));
    ?>

    <div class="filter-container">
        <h1><?php echo $title_for_layout; ?></h1>
    </div>

    <div id="help-container">
        <ul>
            <li>Auf dieser Seite siehst du die Konfiguration deiner
                FoodCoopShop-Installation.</li>
        </ul>
    </div>

    <h2 class="info">Die folgenden Einstellungen können selbst geändert werden.</h2>

    <table class="list no-hover no-clone-last-row">

        <tr>
            <th>Einstellung</th>
            <th></th>
            <th>Wert</th>
        </tr>

        <?php
        foreach ($configurations as $configuration) {
            if ($configuration['Configuration']['type'] == 'readonly') {
                continue;
            }

            if (! Configure::read('htmlHelper')->paymentIsCashless() && in_array($configuration['Configuration']['name'], array(
                'FCS_BANK_ACCOUNT_DATA',
                'FCS_MINIMAL_CREDIT_BALANCE'
            ))) {
                continue;
            }
            if (! Configure::read('AppConfig.memberFeeEnabled') && $configuration['Configuration']['name'] == 'FCS_MEMBER_FEE_BANK_ACCOUNT_DATA') {
                continue;
            }

            echo '<tr>';

            echo '<td class="first">';
            echo $configuration['Configuration']['text'];
            echo '</td>';

            echo '<td style="width:30px;">';

            echo $this->Html->getJqueryUiIcon($this->Html->image($this->Html->getFamFamFamPath('page_edit.png')), array(
                'title' => 'Einstellung bearbeiten',
                'class' => 'edit-configuration-button'
            ), $this->Slug->getConfigurationEdit($configuration['Configuration']['id_configuration'], $configuration['Configuration']['name']));

            echo '</td>';

            echo '<td>';

            switch ($configuration['Configuration']['type']) {
                case 'number':
                case 'text':
                case 'textarea':
                case 'textarea_big':
                    echo $configuration['Configuration']['value'];
                    break;
                case 'dropdown':
                    echo $this->Html->getConfigurationDropdownOption($configuration['Configuration']['name'], $configuration['Configuration']['value']);
                    break;
                case 'boolean':
                    echo (boolean) $configuration['Configuration']['value'] ? 'ja' : 'nein';
                    break;
            }

            echo '</td>';

            echo '</tr>';
        }
        ?>
        
        <?php if (Configure::read('AppConfig.db_config_FCS_NETWORK_PLUGIN_ENABLED')) { ?>
            <tr>
                <td>
                    <b>Remote-Foodcoops</b>
                    <br /><div class="small">Foodcoops, mit denen Hersteller ihre Produktdaten synchronisieren können.<br /><a target="_blank" href="<?php echo $this->Network->getNetworkPluginDocs(); ?>">Infos zum Netzwerk-Modul</a></div>
                </td>
                <?php if (!Configure::read('AppConfig.db_config_FCS_USE_VARIABLE_MEMBER_FEE')) { ?>
                <td colspan="2" class="sync-domain-list">
                <?php
                    echo $this->Html->link('<i class="fa fa-plus-square fa-lg"></i> Neue Remote-Foodcoop erstellen', $this->Network->getSyncDomainAdd(), array(
                        'class' => 'btn btn-default',
                        'escape' => false
                    ));
                if (!empty($syncDomains)) {
                    echo '<table class="list">';
                    echo '<tr class="sort">';
                    echo '<th>Domain</th>';
                    echo '<th>Aktiv</th>';
                    echo '<th></th>';
                    echo '</th>';
                }

                foreach ($syncDomains as $syncDomain) {
                    echo '<tr>';
                    echo '<td>'.$syncDomain['SyncDomain']['domain'].'</td>';
                    echo '<td align="center">';
                    if ($syncDomain['SyncDomain']['active'] == 1) {
                        echo $this->Html->image($this->Html->getFamFamFamPath('accept.png'));
                    } else {
                        echo $this->Html->image($this->Html->getFamFamFamPath('delete.png'));
                    }
                    echo '</td>';
                    echo '<td>';
                    echo $this->Html->getJqueryUiIcon(
                        $this->Html->image($this->Html->getFamFamFamPath('page_edit.png')),
                        array(
                        'title' => 'Remote-Foodcoop ' . $syncDomain['SyncDomain']['domain'] . ' ändern',
                        ),
                        $this->Network->getSyncDomainEdit($syncDomain['SyncDomain']['id'])
                    );
                    echo '</td>';
                    echo '<tr>';
                }
                if (!empty($syncDomains)) {
                    echo '</table>';
                }
                    ?>
                </td>
                <?php } else { ?>
                <td colspan="2"><p>Solange der variable Mitgliedsbeitrag aktiviert ist, können für diese Foodcoop keine Remote-Foodcoops erstellt werden.</p></td>
                <?php } ?>
        </tr>
        <?php } ?>
    </table>

    <br />


    <h2 class="info">Die folgenden Einstellungen können (noch) nicht
        selbst geändert werden.</h2>

    <table class="list no-hover">

        <tr>
            <th>Einstellung</th>
            <th>Wert</th>
        </tr>

        <?php
        foreach ($configurations as $configuration) {
            if ($configuration['Configuration']['type'] != 'readonly') {
                continue;
            }

            echo '<tr>';

            echo '<td class="first">';
            echo $configuration['Configuration']['text'];
            echo '</td>';

            echo '<td>';
            echo $configuration['Configuration']['value'];
            echo '</td>';

            echo '</tr>';
        }
        ?>
        
        <tr>
            <td>Version FoodCoopShop</td>
            <td><?php echo $versionFoodCoopShop; ?></td>
        </tr>

        <?php if (Configure::read('AppConfig.db_config_FCS_NETWORK_PLUGIN_ENABLED')) { ?>
        <tr>
            <td>Version Netzwerk-Modul</td>
            <td><?php echo $versionNetworkPlugin; ?></td>
        </tr>
        <?php } ?>

        <tr>
            <td>app.cakeServerName</td>
            <td><a target="_blank"
                href="<?php echo Configure::read('AppConfig.cakeServerName'); ?>"><?php echo Configure::read('AppConfig.cakeServerName'); ?></a></td>
        </tr>
        

        <tr>
            <td>app.emailOrderReminderEnabled</td>
            <td><?php echo Configure::read('AppConfig.emailOrderReminderEnabled') ? 'ja' : 'nein'; ?></td>
        </tr>

        <tr>
            <td>app.registrationNotificationEmails</td>
            <td><?php echo join(', ', Configure::read('AppConfig.registrationNotificationEmails')); ?></td>
        </tr>



        <tr>
            <td>app.adminEmail / app.adminPassword</td>
            <td><?php echo Configure::read('AppConfig.adminEmail'); ?> / <?php echo preg_replace("|.|", "*", Configure::read('AppConfig.adminPassword')); ?></td>
        </tr>

        <tr>
            <td>app/Config/email.php</td>
            <?php
            require_once(APP . 'Config' . DS . 'email.php');
            $email = new EmailConfig();
            ?>
            <td>
            <?php if (isset($email->default['host'])) { ?>
                <b>Host:</b> <?php echo $email->default['host']; ?><br />
            <?php } ?>
            <?php if (isset($email->default['username'])) { ?>
                <b>Username:</b> <?php echo $email->default['username']; ?><br />
            <?php } ?>
            <b>Log:</b> <?php echo (isset($email->default['log']) && $email->default['log']) ? 'on' : 'off'; ?></td>
        </tr>

        <tr>
            <td>app.additionalOrderStatusChangeInfo</td>
            <td><?php echo Configure::read('AppConfig.additionalOrderStatusChangeInfo'); ?></td>
        </tr>

        <tr>
            <td>app.allowManualOrderListSending</td>
            <td><?php echo Configure::read('AppConfig.allowManualOrderListSending') ? 'ja' : 'nein'; ?></td>
        </tr>

        <tr>
            <td>app.sendOrderListsWeekday</td>
            <td><?php echo $this->MyTime->getWeekdayName(Configure::read('AppConfig.sendOrderListsWeekday')); ?></td>
        </tr>

        <tr>
            <td>Abholtag</td>
            <td><?php echo $this->MyTime->getWeekdayName(Configure::read('AppConfig.sendOrderListsWeekday') + Configure::read('AppConfig.deliveryDayDelta')); ?> (app.sendOrderListsWeekday + app.deliveryDayDelta)</td>
        </tr>

        <tr>
            <td>app.paymentMethods</td>
            <td><?php echo join(', ', Configure::read('AppConfig.paymentMethods')); ?></td>
        </tr>

        <tr>
            <td>app.visibleOrderStates</td>
            <td><?php echo json_encode(Configure::read('AppConfig.visibleOrderStates')); ?></td>
        </tr>

        <tr>
            <td>app.memberFeeEnabled</td>
            <td><?php echo Configure::read('AppConfig.memberFeeEnabled') ? 'ja' : 'nein'; ?></td>
        </tr>

        <tr>
            <td>app.isDepositPaymentCashless</td>
            <td><?php echo Configure::read('AppConfig.isDepositPaymentCashless') ? 'ja' : 'nein'; ?></td>
        </tr>

        <?php if (Configure::read('AppConfig.isDepositPaymentCashless')) { ?>
            <tr>
            <td>app.depositPaymentCashlessStartDate</td>
            <td><?php echo date('d.m.Y', strtotime(Configure::read('AppConfig.depositPaymentCashlessStartDate'))); ?></td>
        </tr>
        <?php } ?>

        <tr>
            <td>app.depositForManufacturersStartDate</td>
            <td><?php echo date('d.m.Y', strtotime(Configure::read('AppConfig.depositForManufacturersStartDate'))); ?></td>
        </tr>

        <tr>
            <td>app.customerMainNamePart</td>
            <td><?php echo Configure::read('AppConfig.customerMainNamePart'); ?></td>
        </tr>

        <?php
        if ($this->elementExists('latestGitCommit')) {
            echo '<tr>';
            echo '<td>Software-Update / Version</td>';
            echo '<td>';
            echo nl2br($this->element('latestGitCommit'));
            echo 'Mehr Informationen zu den Änderungen findest du im <a href="https://www.foodcoopshop.com/changelog" target="_blank">Changelog</a>.';
            echo '</td>';
            echo '</tr>';
        }
        ?>

        <tr>
            <td>app.emailErrorLoggingEnabled</td>
            <td><?php echo Configure::read('AppConfig.emailErrorLoggingEnabled') ? 'ja' : 'nein'; ?></td>
        </tr>

        <tr>
            <td>app.defaultTax</td>
            <td><?php echo $this->Html->formatAsPercent($defaultTax['Tax']['rate']); ?> - <?php echo $defaultTax['Tax']['active'] ? 'aktiviert' : 'deaktiviert'; ?></td>
        </tr>

        <tr>
            <td>Logo für Webseite (Breite: 260px)<br /><?php echo Configure::read('AppConfig.cakeServerName'); ?>/files/images/logo.jpg</td>
            <td><img
                src="<?php echo Configure::read('AppConfig.cakeServerName'); ?>/files/images/logo.jpg" /></td>
        </tr>

        <tr>
            <td>Logo für Bestelllisten und Rechnungen (Breite: 260px)<br /><?php echo Configure::read('AppConfig.cakeServerName'); ?>/files/images/logo-pdf.jpg</td>
            <td><img src="/files/images/logo-pdf.jpg" /></td>
        </tr>

        <tr>
            <td>Default-Bild für Produkte (Liste, 150x150)<br /><?php echo Configure::read('AppConfig.cakeServerName'); ?>/files/images/products/de-default-home_default.jpg</td>
            <td><img src="/files/images/products/de-default-home_default.jpg" /></td>
        </tr>

        <tr>
            <td>Default-Bild für Hersteller (Liste: 125x125)<br /><?php echo Configure::read('AppConfig.cakeServerName'); ?>/files/images/manufacturers/de-default-medium_default.jpg</td>
            <td><img
                src="/files/images/manufacturers/de-default-medium_default.jpg" /></td>
        </tr>

        <tr>
            <td>Default-Bild für Aktuelles-Beitrag (Home, 150x113)<br /><?php echo Configure::read('AppConfig.cakeServerName'); ?>/files/images/blog_posts/no-home-default.jpg</td>
            <td><img src="/files/images/blog_posts/no-home-default.jpg" /></td>
        </tr>

    </table>

</div>

<?php

namespace App\Model\Table;

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
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class CategoryProductsTable extends AppTable
{

    public function initialize(array $config)
    {
        $this->setTable('category_product');
        parent::initialize($config);
        $this->setPrimaryKey('id_product');
        $this->belongsTo('Categories', [
            'foreignKey' => 'id_category'
        ]);
    }
}

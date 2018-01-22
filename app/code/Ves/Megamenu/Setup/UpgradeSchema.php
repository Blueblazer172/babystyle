<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableItems = $installer->getTable('ves_megamenu_item');

        $installer->getConnection()->addColumn(
            $tableItems,
            'hover_icon',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Hover Icon'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgcolor',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Background Color'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgimage',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Bakground Image'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgimage',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Bakground Image'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgimagerepeat',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Bakground Image Repeat'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgpositionx',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Background Position X'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_bgpositiony',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Background Position Y'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'dropdown_inlinecss',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Dropdown Inline CSS'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableItems,
            'parentcat',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Parent Category'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableItems,
            'animation_in',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Animation In'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableItems,
            'animation_time',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Animation Time'
            ]
        );

        $installer->getConnection()->modifyColumn(
            $tableItems,
            'id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                'auto_increment' => true,
                'primary' => true,
                'nullable' => false
            ]
            );

        $tableMenu = $installer->getTable('ves_megamenu_menu');
        $installer->getConnection()->addColumn(
            $tableMenu,
            'desktop_template',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Desktop Template'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableMenu,
            'disable_iblocks',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Disable Item Blocks'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableMenu,
            'event',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Event'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableMenu,
            'classes',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Classes'
            ]
        );
        $installer->getConnection()->addColumn(
            $tableMenu,
            'width',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Width'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableMenu,
            'scrolltofixed',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Scroll to fixed'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableMenu,
            'current_version',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Current Version'
            ]
        );

        $installer->getConnection()->addColumn(
            $tableMenu,
            'mobile_menu_alias',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Mobile menu alias'
            ]
        );
        $installer->endSetup();
        


        /**
         * Create table 'ves_megamenu_menu_customergroup'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_megamenu_menu_customergroup')
        )->addColumn(
            'menu_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Menu ID'
        )->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group ID'
        )->addIndex(
            $installer->getIdxName('ves_megamenu_menu_customergroup', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $installer->getFkName('ves_megamenu_menu_customergroup', 'menu_id', 'ves_megamenu_menu', 'menu_id'),
            'menu_id',
            $installer->getTable('ves_megamenu_menu'),
            'menu_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('ves_megamenu_menu_customergroup', 'customer_group_id', 'customer_group', 'customer_group_id'),
            'customer_group_id',
            $installer->getTable('customer_group'),
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Menu Custom Group'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'ves_megamenu_menu_log'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ves_megamenu_menu_log')
        )->addColumn(
            'log_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true, 'identity' => true],
            'Log ID'
        )->addColumn(
            'menu_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Menu ID'
        )->addColumn(
            'version',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['unsigned' => true],
            'Menu Data'
        )->addColumn(
            'menu_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '256k',
            ['unsigned' => true],
            'Menu Data'
        )->addColumn(
            'menu_structure',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '256k',
            ['unsigned' => true],
            'Menu Structure'
        )->addColumn(
            'note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['unsigned' => true],
            'Menu Note'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Menu Modification Time'
        )->setComment(
            'Menu Log'
        );
        $installer->getConnection()->createTable($table);
    }
}
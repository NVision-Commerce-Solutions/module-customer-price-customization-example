<?php

declare(strict_types=1);

namespace MyCustom\CustomerPrice\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Backend\Weight;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddCustomProductAttributes implements DataPatchInterface
{
    private EavSetupFactory $eavSetupFactory;
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        $attributeSetup = $this->eavSetupFactory->create();

        $this->moduleDataSetup->startSetup();
        $attributes = [
            'acadon_length' => 'Acadon Length',
            'acadon_width' => 'Acadon Width',
            'acadon_thickness' => 'Acadon Thickness',
            'acadon_diameter' => 'Acadon Diameter'
        ];

        foreach ($attributes as $name => $label) {
            $attributeSetup->addAttribute(
                Product::ENTITY,
                $name,
                [
                    'type' => 'decimal',
                    'label' => $label,
                    'input' => 'weight',
                    'backend' => Weight::class,
                    'sort_order' => 100,
                    'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    'searchable' => true,
                    'filterable' => true,
                    'required' => false,
                    'visible_in_advanced_search' => true,
                    'used_in_product_listing' => true,
                    'used_for_sort_by' => true,
                    'apply_to' => 'simple,virtual',
                    'group' => 'General',
                ]
            );
        }


        $this->moduleDataSetup->endSetup();
    }
}

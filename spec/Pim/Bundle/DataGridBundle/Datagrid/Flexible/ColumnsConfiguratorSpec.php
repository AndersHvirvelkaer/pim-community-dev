<?php

namespace spec\Pim\Bundle\DataGridBundle\Datagrid\Flexible;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Configuration as FormatterConfiguration;
use Pim\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Pim\Bundle\CatalogBundle\Entity\Attribute;
use Pim\Bundle\DataGridBundle\Datagrid\Flexible\ConfigurationRegistry;

class ColumnsConfiguratorSpec extends ObjectBehavior
{
    function let(DatagridConfiguration $configuration, ConfigurationRegistry $registry)
    {
        $this->beConstructedWith($configuration, $registry);
    }

    function it_is_a_configurator()
    {
        $this->shouldBeAnInstanceOf('Pim\Bundle\DataGridBundle\Datagrid\Flexible\ConfiguratorInterface');
    }

    function it_configures_datagrid_columns(DatagridConfiguration $configuration, ConfigurationRegistry $registry)
    {
        $registry->getConfiguration('pim_catalog_identifier')->willReturn(array('column' => array('identifier_config')));
        $registry->getConfiguration('pim_catalog_text')->willReturn(array('column' => array('text_config')));

        $columnConfPath = sprintf('[%s]', FormatterConfiguration::COLUMNS_KEY);
        $columns = [
            'sku' => [
                'identifier_config',
                'label' => 'Sku'
            ],
            'family' => [
                'family_config',
            ],
            'name' => [
                'text_config',
                'label' => 'Name'
            ]
        ];
        $configuration->offsetGetByPath($columnConfPath)->willReturn(array('family' => array('family_config')));

        $attributes = [
            'sku' => [
                'code'  => 'sku',
                'label' => 'Sku',
                'useableAsGridColumn' => 1,
                'attributeType' => 'pim_catalog_identifier'
            ],
            'name' => [
                'code'  => 'name',
                'label' => 'Name',
                'useableAsGridColumn' => 1,
                'attributeType' => 'pim_catalog_text'
            ],
            'desc' => [
                'code'  => 'desc',
                'label' => 'Desc',
                'useableAsGridColumn' => 0,
                'attributeType' => 'pim_catalog_text'
            ],
        ];
        $configuration->offsetGetByPath(OrmDatasource::USEABLE_ATTRIBUTES_PATH)->willReturn($attributes);

        $configuration->offsetSetByPath($columnConfPath, $columns)->shouldBeCalled();
        $this->configure();
    }

    function it_doesnt_add_column_for_not_useable_as_column_attribute(DatagridConfiguration $configuration, ConfigurationRegistry $registry)
    {
        $registry->getConfiguration('pim_catalog_identifier')->willReturn(array('column' => array('identifier_config')));
        $registry->getConfiguration('pim_catalog_text')->willReturn(array('column' => array('text_config')));

        $columnConfPath = sprintf('[%s]', FormatterConfiguration::COLUMNS_KEY);
        $columns = [
            'family' => [
                'family_config',
            ],
        ];
        $configuration->offsetGetByPath($columnConfPath)->willReturn(array('family' => array('family_config')));

        $attributes = [
            'sku' => [
                'code'  => 'sku',
                'label' => 'Sku',
                'useableAsGridColumn' => 0,
                'attributeType' => 'pim_catalog_identifier'
            ],
            'name' => [
                'code'  => 'name',
                'label' => 'Name',
                'useableAsGridColumn' => 0,
                'attributeType' => 'pim_catalog_text'
            ],
        ];
        $configuration->offsetGetByPath(OrmDatasource::USEABLE_ATTRIBUTES_PATH)->willReturn($attributes);

        $configuration->offsetSetByPath($columnConfPath, $columns)->shouldBeCalled();
        $this->configure();
    }

    function it_cannot_handle_misconfigured_attribute_type(DatagridConfiguration $configuration, ConfigurationRegistry $registry)
    {
        $registry->getConfiguration('pim_catalog_identifier')->willReturn(array('column' => array('identifier_config')));
        $registry->getConfiguration('pim_catalog_text')->willReturn(array());

        $columnConfPath = sprintf('[%s]', FormatterConfiguration::COLUMNS_KEY);
        $configuration->offsetGetByPath($columnConfPath)->willReturn(array('family' => array('family_config')));

        $attributes = [
            'sku' => [
                'code'  => 'sku',
                'label' => 'Sku',
                'useableAsGridColumn' => 0,
                'attributeType' => 'pim_catalog_identifier'
            ],
            'name' => [
                'code'  => 'name',
                'label' => 'Name',
                'useableAsGridColumn' => 0,
                'attributeType' => 'pim_catalog_text'
            ],
        ];
        $configuration->offsetGetByPath(OrmDatasource::USEABLE_ATTRIBUTES_PATH)->willReturn($attributes);

        $this->shouldThrow('\LogicException')->duringConfigure();
    }
}

<?php

namespace spec\Pim\Bundle\DataGridBundle\Extension\Selector\Orm;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Pim\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Pim\Bundle\DataGridBundle\Extension\Selector\Orm\FlexibleValuesSelector;

class FlexibleMediaSelectorSpec extends ObjectBehavior
{
    function let(FlexibleValuesSelector $predecessor)
    {
        $this->beConstructedWith($predecessor);
    }

    function it_should_be_a_selector()
    {
        $this->shouldBeAnInstanceOf('Pim\Bundle\DataGridBundle\Extension\Selector\SelectorInterface');
    }

    function it_applies_join_on_datasource_query(FlexibleValuesSelector $predecessor, OrmDatasource $datasource, DatagridConfiguration $configuration, QueryBuilder $queryBuilder)
    {
        $datasource->getQueryBuilder()->willReturn($queryBuilder);
        $queryBuilder->leftJoin('values.media', 'media')->willReturn($queryBuilder);
        $queryBuilder->addSelect('media')->willReturn($queryBuilder);
        $this->apply($datasource, $configuration);
    }
}

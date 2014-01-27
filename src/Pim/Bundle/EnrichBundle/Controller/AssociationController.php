<?php

namespace Pim\Bundle\EnrichBundle\Controller;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Pim\Bundle\CatalogBundle\Manager\ProductManager;
use Pim\Bundle\CatalogBundle\Manager\LocaleManager;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;

/**
 * Association controller
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AssociationController
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var ProductManager
     */
    protected $productManager;

    /**
     * @var LocaleManager
     */
    protected $localeManager;

    /**
     * Constructor
     *
     * @param RegistryInterface $doctrine
     * @param EngineInterface   $templating
     * @param ProductManager    $productManager
     * @param LocaleManager     $localeManager
     */
    public function __construct(
        RegistryInterface $doctrine,
        EngineInterface $templating,
        ProductManager $productManager,
        LocaleManager $localeManager
    ) {
        $this->doctrine        = $doctrine;
        $this->templating      = $templating;
        $this->productManager  = $productManager;
        $this->localeManager   = $localeManager;
    }

    /**
     * Display association grids
     *
     * @param integer $id
     *
     * @AclAncestor("pim_enrich_associations_view")
     *
     * @return Response
     */
    public function associationsAction($id)
    {
        $product = $this->findProductOr404($id);

        $this->productManager->ensureAllAssociationTypes($product);

        $associationTypes = $this->doctrine->getRepository('PimCatalogBundle:AssociationType')->findAll();

        return $this->templating->renderResponse(
            'PimEnrichBundle:Association:_associations.html.twig',
            array(
                'product'          => $product,
                'associationTypes' => $associationTypes,
                'dataLocale'       => $this->localeManager->getDataLocale(),
            )
        );
    }

    /**
     * Find a product by its id or return a 404 response
     *
     * @param integer $id the product id
     *
     * @return ProductInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findProductOr404($id)
    {
        $product = $this->productManager->find($id);

        if (!$product) {
            throw new NotFoundHttpException(
                sprintf('Product with id %d could not be found.', $id)
            );
        }

        return $product;
    }
}
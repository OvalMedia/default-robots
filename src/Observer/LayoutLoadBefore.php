<?php
declare(strict_types=1);

namespace OM\DefaultRobots\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class LayoutLoadBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected Http $_request;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $_categoryRepository;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Http $request,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->_request = $request;
        $this->_categoryRepository = $categoryRepository;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var $layout \Magento\Framework\View\Layout */
        $layout = $observer->getEvent()->getLayout();

        if ($this->_request->getFullActionName() == 'catalog_category_view') {
            $catId = $this->_request->getParam('id', false);

            try {
                $category = $this->_categoryRepository->get($catId);
            } catch (NoSuchEntityException $e) {}

            if ($category && !$category->getIncludeInMenu()) {
                $layout->getUpdate()->addHandle('noindex_follow');
            }

            if (!empty($_GET['p'])) {
                $layout->getUpdate()->addHandle('noindex_follow');
            }
        }
    }
}
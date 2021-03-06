<?php

namespace ChrisMallory\CodeSample\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use ChrisMallory\CodeSample\Model\ResourceModel\Data\CollectionFactory as DataFactory;
use Magento\Framework\Serialize\Serializer\Json as Serializer;

/**
 * Class Index
 *
 * @package ChrisMallory\CodeSample\Block
 */
class Index extends Template
{
    /**
     * @var array|\Magento\Checkout\Block\Checkout\LayoutProcessorInterface[]
     */
    protected $_layoutProcessors;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var DataFactory
     */
    protected $_dataFactory;

    /**
     * @var Serializer
     */
    protected $_serializer;

    /**
     * Index constructor.
     *
     * @param ScopeConfigInterface $_scopeConfig
     * @param Template\Context     $context
     * @param DataFactory          $_dataFactory
     * @param Serializer           $_serializer
     * @param array                $_layoutProcessors
     * @param array                $data
     */
    public function __construct(
        ScopeConfigInterface $_scopeConfig,
        Template\Context $context,
        DataFactory $_dataFactory,
        Serializer $_serializer,
        array $_layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_scopeConfig = $_scopeConfig;
        $this->_dataFactory = $_dataFactory;
        $this->_serializer = $_serializer;
        $this->jsLayout = isset($data['jsLayout'])
        && is_array(
            $data['jsLayout']
        ) ? $data['jsLayout'] : [];
        $this->_layoutProcessors = $_layoutProcessors;
    }


    /**
     * @return mixed
     */
    private function _getCollection()
    {
        return $this->_dataFactory->create();
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->_layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * @return mixed
     */
    public function getNotice()
    {
        return $this->_serializer->serialize($this->_scopeConfig->getValue(
            'codesample/general/notice_text'
        ));
    }

    /**
     * @return bool|false|string
     */
    public function getProductCollectionJsonData()
    {
        $_productCollection = $this->_getCollection();
        $i = 0;
        $productDataArray = array();
        foreach ($_productCollection as $product) {
            $productDataArray[$i] = array(
                'entity_id'       => $product['entity_id'],
                'product_sku'     => $product['product_sku'],
                'additional_text' => $product['additional_text']
            );
            $i++;
        }
        return $this->_serializer->serialize($productDataArray);
    }
}
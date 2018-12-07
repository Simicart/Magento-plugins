<?php
namespace Simi\Instantsearch\Block;
class Search extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \Simi\Instantsearch\Helper\Data
     */
    protected $helperData;

    public function __construct(
        \Simi\Instantsearch\Helper\Data $helperData,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {

        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    // public function getSearchDelay()
    // {
    //     return $this->helperData->getSearchDelay();
    // }

    public function getSearchUrl($path)
    {
        return $this->getUrl($path);
    }

}
?>
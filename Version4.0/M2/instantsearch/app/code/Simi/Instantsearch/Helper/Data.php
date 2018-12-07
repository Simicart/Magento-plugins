<?php

namespace Simi\Instantsearch\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Search Suite Autocomplete config data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * XML config path search delay
     */
    

    /**
     * Path to store config where count of connector posts per page is stored
     *
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE = 'simiconnector/view/items_per_page';

    /**
     * Media path to extension images
     *
     * @var string
     */
    const MEDIA_PATH = 'Simiconnector';

    /**
     * Maximum size for image in bytes
     * Default value is 1M
     *
     * @var int
     */
    const MAX_FILE_SIZE = 8388608;

    /**
     * Manimum image height in pixels
     *
     * @var int
     */
    const MIN_HEIGHT = 10;

    /**
     * Maximum image height in pixels
     *
     * @var int
     */
    const MAX_HEIGHT = 6000;

    /**
     * Manimum image width in pixels
     *
     * @var int
     */
    const MIN_WIDTH = 10;

    /**
     * Maximum image width in pixels
     *
     * @var int
     */
    const MAX_WIDTH = 10000;

    /**
     * Array of image size limitation
     *
     * @var array
     */
    public $imageSize = [
        'minheight' => self::MIN_HEIGHT,
        'minwidth' => self::MIN_WIDTH,
        'maxheight' => self::MAX_HEIGHT,
        'maxwidth' => self::MAX_WIDTH,
    ];

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    public $mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    public $filesystem;

    /**
     * @var \Magento\Framework\HTTP\Adapter\FileTransferFactory
     */
    public $httpFactory;

    /**
     * File Uploader factory
     *
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    public $fileUploaderFactory;

    /**
     * File Uploader factory
     *
     * @var \Magento\Framework\Io\File
     */
    public $ioFile;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    
    

    /*
     * Object Mangager
     *
     *
     */
    public $simiObjectManager;
    public $resource;


    /**
     * Retrieve search delay
     *
     * @param int|null $storeId
     * @return int
     */
    public function countArray($array)
    {
        return count($array);
    }

    
}

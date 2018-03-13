<?php

namespace Simi\Simiapicache\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $mediaDirectory;
    public $filesystem;
    public $httpFactory;
    public $fileUploaderFactory;
    public $ioFile;
    public $storeManager;
    public $scopeConfig;
    public $simiObjectManager;
    public $resource;

    public $directionList;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        DirectoryList $directoryList
    ) {

        $this->simiObjectManager = $simiObjectManager;
        $this->scopeConfig = $this->simiObjectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->filesystem = $this->simiObjectManager->create('\Magento\Framework\Filesystem');
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->httpFactory = $this->simiObjectManager->create('\Magento\Framework\HTTP\Adapter\FileTransferFactory');
        $this->fileUploaderFactory = $this->simiObjectManager
            ->create('\Magento\MediaStorage\Model\File\UploaderFactory');
        $this->ioFile = $this->simiObjectManager->create('\Magento\Framework\Filesystem\Io\File');
        $this->storeManager = $this->simiObjectManager->create('\Magento\Store\Model\StoreManagerInterface');
        $this->_imageFactory = $this->simiObjectManager->create('\Magento\Framework\Image\Factory');
        $this->resource = $this->simiObjectManager->create('\Magento\Framework\App\ResourceConnection');
        $this->resourceFactory = $this->simiObjectManager
            ->create('\Magento\Reports\Model\ResourceModel\Report\Collection\Factory');
        $this->directionList = $directoryList;
        parent::__construct($context);
    }

    /*
     * Get Store Config Value
     */

    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    /*
     * Flush cache
     */
    public function flushCache()
    {
        $path = $this->directionList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'Simiapicache'
            . DIRECTORY_SEPARATOR . 'json';
        if (is_dir($path)) {
            $this->_removeFolder($path);
        }
    }

    private function _removeFolder($folder)
    {
        if (is_dir($folder)) {
            $dir_handle = opendir($folder);
        }
        if (!$dir_handle) {
            return false;
        }
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($folder . "/" . $file)) {
                    unlink($folder . "/" . $file);
                } else {
                    $this->_removeFolder($folder . '/' . $file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($folder);
        return true;
    }
}

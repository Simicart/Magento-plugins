<?php

/**
 * Simipushnotif Helper
 */

namespace Simi\Simipushnotif\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use \Magento\Framework\DataObject;
use \Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const BUILD_TYPE_SANDBOX = 'sandbox';
    const BUILD_TYPE_LIVE = 'live';

    public $directionList;
    public $objectManager;
    public $storeManager;
    public $httpFactory;
    public $countryCollectionFactory;
    public $fileUploaderFactory;
    public $filesystem;

    public function __construct(
        Context $context,
        ObjectManagerInterface $manager,
        DirectoryList $directoryList,
        StoreManager $storemanager,
        CountryCollectionFactory $countryCollectionFactory
    )
    {
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->directionList = $directoryList;
        $this->objectManager = $manager;
        $this->storeManager = $storemanager;
        $this->httpFactory = $this->objectManager->create('\Magento\Framework\HTTP\Adapter\FileTransferFactory');
        $this->fileUploaderFactory = $this->objectManager
            ->create('\Magento\MediaStorage\Model\File\UploaderFactory');
        $this->filesystem = $this->objectManager->create('\Magento\Framework\Filesystem');
        parent::__construct($context);
    }


    /**
     * Upload image and return uploaded image file name or false
     *
     * @throws Mage_Core_Exception
     * @param string $scope the request key for file
     * @return bool|string
     */
    public function uploadImage($scope)
    {
        $adapter = $this->httpFactory->create();
        if ($adapter->isUploaded($scope)) {
            if (!$adapter->isValid($scope)) {
                throw new \Simi\Simipushnotif\Helper\SimiException(__('Uploaded image is not valid.'));
            }
            $uploader = $this->fileUploaderFactory->create(['fileId' => $scope]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->setAllowCreateFolders(true);
            $ext = $uploader->getFileExtension();
            if ($uploader->save($this->getBaseDir(), $scope . time() . '.' . $ext)) {
                return 'Simipushnotif/' . $uploader->getUploadedFileName();
            }
        }
        return false;
    }

    public function getBaseDir()
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath('Simipushnotif');
        return $path;
    }

    public function getCountryCollection()
    {
        return $this->countryCollectionFactory->create();
    }

    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getMediaUrl($media_path)
    {
        return $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . $media_path;
    }
}

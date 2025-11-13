<?php
/**
 * Compatibility shim for Zend_Cache
 */

use Laminas\Cache\Storage\Adapter\Filesystem;
use Laminas\Cache\Storage\Adapter\Memory;
use Laminas\Cache\Storage\StorageInterface;

class Zend_Cache
{
    /**
     * Factory to create cache
     */
    public static function factory($frontend, $backend, $frontendOptions = [], $backendOptions = [])
    {
        try {
            $storage = null;

            switch ($backend) {
                case 'File':
                    $options = [];
                    if (isset($backendOptions['cache_dir'])) {
                        $options['cache_dir'] = $backendOptions['cache_dir'];
                    }
                    $storage = new Filesystem($options);
                    break;

                case 'Memcached':
                case 'Redis':
                case 'Apc':
                    // For now, fall back to memory adapter
                    $storage = new Memory();
                    break;

                default:
                    // Default to filesystem
                    $storage = new Filesystem();
                    break;
            }

            return new Zend_Cache_Core($storage);
        } catch (\Exception $e) {
            throw new Zend_Cache_Exception($e->getMessage(), 0, $e);
        }
    }
}

/**
 * Cache core wrapper
 */
class Zend_Cache_Core
{
    /**
     * @var StorageInterface
     */
    protected $_storage;

    public function __construct(StorageInterface $storage)
    {
        $this->_storage = $storage;
    }

    /**
     * Load data from cache
     */
    public function load($id)
    {
        try {
            return $this->_storage->getItem($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Save data to cache
     */
    public function save($data, $id, $tags = [], $specificLifetime = null)
    {
        try {
            return $this->_storage->setItem($id, $data);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove from cache
     */
    public function remove($id)
    {
        try {
            return $this->_storage->removeItem($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clean cache
     */
    public function clean($mode = 'all', $tags = [])
    {
        try {
            if ($mode === 'all') {
                return $this->_storage->flush();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

/**
 * Exception class
 */
class Zend_Cache_Exception extends Exception
{
}

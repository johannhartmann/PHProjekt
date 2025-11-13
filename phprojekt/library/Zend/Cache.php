<?php
/**
 * Compatibility shim for Zend_Cache
 */

use Laminas\Cache\StorageFactory;
use Laminas\Cache\Storage\StorageInterface;

class Zend_Cache
{
    /**
     * Factory to create cache
     */
    public static function factory($frontend, $backend, $frontendOptions = [], $backendOptions = [])
    {
        // Map Zend cache types to Laminas
        $backendMap = [
            'File' => 'filesystem',
            'Memcached' => 'memcached',
            'Redis' => 'redis',
            'Apc' => 'apcu',
        ];

        $adapter = $backendMap[$backend] ?? 'filesystem';

        // Prepare Laminas config
        $config = [
            'adapter' => $adapter,
            'options' => []
        ];

        // Map backend options
        if (isset($backendOptions['cache_dir'])) {
            $config['options']['cache_dir'] = $backendOptions['cache_dir'];
        }

        try {
            $storage = StorageFactory::factory($config);
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

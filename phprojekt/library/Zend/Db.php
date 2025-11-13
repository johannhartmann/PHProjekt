<?php
/**
 * Compatibility shim for Zend_Db
 */

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;

class Zend_Db
{
    /**
     * Factory for Laminas Db Adapter
     *
     * @param string|array|Zend_Config $adapter Adapter name or config
     * @param array|Zend_Config $config Configuration array or Zend_Config
     * @return AdapterInterface
     */
    public static function factory($adapter, $config = [])
    {
        // Convert Zend_Config to array
        if ($adapter instanceof Zend_Config) {
            $adapter = $adapter->toArray();
        }
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        // If $adapter is an array, it contains the config
        if (is_array($adapter)) {
            $config = $adapter;
            $adapter = $config['adapter'] ?? 'Pdo_Mysql';
        }

        // Convert Zend adapter names to Laminas
        $adapterMap = [
            'Pdo_Mysql' => 'Pdo_Mysql',
            'Pdo_Pgsql' => 'Pdo_Pgsql',
            'Pdo_Sqlite' => 'Pdo_Sqlite',
            'Mysqli' => 'Mysqli',
        ];

        $laminasAdapter = $adapterMap[$adapter] ?? 'Pdo_Mysql';

        // Convert Zend config format to Laminas format
        $laminasConfig = [
            'driver' => $laminasAdapter,
        ];

        // Handle database parameters
        if (isset($config['params'])) {
            $params = $config['params'];

            // Convert nested Zend_Config to array
            if ($params instanceof Zend_Config) {
                $params = $params->toArray();
            }

            if (isset($params['host'])) {
                $laminasConfig['hostname'] = $params['host'];
            }
            if (isset($params['username'])) {
                $laminasConfig['username'] = $params['username'];
            }
            if (isset($params['password'])) {
                $laminasConfig['password'] = $params['password'];
            }
            if (isset($params['dbname'])) {
                $laminasConfig['database'] = $params['dbname'];
            }
            if (isset($params['charset'])) {
                $laminasConfig['charset'] = $params['charset'];
            }
            if (isset($params['port'])) {
                $laminasConfig['port'] = $params['port'];
            }
        }

        // Create and return Laminas adapter
        return new Adapter($laminasConfig);
    }
}

/**
 * Exception class for Zend_Db_Adapter
 */
class Zend_Db_Adapter_Exception extends Exception
{
}

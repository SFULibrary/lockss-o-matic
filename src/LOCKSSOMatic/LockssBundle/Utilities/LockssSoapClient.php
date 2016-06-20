<?php

namespace LOCKSSOMatic\LockssBundle\Utilities;

use Exception;
use SoapClient;
use SoapFault;

class LockssSoapClient
{
    private $wsdl;
    private $options;
    private $errors;
    private $client;

    public function __construct()
    {
        $this->wsdl = null;
        $this->errors = array();
        $this->client = null;

        $this->options = array(
            'soap_version' => SOAP_1_1,
            'trace' => true,
            'exceptions' => true,
            'cache' => WSDL_CACHE_BOTH,
        );
    }

    public function setWsdl($wsdl)
    {
        $this->wsdl = $wsdl;
    }

    public function getWsdl()
    {
        return $this->wsdl;
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function getOption($key)
    {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return;
    }

    public function getErrors()
    {
        return implode("\n", $this->errors);
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    public function soapErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
    {
        $this->errors[] = "Soap Error: {$errmsg}";
    }

    public function soapExceptionHandler(Exception $e)
    {
        // Symfony\Component\Debug\Debug enables its own exception handler
        $this->errors[] = "Soap Exception: {$e->getMessage()}";
    }
    
    /**
     * @param string $method
     * @param array  $params
     *
     * @return stdClass|stdClass[]
     */
    public function call($method, $params = array())
    {
        $oldErrorHandler = set_error_handler(array($this, 'soapErrorHandler'));
        $oldExceptionHandler = set_exception_handler(array($this, 'soapExceptionHandler'));
        $response = null;
        try {
            $this->client = @new SoapClient($this->wsdl, $this->options);
            if ($this->client) {
                $response = $this->client->$method($params);
            }
        } catch (SoapFault $e) {
            $this->errors[] = "Soap Fault: {$e->getMessage()}";
            if ($this->client) {
                $this->errors[] = strstr($this->client->__getLastResponseHeaders(), "\n", true);
            }
            $this->errors[] = $e->getMessage();

            // Symfony is particularily aggressive about getting at this error.
            set_error_handler('var_dump', 0); // Never called because of empty mask.
            @trigger_error('');
            restore_error_handler();
        } catch (Exception $e) {
            $this->errors[] = "PHP Exception: {$e->getMessage()}";
        }
        set_error_handler($oldErrorHandler);
        set_exception_handler($oldExceptionHandler);

        return $response;
    }
}

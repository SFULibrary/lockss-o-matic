<?php

namespace LOCKSSOMatic\LockssBundle\Utilities;

use BeSimple\SoapClient\SoapClient;
use Exception;
use SoapFault;
use stdClass;

/**
 * LOCKSS Soap client. Works around most of the shortcomings in the PHP SOAP
 * system.
 */
class LockssSoapClient
{
    /**
     * URL for the soap definitions
     *
     * @var string
     */
    private $wsdl;

    /**
     * Soap client options array.
     *
     * @var array
     */
    private $options;

    /**
     * Errors in the most recently executed SOAP call.
     *
     * @var array
     */
    private $errors;

    /**
     * Clent to make the actual soap call.
     *
     * @var type
     */
    private $client;

    /**
     * Construct the LOCKSS client.
     */
    public function __construct() {
        $this->wsdl = null;
        $this->errors = array();
        $this->client = null;

        $this->options = array(
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'soap_version' => SOAP_1_1,
            'trace' => true,
            'exceptions' => true,
            'cache' => WSDL_CACHE_BOTH,
            'authentication'  => SOAP_AUTHENTICATION_BASIC,
        );
    }

    /**
     * Set the WSDL URL.
     *
     * @param string $wsdl
     */
    public function setWsdl($wsdl) {
        $this->wsdl = $wsdl;
    }

    /**
     * Get the WSDL URL.
     *
     * @return string
     */
    public function getWsdl() {
        return $this->wsdl;
    }

    /**
     * Set an option
     *
     * @param string $key
     * @param string $value
     */
    public function setOption($key, $value) {
        $this->options[$key] = $value;
    }

    /**
     * Get an option.
     *
     * @param string $key
     * @return string|null
     */
    public function getOption($key) {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return null;
    }

    /**
     * Get the errors from the soap call as a string.
     *
     * @return type
     */
    public function getErrors() {
        $errors = array_map(function($s){
            return str_replace("\n", "", $s);            
        }, array_filter($this->errors));
        return implode("\n\n", $errors);
    }

    /**
     * Check if the soap call had errors.
     *
     * @return boolean
     */
    public function hasErrors() {
        return count($this->errors) > 0;
    }

    /**
     * Call back function to register one error.
     *
     * @param int $errno
     * @param string $errmsg
     * @param string $filename
     * @param int $linenum
     * @param mixed $vars
     */
    public function soapErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
        $this->errors[] = "Soap Error: {$errno}: {$errmsg}";
    }

    /**
     * Callback function to register exceptions.
     *
     * @param Exception $e
     */
    public function soapExceptionHandler(Exception $e) {
        $this->errors[] = "Soap Exception: {$e->getMessage()}";
    }

    /**
     * Call a SOAP method. So gross.
     *
     * @param string $method
     * @param array  $params
     * @param array $attachments unused
     *
     * @return stdClass|stdClass[]
     */
    public function call($method, $params = array(), $attachments = null) {
        $oldErrorHandler = set_error_handler(array($this, 'soapErrorHandler'));
        $oldExceptionHandler = set_exception_handler(array($this, 'soapExceptionHandler'));
        $response = null;
        try {
            // @codingStandardsIgnoreLine
            $this->client = @new SoapClient($this->wsdl, $this->options);
            if($this->client) {
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

            // @codingStandardsIgnoreLine
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

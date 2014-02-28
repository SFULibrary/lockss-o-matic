<?php

/**
 * Simplified version of the Monoglog LineFormatter.php
 * (c) Jordi Boggiano <j.boggiano@seld.be>.
 * 
 * Custom formatter for LOCKSS-O-Matic.
 */

namespace LOCKSSOMatic\CoreBundle\DependencyInjection;

use Monolog\Formatter\NormalizerFormatter;

/**
 * Formats incoming records into a tab-delimited string.
 * $message is formatted by instances of LomLogFormatter.
 */
class LomLogFormatter extends NormalizerFormatter
{
    const FORMAT = "%datetime%\t%message%\n";

    protected $format;

    /**
     * @param string $format     The format of the message
     * @param string $dateFormat The format of the timestamp: one supported by DateTime::format
     */
    public function __construct($format = null, $dateFormat = null)
    {
        $this->format = $format ?: static::FORMAT;
        parent::__construct($dateFormat);
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $vars = parent::format($record);

        $output = $this->format;
        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%'.$var.'%')) {
                $output = str_replace('%'.$var.'%', $this->convertToString($val), $output);
            }
        }

        return $output;
    }

    public function formatBatch(array $records)
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    protected function normalize($data)
    {
        if (is_bool($data) || is_null($data)) {
            return var_export($data, true);
        }

        if ($data instanceof \Exception) {
            $previousText = '';
            if ($previous = $data->getPrevious()) {
                do {
                    $previousText .= ', '.get_class($previous).': '.$previous->getMessage().' at '.$previous->getFile().':'.$previous->getLine();
                } while ($previous = $previous->getPrevious());
            }

            return '[object] ('.get_class($data).': '.$data->getMessage().' at '.$data->getFile().':'.$data->getLine().$previousText.')';
        }

        return parent::normalize($data);
    }

    protected function convertToString($data)
    {
        if (null === $data || is_scalar($data)) {
            return (string) $data;
        }

        $data = $this->normalize($data);
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return $this->toJson($data, true);
        }

        return str_replace('\\/', '/', @json_encode($data));
    }
}

<?php

namespace LOCKSSOMatic\SwordBundle;

/**
 * This class documents the events thrown by the SWORD bundle. It has no
 * functionality of its own.
 */
final class SwordEvents
{
    /**
     * The sword.servicedoc event is thrown when a SWORD client requests the
     * service document via API.
     *
     * The event listener will receive a
     * LOCKSSOMatic\SwordBundle\Event\ServiceDocumentEvent object.
     *
     * @var string
     */
    const SERVICEDOC = 'sword.servicedoc';

    /**
     * The sword.depositcontent event is thrown for each item in a deposit
     * request received by the API.
     *
     * The event listener will receive a
     * LOCKSSOMatic\SwordBundle\Event\DepositContentEvent object.
     *
     * @var string
     */
    const DEPOSITCONTENT = 'sword.depositcontent';
}

<?php

namespace LOCKSSOMatic\SWORDBundle;

/**
 * This class documents the events thrown by the SWORD bundle. It has no
 * functionality of its own.
 */
final class SWORDEvents
{
    /**
     * The sword.servicedoc event is thrown when a SWORD client requests the
     * service document via API.
     *
     * The event listener will receive a
     * LOCKSSOMatic\SWORDBundle\Event\ServiceDocumentEvent object.
     *
     * @var string
     */
    const SERVICEDOC = 'sword.servicedoc';

    /**
     * The sword.depositcontent event is thrown for each item in a deposit
     * request received by the API.
     *
     * The event listener will receive a
     * LOCKSSOMatic\SWORDBundle\Event\DepositContentEvent object.
     *
     * @var string
     */
    const DEPOSITCONTENT = 'sword.depositcontent';
}

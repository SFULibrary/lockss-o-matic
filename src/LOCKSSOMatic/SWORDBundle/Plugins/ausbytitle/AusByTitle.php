<?php

namespace LOCKSSOMatic\SWORDBundle\Plugins\ausbytitle;

use LOCKSSOMatic\CRUDBundle\Entity\Aus;
use LOCKSSOMatic\CRUDBundle\Entity\ContentBuilder;
use LOCKSSOMatic\CRUDBundle\Entity\ContentProviders;
use LOCKSSOMatic\CRUDBundle\Entity\Deposits;
use LOCKSSOMatic\SWORDBundle\Plugins\DepositPlugin;
use LOCKSSOMatic\SWORDBundle\Event\DepositContentEvent;
use SimpleXMLElement;

class AusByTitle extends DepositPlugin
{

    public function getDescription()
    {
        return "Organize archival units by content title";
    }

    public function getName()
    {
        return "AusByTitle";
    }

    public function requiredAttributes() {
        return array_merge(array('title'), parent::requiredAttributes());
    }

    public function buildFilter($contentSize, $maxSize, $expr) {
        $self = $this;
        return function(Aus $au) use($self, $contentSize, $maxSize, $expr) {
            if ($au->getContentSize() + $contentSize >= $maxSize) {
                return false;
            }
            $data = $self->getData('AuParams', $au);
            if ($data === null) {
                return false;
            }
            if (!array_key_exists('ByTitle', $data) || $data['ByTitle'] !== true) {
                return false;
            }
            if (array_key_exists('expression', $data) && $data['expression'] === $expr) {
                return true;
            }
            return false;
        };
    }

    /**
     * A deposit has been received, so process it.
     *
     * @param DepositContentEvent $event
     */
    public function onDepositContent(DepositContentEvent $event)
    {
        // check that the deposit requested this plugin process it.
        if ($event->getPluginName() !== $this->getPluginId()) {
            return;
        }

        /** @var ContentProviders */
        $contentProvider = $event->getContentProvider();

        /** @var Deposits */
        $deposit = $event->getDeposit();

        /** @var SimpleXMLElement */
        $contentXml = $event->getXml();

        // Resepect the maximum AU size for the content provider.
        $maxSize = $contentProvider->getMaxAuSize();
        $contentSize = (string) $contentXml->attributes()->size;

        // get the content title from the deposit XML.
        $contentTitle = (string) $contentXml->attributes()->title;

        // get a list of the regular expressions from settings.yml
        $expressions = $this->getSetting('expressions');

        // attempt to match the content title against a regular expression, and
        // stop at the first match.
        $expr = null;
        for ($i = 0; $i < count($expressions); $i++) {
            if (preg_match($expressions[$i]['regex'], $contentTitle)) {
                $expr = $expressions[$i]['name'];
                break;
            }
        }

        // if there was no match, then fallback to the catchall setting.
        if ($expr === null) {
            $expr = $this->getSetting('catchall');
        }

        $filter = $this->buildFilter($contentSize, $maxSize, $expr);

        // Refresh the contentProvider before trying to filter the list of AUs,
        $this->container->get('doctrine')->getManager()->refresh($contentProvider);

        // apply the filter to the list of AUs.
        $aus = $contentProvider->getAus()->filter($filter);

        // If there's one or more AUs in the list, use the first one. There shouldn't
        // ever be more than one.
        if($aus->count() >= 1) {
            $au = $aus->first();
        } else {
            // There was no AU for this content item. So create one.
            $au = $this->buildAu(
                $contentProvider,
                'auid-title-' . $expr,
                'Created by AusByTitle for ' . $expr,
                'http://pln.example.com/foo/bar'
                );
            // store the plugin data about this AU.
            $this->setData('AuParams', $au, array(
                'ByTitle' => true,
                'expression' => $expr
            ));
        }

        // Deposit the content.
        $this->depositContent($contentXml, $deposit, $au);
    }

}

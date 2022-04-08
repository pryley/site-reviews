<?php

/**
 * League.Csv (https://csv.thephpleague.com).
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GeminiLabs\League\Csv;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMException;

/**
 * Converts tabular data into a DOMDocument object.
 */
class XMLConverter
{
    /**
     * XML Root name.
     * @var string
     */
    protected $root_name = 'csv';

    /**
     * XML Node name.
     * @var string
     */
    protected $record_name = 'row';

    /**
     * XML Item name.
     * @var string
     */
    protected $field_name = 'cell';

    /**
     * XML column attribute name.
     * @var string
     */
    protected $column_attr = '';

    /**
     * XML offset attribute name.
     * @var string
     */
    protected $offset_attr = '';

    /**
     * @return self
     */
    public static function create()
    {
        return new self();
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated since version 9.7.0
     * @see XMLConverter::create()
     */
    public function __construct()
    {
    }

    /**
     * Convert a Record collection into a DOMDocument.
     * @param iterable $records
     * @return DOMDocument
     */
    public function convert($records)
    {
        $doc = new DOMDocument('1.0');
        $node = $this->import($records, $doc);
        $doc->appendChild($node);

        return $doc;
    }

    /**
     * Create a new DOMElement related to the given DOMDocument.
     *
     * **DOES NOT** attach to the DOMDocument
     *
     * @param iterable $records
     * @return DOMElement
     */
    public function import($records, DOMDocument $doc)
    {
        $root = $doc->createElement($this->root_name);
        foreach ($records as $offset => $record) {
            $node = $this->recordToElement($doc, $record, $offset);
            $root->appendChild($node);
        }

        return $root;
    }

    /**
     * Convert a CSV record into a DOMElement and adds its offset as DOMElement attribute.
     *
     * @param int $offset
     * @return DOMElement
     */
    protected function recordToElement(DOMDocument $doc, array $record, $offset)
    {
        $node = $doc->createElement($this->record_name);
        foreach ($record as $node_name => $value) {
            $item = $this->fieldToElement($doc, (string) $value, $node_name);
            $node->appendChild($item);
        }

        if ('' !== $this->offset_attr) {
            $node->setAttribute($this->offset_attr, (string) $offset);
        }

        return $node;
    }

    /**
     * Convert Cell to Item.
     *
     * Convert the CSV item into a DOMElement and adds the item offset
     * as attribute to the returned DOMElement
     *
     * @param string $value
     * @param int|string $node_name
     * @return DOMElement
     */
    protected function fieldToElement(DOMDocument $doc, $value, $node_name)
    {
        $item = $doc->createElement($this->field_name);
        $item->appendChild($doc->createTextNode($value));

        if ('' !== $this->column_attr) {
            $item->setAttribute($this->column_attr, (string) $node_name);
        }

        return $item;
    }

    /**
     * XML root element setter.
     * @param string $node_name
     * @return self
     */
    public function rootElement($node_name)
    {
        $clone = clone $this;
        $clone->root_name = $this->filterElementName($node_name);

        return $clone;
    }

    /**
     * Filter XML element name.
     *
     * @param string $value
     * @return string
     * @throws DOMException If the Element name is invalid
     */
    protected function filterElementName($value)
    {
        return (new DOMElement($value))->tagName;
    }

    /**
     * XML Record element setter.
     * @param string $node_name
     * @param string $record_offset_attribute_name
     * @return self
     */
    public function recordElement($node_name, $record_offset_attribute_name = '')
    {
        $clone = clone $this;
        $clone->record_name = $this->filterElementName($node_name);
        $clone->offset_attr = $this->filterAttributeName($record_offset_attribute_name);

        return $clone;
    }

    /**
     * Filter XML attribute name.
     *
     * @param string $value Element name
     * @return string
     *
     * @throws DOMException If the Element attribute name is invalid
     */
    protected function filterAttributeName($value)
    {
        if ('' === $value) {
            return $value;
        }

        return (new DOMAttr($value))->name;
    }

    /**
     * XML Field element setter.
     * @param string $node_name
     * @param string $fieldname_attribute_name
     * @return self
     */
    public function fieldElement($node_name, $fieldname_attribute_name = '')
    {
        $clone = clone $this;
        $clone->field_name = $this->filterElementName($node_name);
        $clone->column_attr = $this->filterAttributeName($fieldname_attribute_name);

        return $clone;
    }
}

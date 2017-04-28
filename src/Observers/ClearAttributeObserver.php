<?php

/**
 * TechDivision\Import\Attribute\Observers\ClearAttributeObserver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-attribute
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Attribute\Observers;

use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Attribute\Utils\ColumnKeys;
use TechDivision\Import\Attribute\Utils\MemberNames;
use TechDivision\Import\Attribute\Services\AttributeBunchProcessorInterface;

/**
 * Observer that removes the EAV attribute with the code found in the CSV file.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-attribute
 * @link      http://www.techdivision.com
 */
class ClearAttributeObserver extends AbstractAttributeImportObserver
{

    /**
     * The attribute processor instance.
     *
     * @var \TechDivision\Import\Attribute\Services\AttributeBunchProcessorInterface
     */
    protected $attributeBunchProcessor;

    /**
     * Initializes the observer with the passed subject instance.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface                           $subject                 The observer's subject instance
     * @param \TechDivision\Import\Attribute\Services\AttributeBunchProcessorInterface $attributeBunchProcessor The attribute bunch processor instance
     */
    public function __construct(
        SubjectInterface $subject,
        AttributeBunchProcessorInterface $attributeBunchProcessor
    ) {

        // pass the subject through to the parend observer
        parent::__construct($subject);

        // initialize the attribute processor
        $this->attributeBunchProcessor = $attributeBunchProcessor;
    }

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // query whether or not, we've found a new EAV attribute code => means we've found a new attribute
        if ($this->hasBeenProcessed($attributeCode = $this->getValue(ColumnKeys::ATTRIBUTE_CODE))) {
            return;
        }

        // try to load the EAV attribute with the code found in the CSV file
        $attribute = $this->loadAttributeByAttributeCode($attributeCode);

        // delete the EAV attribute with the code found in the CSV file
        $this->deleteAttribute(array(MemberNames::ATTRIBUTE_ID => $attribute[MemberNames::ATTRIBUTE_ID]));

        // temporary persist the ID of the deleted attribute
        $this->setLastAttributeId($attribute[MemberNames::ATTRIBUTE_ID]);
    }

    /**
     * Return's the attribute bunch processor instance.
     *
     * @return \TechDivision\Import\Attribute\Services\AttributeBunchProcessorInterface The attribute bunch processor instance
     */
    protected function getAttributeBunchProcessor()
    {
        return $this->attributeBunchProcessor;
    }

    /**
     * Load's and return's the EAV attribute with the passed code.
     *
     * @param string $attributeCode The code of the EAV attribute to load
     *
     * @return array The EAV attribute
     */
    protected function loadAttributeByAttributeCode($attributeCode)
    {
        return $this->getAttributeBunchProcessor()->loadAttributeByAttributeCode($attributeCode);
    }

    /**
     * Delete's the EAV attribute with the passed attributes.
     *
     * @param array       $row  The attributes of the EAV attribute to delete
     * @param string|null $name The name of the prepared statement that has to be executed
     *
     * @return void
     */
    protected function deleteAttribute($row, $name = null)
    {
        $this->getAttributeBunchProcessor()->deleteAttribute($row, $name);
    }
}

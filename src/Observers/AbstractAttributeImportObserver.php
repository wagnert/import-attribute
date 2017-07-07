<?php

/**
 * TechDivision\Import\Attribute\Observers\AbstractAttributeImportObserver
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
use TechDivision\Import\Observers\AbstractObserver;
use TechDivision\Import\Attribute\Utils\ColumnKeys;

/**
 * Abstract attribute observer that handles the process to import attribute bunches.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-attribute
 * @link      http://www.techdivision.com
 */
abstract class AbstractAttributeImportObserver extends AbstractObserver implements AttributeImportObserverInterface
{

    /**
     * Will be invoked by the action on the events the listener has been registered for.
     *
     * @param \TechDivision\Import\Subjects\SubjectInterface $subject The subject instance
     *
     * @return array The modified row
     * @see \TechDivision\Import\Product\Observers\ImportObserverInterface::handle()
     */
    public function handle(SubjectInterface $subject)
    {

        // initialize the row
        $this->setSubject($subject);
        $this->setRow($subject->getRow());

        // process the functionality and return the row
        $this->process();

        // return the processed row
        return $this->getRow();
    }

    /**
     * Return's whether or not this is the admin store view.
     *
     * @return boolean TRUE if we're in admin store view, else FALSE
     */
    protected function isAdminStore()
    {
        return $this->getValue(ColumnKeys::STORE_VIEW_CODE) === null;
    }

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    abstract protected function process();


    /**
     * Prepare data array by given values in source
     *
     * @param array $keys
     * @param bool $useDefaults
     * @return array
     */
    protected function getPreparedAttributeData(array $keys, $useDefaults = true)
    {
        $attribute = [];
        foreach ($keys as $key) {
            if ($this->hasValue($key) || $useDefaults) {
                $attribute[$key] = $this->getValue($key, $this->getDefaultValue($key), $this->getCallback($key));
            }
        }
        return $attribute;
    }

    /**
     * @return array
     */
    protected function getDefaultValues()
    {
        return array();
    }

    /**
     * Retrieve default value for given key
     *
     * @param string $key
     * @return null
     */
    protected function getDefaultValue($key)
    {
        $defaultValues = $this->getDefaultValues();

        if (isset($defaultValues[$key])) {
            return $defaultValues[$key];
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getCallbacks()
    {
        return array();
    }

    /**
     * Retrieve callbacks for given column key
     *
     * @param string $key
     * @return null
     */
    protected function getCallback($key)
    {
        $callbacks = $this->getCallbacks();

        if (isset($callbacks[$key])) {
            return $callbacks[$key];
        }

        return null;
    }
}

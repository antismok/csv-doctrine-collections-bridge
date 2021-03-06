<?php

/**
 * League CSV Doctrine Collection Bridge (https://github.com/bakame-php/csv-doctrine-bridge).
 *
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @license https://github.com/bakame-php/csv-doctrine-bridge/blob/master/LICENSE (MIT License)
 * @version 1.0.0
 * @link    https://github.com/bakame-php/csv-doctrine-bridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bakame\Csv\Extension;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use League\Csv\Reader;
use League\Csv\ResultSet;
use TypeError;
use function get_class;
use function gettype;
use function is_object;
use function sprintf;

final class RecordCollection extends AbstractLazyCollection implements Selectable
{
    /**
     * @var Reader|ResultSet
     */
    private $csv;

    /**
     * @param Reader|ResultSet $csv
     */
    public function __construct($csv)
    {
        $this->csv = $this->filterInput($csv);
    }

    /**
     * Filter the csv object.
     *
     * @throws TypeError if the input is not a League\Csv object
     *
     * @return Reader|ResultSet
     */
    private function filterInput($input)
    {
        if ($input instanceof Reader) {
            return $input;
        }

        if ($input instanceof ResultSet) {
            return $input;
        }

        throw new TypeError(sprintf(
            'expected csv to be a %s or a % object, but received %s instead',
            Reader::class,
            ResultSet::class,
            is_object($input) ? get_class($input) : gettype($input)
        ));
    }

    /**
     * {@inheritDoc}
     */
    protected function doInitialize(): void
    {
        $this->collection = new ArrayCollection();
        foreach ($this->csv as $offset => $record) {
            $this->collection[$offset] = $record;
        }
        unset($this->csv);
    }

    /**
     * {@inheritDoc}
     */
    public function matching(Criteria $criteria)
    {
        $this->initialize();

        return $this->collection->matching($criteria);
    }
}

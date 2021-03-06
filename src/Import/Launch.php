<?php
namespace FMUP\Import;

use FMUP\Import\Iterator\DuplicateIterator;
use FMUP\Import\Iterator\LineToConfigIterator;
use FMUP\Import\Iterator\ValidatorIterator;

/**
 * Lance l'import effectif
 *
 * @author csanz
 *
 */
class Launch extends \FMUP\Import
{
    private $totalInsert;
    private $totalUpdate;
    private $totalErrors;

    /**
     *
     * @return int
     */
    public function getTotalUpdate()
    {
        return $this->totalUpdate;
    }

    /**
     *
     * @return int
     */
    public function getTotalInsert()
    {
        return $this->totalInsert;
    }

    /**
     *
     * @return int
     */
    public function getTotalErrors()
    {
        return $this->totalErrors;
    }

    public function parse()
    {
        $db = $this->getDb();
        $db->beginTransaction();
        try {
            $lci = $this->getLineToConfigIterator($this->fileIterator, $this->config);
            $di = $this->getDoublonIterator($lci);
            $vi = $this->getValidatorIterator($di);
            foreach ($vi as $value) {
                if ($value) {
                    $valid = $vi->getValid();
                    if ($valid && !$value->getDoublonLigne()) {
                        $value->insertLine();
                    }
                }
            }
            $this->totalErrors = $vi->getTotalErrors();
            $this->totalInsert = $vi->getTotalInsert();
            $this->totalUpdate = $vi->getTotalUpdate();
            echo "Import terminé." . PHP_EOL;
            $db->commit();
        } catch (\Exception $e) {
            echo "Une erreur a été détecté lors de l'import." . PHP_EOL;
            echo $e->getMessage();
            $db->rollback();
        }
    }

    /**
     * @return \FMUP\Db
     * @codeCoverageIgnore
     */
    protected function getDb()
    {
        return \Model::getDb();
    }

    /**
     * @param \Iterator $fIterator
     * @param Config $config
     * @return LineToConfigIterator
     * @codeCoverageIgnore
     */
    protected function getLineToConfigIterator(\Iterator $fIterator, \FMUP\Import\Config $config)
    {
        return new LineToConfigIterator($fIterator, $config);
    }

    /**
     * @param \Traversable $iterator
     * @return DuplicateIterator
     * @codeCoverageIgnore
     */
    protected function getDoublonIterator(\Traversable $iterator)
    {
        return new DuplicateIterator($iterator);
    }

    /**
     * @param \Traversable $iterator
     * @return ValidatorIterator
     * @codeCoverageIgnore
     */
    protected function getValidatorIterator(\Traversable $iterator)
    {
        return new ValidatorIterator($iterator);
    }
}

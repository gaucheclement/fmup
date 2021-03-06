<?php
namespace FMUP\Import\Config\Field\Formatter;

use FMUP\Import\Config\Field\Formatter;

class IdFromField implements Formatter
{
    private $originField;
    private $originTable;

    private $hasError = false;
    private $db;

    /**
     * @param string $originField
     * @param string $originTable
     */
    public function __construct($originField, $originTable)
    {
        $this->originField = (string)$originField;
        $this->originTable = (string)$originTable;
    }

    /**
     * @param string $value
     * @return string
     */
    public function format($value)
    {
        $value = (string) $value;
        if ($value == "") {
            $this->hasError = true;
            return "";
        } else {
            $sql = "SELECT id FROM {$this->originTable} WHERE {$this->originField} LIKE '%$value%'";
            $result = $this->getDb()->fetchRow($sql);
            if ($result) {
                return $result['id'];
            } else {
                $this->hasError = true;
                return $value;
            }
        }
    }

    /**
     * @return \FMUP\Db
     * @codeCoverageIgnore
     */
    protected function getModelDb()
    {
        return \Model::getDb();
    }

    /**
     * @return \FMUP\Db
     */
    public function getDb()
    {
        if (!$this->db) {
            $this->db = $this->getModelDb();
        }
        return $this->db;
    }

    /**
     * @param \FMUP\Db $db
     * @return $this
     */
    public function setDb(\FMUP\Db $db)
    {
        $this->db = $db;
        return $this;
    }

    public function getErrorMessage($value = null)
    {
        return 'Aucune correspondance n\'a été trouvé pour le champ : '
            . "'{$this->originField}' de la table '{$this->originTable}' pour la valeur : '$value'";
    }

    public function hasError()
    {
        return $this->hasError;
    }
}

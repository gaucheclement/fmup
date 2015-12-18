<?php
namespace FMUP\Import\Config\Field\Validator;

use FMUP\Import\Config\Field\Validator;

class Telephone implements Validator
{
    private $empty;
    
    public function __construct($empty = false)
    {
        $this->setCanEmpty($empty);
    }
    
    public function setCanEmpty($empty)
    {
        $this->empty = $empty;
        return $this;
    }

    public function getCanEmpty()
    {
        return $this->empty;
    }
    
    public function validate($value)
    {
        $valid = false;
        if (($this->getCanEmpty() && $value == '') || \Is::telephone($value) || \Is::telephonePortable($value)) {
            $valid = true;
        }
        return $valid;
    }

    public function getErrorMessage()
    {
        return "Le champ reçu n'est pas un téléphone valide";
    }
}

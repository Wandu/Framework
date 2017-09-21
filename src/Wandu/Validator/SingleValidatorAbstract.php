<?php
namespace Wandu\Validator;

abstract class SingleValidatorAbstract extends Validator
{
    public function __construct(TesterLoader $loader, ValidatorNormalizer $normalizer = null)
    {
        parent::__construct($loader, $normalizer ?: new ValidatorNormalizer(), $this->rule());
    }
    
    abstract public function rule();
}

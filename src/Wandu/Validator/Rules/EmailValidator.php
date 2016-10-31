<?php
namespace Wandu\Validator\Rules;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Validation\EmailValidation;
use Egulias\EmailValidator\Validation\RFCValidation;

/**
 * Class EmailValidator
 * @Annotation
 */
class EmailValidator extends ValidatorAbstract
{
    const ERROR_TYPE = 'email';

    /** @var \Egulias\EmailValidator\Validation\EmailValidation */
    protected $validation;
    
    public function __construct(EmailValidation $validation = null)
    {
        if (!$validation) {
            $validation = new RFCValidation();
        }
        $this->validation = $validation;
    }

    /**
     * {@inheritdoc}
     */
    public function test($item)
    {
        return is_string($item) && $this->validation->isValid($item, new EmailLexer());
    }
}

<?php
namespace Wandu\Validator\Testers;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Validation\EmailValidation;
use Egulias\EmailValidator\Validation\RFCValidation;
use Wandu\Validator\Contracts\TesterInterface;

class EmailTester implements TesterInterface
{
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
    public function test($data): bool
    {
        return is_string($data) && $this->validation->isValid($data, new EmailLexer());
    }
}

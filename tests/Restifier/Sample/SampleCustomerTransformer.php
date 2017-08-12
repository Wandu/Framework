<?php
namespace Wandu\Restifier\Sample;

use Wandu\Restifier\Contracts\Restifiable;

class SampleCustomerTransformer
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(SampleCustomer $customer, Restifiable $restifier)
    {
        return [
            'address' => $customer->address,
        ];
    }
    
    public function paymentmethods(SampleCustomer $customer)
    {
        return [
            'paymentmethods' => $customer->paymentmethods,
        ];
    }
}

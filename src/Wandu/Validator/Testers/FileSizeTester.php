<?php

namespace Wandu\Validator\Testers;

class FileSizeTester extends PropertyTesterAbstract
{
    
    /**
	 * [test :: check the file size]
	 * @param  [type] $data   [description]
	 * @param  [type] $origin [description]
	 * @param  [type] $keys   [description]
	 * @return bool           [a]
	 */
    public function test(array $data = [], $origin = null, array $keys = []): bool
    {
        if ( !$data['max'] || $data['mix'] ) {
            return false;
        }
        
        $checkIsMax = ( $data['max'] );
        return (  $checkIsMax ? ( $data['max'] > $data['value']  ) :  ( $data['min'] < $data['value'] ) );
    }
}

/* End of file FileSizeTester.php */
/* Location: /src/Wandu/Validator/Testers/FileSizeTester.php */

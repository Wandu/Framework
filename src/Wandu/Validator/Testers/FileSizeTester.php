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
        $checkIsMax = ( $data['max'] );
        return (  $checkIsMax ? $checkMax > $data['value'] :  $checkIsMax < $data['value'] );
    }
}

/* End of file FileSizeTester.php */
/* Location: application/controllers/FileSizeTester.php */

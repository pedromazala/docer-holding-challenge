<?php


namespace Language\Validator;

use Exception;

class ApiResponseValidator implements Validator
{
    public function validate($result): void
    {
        // Error during the api call.
        if ($result === false || !isset($result['status'])) {
            throw new Exception('Error during the api call');
        }
        // Wrong response.
        if ($result['status'] != 'OK') {
            throw new Exception('Wrong response: '
                . (!empty($result['error_type']) ? 'Type(' . $result['error_type'] . ') ' : '')
                . (!empty($result['error_code']) ? 'Code(' . $result['error_code'] . ') ' : '')
                . ((string)$result['data']));
        }
        // Wrong content.
        if ($result['data'] === false) {
            throw new Exception('Wrong content!');
        }
    }
}

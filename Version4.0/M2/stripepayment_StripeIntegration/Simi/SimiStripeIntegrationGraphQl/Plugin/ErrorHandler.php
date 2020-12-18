<?php

namespace Simi\SimiStripeIntegrationGraphQl\Plugin;

class ErrorHandler
{

    public function aroundHandle($subject, $proceed, array $errors, callable $formatter)
    {
        $result = $proceed($errors, $formatter);
        foreach ($errors as $error) {
            $previousError = $error->getPrevious();
            if ($previousError instanceof AggregateExceptionInterface && !empty($previousError->getErrors())) {
                
            } else {
            	if (
            		(get_class($error) === 'GraphQL\Error\Error') && 
            		(strpos($error->getMessage(), 'Authentication Required') !== false)
            	) {
            		if ($result[0])
            			$result[0]['debugMessage'] = $error->getMessage();
            	}
            }
        }
        return $result;
    }

}
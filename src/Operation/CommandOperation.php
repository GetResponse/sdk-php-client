<?php

namespace Getresponse\Sdk\Client\Operation;

use Getresponse\Sdk\Client\Exception\InvalidCommandDataException;

/**
 * Class CommandOperation
 * @package Getresponse\Sdk\Client\Operation
 */
abstract class CommandOperation implements Operation
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $payload
     * @return string
     * @throws InvalidCommandDataException
     */
    protected function encode(array $payload)
    {
        $jsonData = json_encode($payload);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw InvalidCommandDataException::createFromJsonLastErrorMsg($payload);
        }
        return $jsonData;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     * @throws InvalidCommandDataException
     */
    public function set($key, $value)
    {
        if (!in_array($key, $this->getAllowedFields(), true)) {
            throw InvalidCommandDataException::createFromInvalidField($key, get_called_class());
        }
        $this->data[$key] = $value;

        return $this;
    }
    
    
    /**
     * @param UrlQuery $urlQuery
     * @param array $extra
     * @return string
     */
    protected function buildUrlQuery(UrlQuery $urlQuery = null, array $extra = [])
    {
        $params = [];
        if (null !== $urlQuery) {
            $params = array_merge($params, $urlQuery->toArray());
        }
        if (!empty($extra)) {
            $params = array_merge($params, $extra);
        }

        if (!empty($params)) {
            return '?' . http_build_query($params);
        }

        return '';
    }
    
    /**
     * @param ValueList | null $additionalFlags
     * @return array
     */
    protected function getAdditionalFlagsParameterArray(ValueList $additionalFlags = null)
    {
        if ($additionalFlags !== null) {
            return [
                'additionalFlags' => $additionalFlags->toString()
            ];
        }
        return [];
    }
}

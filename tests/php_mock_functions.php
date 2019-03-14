<?php

namespace {
    
    use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockFactory;

    FunctionMockFactory::create('Getresponse\Sdk\Client\Debugger', 'extension_loaded');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_init');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_multi_init');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_multi_exec');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_multi_add_handle');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_multi_getcontent');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_multi_remove_handle');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_multi_close');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_setopt');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_errno');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_error');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_close');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_exec');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Handler', 'curl_getinfo');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Exception', 'curl_getinfo');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Exception', 'curl_error');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Exception', 'json_last_error_msg');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Exception', 'json_last_error');
    FunctionMockFactory::create('Getresponse\Sdk\Client\Operation', 'json_last_error');
}
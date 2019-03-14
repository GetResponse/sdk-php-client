$callback = function ({signatureParameters})
{
    $arguments = [{bodyParameters}];

    $variadics = \array_slice(\func_get_args(), \count($arguments));
    $arguments = \array_merge($arguments, $variadics);

    return call_user_func_array('\\{name}', $arguments);
};
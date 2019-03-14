namespace {fqfn};

use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMock as FunctionMockInterface;

class FunctionMock implements FunctionMockInterface {
    private $name;
    private $callback;
    private $originCallback;

    public function __construct($namespace, $name, callable $callback)
    {
        $this->namespace = $namespace;
        $this->name = $name;
        $this->callback = $this->originCallback = $callback;
    }
    public function getNameWithNamespace()
    {
        return $this->namespace . '\\' . $this->name;
    }
    public function overwriteCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }
    public function reset()
    {
        $this->callback = $this->originCallback;
        return $this;
    }
    public function invoke({invokeSignatureParameters})
    {
        $arguments = [{bodyParameters}];
        $variadics = \array_slice(\func_get_args(), \count($arguments));
        $arguments = \array_merge($arguments, $variadics);
        return call_user_func_array($this->callback, $arguments);
    }
    public function getCallable()
    {
        return [$this, 'invoke'];
    }
}
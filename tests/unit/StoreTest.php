<?php

use Tarsana\Command\Utils\Store;


class StoreTest extends \Codeception\Test\Unit
{
    protected $tester;

    protected $store;

    protected function _before()
    {
        // the class Foo is declared in _boostrap.php
        $this->store = new Store('Foo');
    }

    protected function _after()
    {
        unset($this->store);
    }

    public function test_elements_getter() {
        $this->assertEmpty($this->store->elements());

        $this->store->store('first', new Foo);
        $this->store->store('second', new Foo);

        $this->assertEquals(2, count($this->store->elements()));
        $this->assertEquals(['first', 'second'], array_keys($this->store->elements()));
    }

    public function test_type_getter () {
        $this->assertEquals('Foo', $this->store->type());
    }

    public function test_store_by_instance () {
        $this->assertEmpty($this->store->elements());
        $this->store->store('name', new Foo);
        $this->assertEquals(1, count($this->store->elements()));
    }

    public function test_store_by_class () {
        $this->assertEmpty($this->store->elements());
        $this->store->store('parent', 'Foo');
        $this->store->store('child', 'ChildFoo');
        $this->assertEquals(2, count($this->store->elements()));
    }

    public function test_contains () {
        $this->assertFalse($this->store->contains('name'));
        $this->store->store('name', new Foo);
        $this->assertTrue($this->store->contains('name'));
    }

    public function test_remove () {
        $this->store->store('name', new Foo);
        $this->assertEquals(1, count($this->store->elements()));
        $this->store->remove('name');
        $this->assertEmpty($this->store->elements());
        // even if the element does not exist, remove() doesn't throw any exception.
        // It simply do nothing in this case
        $this->store->remove('name');
    }

    public function test_get () {
        $foo = new Foo;
        $foo->value = 'my foo value';
        $this->store->store('foo', $foo);

        $foo = $this->store->get('foo');
        $this->assertTrue($foo instanceof Foo);
        $this->assertEquals('my foo value', $foo->value);
    }

    /**
     * @expectedException Tarsana\Command\Exceptions\StoreException
     * @expectedExceptionMessage The class given to the store constructor is invalid
     */
    public function test_error_when_wrong_type () {
        $store = new Store('SomeInvalidClassName');
    }

    /**
     * @expectedException Tarsana\Command\Exceptions\StoreException
     * @expectedExceptionMessage The element with name 'foo' is missing
     */
    public function test_error_when_getting_missing_element () {
        $this->store->get('foo');
    }

    /**
     * @expectedException Tarsana\Command\Exceptions\StoreException
     * @expectedExceptionMessage The name must be a string
     */
    public function test_error_when_name_is_not_string () {
        $this->store->store(['foo'], new Foo);
    }

    /**
     * @expectedException Tarsana\Command\Exceptions\StoreException
     * @expectedExceptionMessage The name 'foo' already exists in the store
     */
    public function test_error_when_duplicated_name () {
        $this->store->store('foo', new Foo);
        $this->store->store('bar', new Foo);
        $this->store->store('foo', new Foo);
    }

    /**
     * @expectedException Tarsana\Command\Exceptions\StoreException
     * @expectedExceptionMessage Trying to store an instance of 'Bar' in a store of 'Foo'
     */
    public function test_error_when_value_instance_of_wrong_class () {
        $this->store->store('bar', new Bar);
    }

    /**
     * @expectedException Tarsana\Command\Exceptions\StoreException
     * @expectedExceptionMessage Trying to store an instance of 'Bar' in a store of 'Foo'
     */
    public function test_error_when_value_is_wrong_class () {
        $this->store->store('bar', 'Bar');
    }

    /**
     * @expectedException Tarsana\Command\Exceptions\StoreException
     * @expectedExceptionMessage Trying to store a value which is not a class or object
     */
    public function test_error_when_value_is_not_string_or_object () {
        $this->store->store('foo', 32);
    }

    /**
     * @expectedException Tarsana\Command\Exceptions\StoreException
     * @expectedExceptionMessage Trying to store an unknow class 'weird'
     */
    public function test_error_when_value_is_string_but_not_class () {
        $this->store->store('foo', 'weird');
    }

}

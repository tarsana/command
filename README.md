# Tarsana Command

[![Build Status](https://travis-ci.org/tarsana/command.svg?branch=master)](https://travis-ci.org/tarsana/command)
[![Coverage Status](https://coveralls.io/repos/github/tarsana/command/badge.svg?branch=master)](https://coveralls.io/github/tarsana/command?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cbaeb46f-468d-4d02-a02a-574fad0f95d3/mini.png)](https://insight.sensiolabs.com/projects/cbaeb46f-468d-4d02-a02a-574fad0f95d3)
[![Gratipay](https://img.shields.io/gratipay/project/Tarsana.svg)](https://gratipay.com/Tarsana)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](https://github.com/tarsana/command/blob/master/LICENSE)

A library to build command line applications using PHP. This is part of the [Tarsana Project](https://github.com/tarsana/specs).

# Table of Contents

- [Installation](#installation)

- [Your First Command](#your-first-command)

- [Initializing The Command](#initializing-the-command)

- [Showing The Help And Version Of A Command](#showing-the-help-and-version-of-a-command)

- [Reading & Writing to The Console](#reading--writing-to-the-console)

- [Defining Arguments and Options](#defining-arguments-and-options)

- [Reading Arguments and Options Interactively](#reading-arguments-and-options-interactively) **New on version 1.1.0**
- [Handeling The Filesystem](#handeling-the-filesystem)

- [Rendering Templates](#rendering-templates)

- [Adding SubCommands](#adding-sub-commands)

- [Testing Commands](#testing-commands)

- [What's Next](#whats-next)

- [Development Notes](#development-notes)

# Installation

Install it using Composer

```
composer require tarsana/command
```

# Your First Command

Let's write a "Hello World" command. Create a file `hello.php` with the following content:

```php
<?php
require __DIR__.'/vendor/autoload.php';

use Tarsana\Command\Command;

class HelloWorld extends Command {

    protected function execute()
    {
        $this->console->line('Hello World');
    }

}

(new HelloWorld)->run();

```

Then run it from the terminal:

```
$ php hello.php
Hello World
```

Congratulations, you have just written your first command :D

As you see, `Tarsana\Command\Command` is a class providing the basic features of a command. Every command should extend it and implement the `execute()` method.

# Initializing The Command

In addition, `Command` gives the `init()` method which is used the initialize the command general attributes. Let's rewrite our `HelloWorld` command:

```php
class HelloWorld extends Command {

	protected function init ()
	{
		$this->name('Hello World')
		     ->version('1.0.0-alpha')
		     ->description('Shows a "Hello World" message');
	}

    protected function execute()
    {
        $this->console->line('Hello World');
    }

}
``` 

Here we are overriding the `init()` method to define the command **name**, **version** and **description**.

Note that the setter of an an attribute `foo` is named `foo()` instead of `setFoo()`. I know that this is not a common convention but it makes sense for me. :P

```php
$this->name('blabla'); // will set the name to 'blabla' and return $this
$this->name(); // calling it without parameter will get the value of name
```

# Showing the Help and Version of a Command	

To show the version of a command, we use the `--version` flag (we will learn after that this is actually a sub command). We also have the `--help` to show the help message:

![Show version and help message](https://raw.githubusercontent.com/tarsana/command/master/docs/screenshots/hello-version-help.png)

# Reading & Writing to the Console

The attribute `console` is used to handle the reading and writing operations to the console.

Let's update our command to read the user name:

```php
protected function execute()
{
    $this->console->out('Your name: ');
    $name = $this->console->readLine();
    $this->console->line("Hello {$name}");
}
```

```
$ php hello.php
Your name: Amine
Hello Amine
```

- The `readLine()` method reads a line from the stdin and returns it as string.
- The `out()` method writes some text to `stdout` (without a line break).
- The `line()` method writes some text to `stdout` and adds a line break.
- The `error()` method writes some text to `stderr` and adds a line break.

The `Console` class provides some `tags` to control the output:

```php
$this->console->line('<background:15><color:19>Blue text on white background<reset>');
$this->console->line('<background:124><color:15>White text on red background<reset>');
```

![Show colors in the console](https://raw.githubusercontent.com/tarsana/command/master/docs/screenshots/background-color.png)

The `<background:$number>` and `<color:$number>` tags allows to set the background and foreground colors of the text to be written; the `<reset>` tag resets the default values. The colors are given as numbers from the 256-color mode.

## List of supported tags

- `<color:$n>`: Sets the foreground text to the color `$n` in 256-color mode.
- `<background:$n>`: Sets the foreground text to the color `$n` in 256-color mode.
- `<reset>`: Resets the formatting default values.
- `<bold>`: Makes the text bold.
- `<underline>`: Underlines the text.

`Console` allows you also to define styles using aliases:

```php
$this->console->alias('<danger>', '<background:124><color:15><bold>');
$this->console->alias('</danger>', '<reset>');

$this->console->line('<danger>Some text</danger>');
// is equivalent to
$this->console->line('<background:124><color:15><bold>Some text<reset>');
```

Predefined aliases are:

```php
$this->console->line('<info> information text </info>');
$this->console->line('<warn> warning text </warn>');
$this->console->line('<success> success text </success>');
$this->console->line('<error> error text </error>');
$this->console->line('<tab>'); // prints four spaces "    " 
$this->console->line('<br>'); // prints line break  PHP_EOL
```

![Console output aliases](https://raw.githubusercontent.com/tarsana/command/master/docs/screenshots/aliases.png)

**Note:** tags and aliases can be used in all strings printed to the console, including the command and arguments descriptions.

# Defining Arguments and Options

The command syntax is defined using the [Syntax](https://github.com/tarsana/syntax) library. Let's start with a command that repeats a word a number of times:

```php
class RepeatCommand extends Command {

    protected function init ()
    {
        $this->name('Repeat')
             ->version('1.0.0')
             ->description('Repeats a word a number of times')
             ->syntax('word: string, count: (number: 3)')
             ->options(['--upper'])
             ->describe('word', 'The word to repeat')
             ->describe('count', 'The number of times to repeat the word')
             ->describe('--upper', 'Converts the result to uppercase');
    }

    protected function execute()
    {
        $result = str_repeat($this->args->word, $this->args->count);
        if ($this->option('--upper'))
            $result = strtoupper($result);
        $this->console->line($result);
    }

}
```

We are using the method `syntax()` to define the syntax of arguments. The string given to this method follows the [rules described here](https://github.com/tarsana/syntax#rules)

The `describe()` method is used to describe an argument.

When you define the syntax of the command; arguments are parsed automatically and available in the `execute()` method via the `args` attribute.

The `help` subcommand shows full description of the arguments and options:

![Help message example](https://raw.githubusercontent.com/tarsana/command/master/docs/screenshots/repeat-help-message.png)

And the result is:

```
$ php repeat.php foo 5
foofoofoofoofoo
$ php repeat.php bar --upper
BARBARBAR
```

In the second example, the `count` argument takes automatically its default value.

**Warning: Giving wrong arguments generates an error**

![Parse error example](https://raw.githubusercontent.com/tarsana/command/master/docs/screenshots/repeat-args-missing.png)

# Reading Arguments and Options Interactively

Some commands can have long and complicated list of arguments. Defining the syntax of such command is easy thanks to [Syntax](https://github.com/tarsana/syntax) but typing the arguments in the command line becomes challenging.

Let's take the following command for example:

```php
class ClassGenerator extends Command {
    protected function init()
    {
        $this->name('Class Generator')
        ->version('1.0.0')
        ->description('Generates basic code for a class.')
        ->syntax('
            language: string,
            name: string,
            parents: ([string]:[]),
            interfaces: ([string]:[]),
            attrs: [{
                name,
                type,
                hasGetter: (boolean:true),
                hasSetter: (boolean:true),
                isStatic: (boolean:false)
            }],
            methods: ([{
                name: string,
                type: string,
                args: [{ name, type, default: (string:null) |.}],
                isStatic: (boolean:false)
            }]:[])
        ')
        ->descriptions([
            'language'          => 'The programming language in which the code will be generated.',
            'name'              => 'The name of the class.',
            'parents'           => 'List of parent classes names.',
            'interfaces'        => 'List of implemented interfaces.',
            'attrs'             => 'List of attributes of the class.',
            'attrs.name'        => 'The name of the attribute.',
            'attrs.type'        => 'The type of the attribute.',
            'attrs.hasGetter'   => 'Generate a getter for the attribute.',
            'attrs.hasSetter'   => 'Generate a setter for the attribute.',
            'attrs.isStatic'    => 'The attribute is static.',
            'methods'           => 'List of methods of the class.',
            'methods.name'      => 'The method name.',
            'methods.type'      => 'The method return type.',
            'methods.args'      => 'List of arguments of the method.',
            'methods.isStatic'  => 'This method is static.'
        ]);
    }

    protected function execute()
    {
        $this->console->line("Generate code for the class {$this->args->name} in {$this->args->language}...");

    }
}
```

if you run the command using the `-i` flag, it will let you enter the arguments interactively:

![Interactive Arguments Reader](https://raw.githubusercontent.com/tarsana/command/master/docs/screenshots/interactive-args.gif)

After reading all args, the command will show the command line version of the entered args: 

```
>  PHP User  Serializable name:string:true:true:false
```

which means that running

```
$ php class.php  PHP User  Serializable name:string:true:true:false 
```

would produce the same result.

# Handling The Filesystem

The `fs` attribute is an instance of `Tarsana\IO\Filesystem` that you can use to handle files and directories. [Read the documentation](https://github.com/tarsana/io#handeling-files-and-directories) for the full API. 

By default, the `Filesystem` instance points to the directory from which the command is run. You can also initialize it to any directory you want:

```php
using Tarsana\IO\Filesystem;
// ...
protected function init()
{
	$this->fs(new Filesystem('path/to/directory/you/want'));
}
```

# Rendering Templates

The `Command` class gives also possibility to render templates. The default template engine is [Twig](https://twig.symfony.com) but you can use your favorite one by implementing the interfaces `TemplateLoaderInterface` and `TemplateInterface`.

Let's make a command which renders a simple template. For this we will create two files:

```
render-hello.php
templates/
    hello.twig
```

**hello.twig**

```
Hello {{name}}
```

This is a simple template that print a hello message.

**render-hello.php**

```php
<?php
require __DIR__.'vendor/autoload.php';

use Tarsana\Command\Command;
use Tarsana\Command\Templates\TwigTemplateLoader;


class RenderHelloCommand extends Command {

    protected function init ()
    {
        $this
            ->name('Renders Simple Template')
            ->description('Renders a simple twig template')
            ->syntax('name: (string:You)')
            ->describe('name', 'Your name')
            ->templatesPath(__DIR__.'/templates'); // defines the path to the templates
    }

    protected function execute()
    {
        $message = $this->template('hello')
            ->render([
                'name' => $this->args->name
            ]);

        $this->console->line($message);
    }

}

(new RenderHelloCommand)->run();
```

**Result**

```
$ php render-hello.php Foo
Hello Foo

$ php render-hello.php
Hello You
```

# Adding SubCommands

You can add subcommands while initializing your command. 

```php
// ...
protected function init()
{
    //...
    // Assuming that FooCommand and BarCommand are already defined
    $this->command('foo', new FooCommand)
         ->command('bar', new BarCommand); // this erases the subcommand with key 'bar' if exists
    // Or set all subcommands at once (this will erase any previous subcommands)
    $this->commands([
        'foo' => new FooCommand,
        'bar' => new BarCommand
    ]);

    // Later on you can get subcommands
    $this->commands(); // returns all the subcommands as key-value array
    $this->command('name'); // gets the subcommand with the given name 
    // will throw an exception if the subcommand is missing
    $this->hasCommand('name'); // checks if a subcommand with the given name exists
}
```

Now when you run

```
$ php your-script.php foo other arguments here
```

The `FooCommand` will be run with `other arguments here` as arguments.

**Note:** subcommands will always have the attributes `console`, `fs` and `templatesLoader` pointing to the same objects as their parent, as long as you don't change them explicitly in the subcommand's code.

# Testing Commands

The class `Tarsana\Tester\CommandTestCase` extends `PHPUnit\Framework\TestCase` and adds useful methods to test Tarsana Commands.

## Testing the Input and Output

Let's write a test for our `HelloWorld` command above which reads the user name than shows the hello message.

```php
use Tarsana\Tester\CommandTestCase;

class HelloWorldTest extends CommandTestCase {

    public function test_it_prints_hello()
    {
        $this->withStdin("Amine\n")
             ->command(new HelloWorld)
             ->prints("Your name:")
             ->prints("Hello Amine<br>");
    }

    public function test_it_shows_hello_world_version()
    {
        $this->command(new HelloWorld, ['--version'])
             ->printsExactly("<info>Hello World</info> version <info>1.0.0-alpha</info><br>");
    }

}
```

```php
withStdin(string $content) : CommandTestCase;
```

Sets the content of the standard input of the command.

```php
command(Command $c, array $args = []) : CommandTestCase;
```

Runs the command `$c` with the standard input and `$args` then stores its outputs for further assertions.

```php
printsExactly(string $text) : CommandTestCase;
prints(string $text) : CommandTestCase;
printsError(string $text) : CommandTestCase;
```

- `printsExactly` asserts that the standard output of the command equals `$text`. Note that [tags](#list-of-supported-tags) are not applied to allow testing them easily.

- `prints` asserts that the standard output of the command contains `$text`.

- `printsError` asserts that error output of the command contains `$text`.

## Testing the Arguments and Options

Let's now test the `RepeatCommand` above.

```php
class RepeatCommandTest extends CommandTestCase {

    public function test_it_repeats_word_three_times()
    {
        $this->command(new RepeatCommand, ['foo'])
             ->argsEqual((object) [
                'word' => 'foo',
                'count' => 3
             ])
             ->optionsEqual([
                '--upper' => false
             ])
             ->printsExactly("foofoofoo<br>");
    }

    public function test_it_repeats_word_n_times_uppercase()
    {
        $this->command(new RepeatCommand, ['bar', '5', '--upper'])
             ->argsEqual((object) [
               'word' => 'bar',
               'count' => 5
             ])
             ->optionsEqual([
               '--upper' => true
             ])
             ->printsExactly("BARBARBARBARBAR<br>");
    }
}
```

```php
argsEqual(object $args) : CommandTestCase;
optionsEqual(array $options) : CommandTestCase;
```

Assert that the parsed arguments and options of the command are equal to the given values.

## Testing the Filesystem

Let's take the following command:

```php
class ListCommand extends Command {

    protected function init ()
    {
        $this->name('List')
             ->version('1.0.0-alpha')
             ->description('Lists files and directories in the current directory.');
    }

    protected function execute()
    {
        foreach($this->fs->find('*')->asArray() as $file) {
            $this->console->line($file->name());
        }
    }

}
```

The test can be written as follows:

```php
class ListCommandTest extends CommandTestCase {

    public function test_it_lists_files_and_directories()
    {
        $this->havingFile('demo.txt', 'Some text here!')
             ->havingFile('doc.pdf')
             ->havingDir('src')
             ->command(new ListCommand)
             ->printsExactly('demo.txt<br>doc.pdf<br>src<br>');
    }

    public function test_it_prints_nothing_when_no_files()
    {
        $this->command(new ListCommand)
             ->printsExactly('');
    }
}
```

```php
havingFile(string $path, string $content = '') : CommandTestCase;
havingDir(string $path) : CommandTestCase;
```

The `CommandTestCase` run the command with a virtual filesystem. The methods `havingFile` and `havingDir` can be used to create files and directories on that filesystem before running the command.

# What's Next

Please take a look at the examples in the `examples` directory, and try using the library to build some awesome commands. Any feedback is welcome!

# Development Notes

- **Version 1.1.1** Fixed a bug with subcommands not having the default `--help`, `--version` and `-i` subcommands.

- **Version 1.1.0** The flag `-i` added to commands to enable interactive reading of arguments and options.

- **Version 1.0.1** Fixed a bug of subcommands having different instances of `fs` and `templatesLoader` from their parent.

- **Version 1.0.0** The first version is finally out; have fun!

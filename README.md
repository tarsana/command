# Tarsana Command

[![Build Status](https://travis-ci.org/tarsana/command.svg?branch=master)](https://travis-ci.org/tarsana/command)
[![Coverage Status](https://coveralls.io/repos/github/tarsana/command/badge.svg?branch=master)](https://coveralls.io/github/tarsana/command?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tarsana/command/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tarsana/command/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/467aafde-761d-4f8d-afe4-a5eec105f27d/mini.png)](https://insight.sensiolabs.com/projects/467aafde-761d-4f8d-afe4-a5eec105f27d)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](https://github.com/tarsana/command/blob/master/LICENSE)

A library to build command line applications using PHP.

# Table of Contents

- [Installation](#installation)

- [Your First Command](#your-first-command)

- [Initializing The Command](#initializing-the-command)

- [Showing The Help And Version Of A Command](#showing-the-help-and-version-of-a-command)

- [Reading & Writing to The Console](#reading-writing-to-the-console)

- [Defining Arguments](#defining-arguments)

- [Handeling The Filesystem](#handeling-the-filesystem)

- [Rendering Templates](#rendering-templates)

- [Adding SubCommands](#adding-sub-commands)

- [Calling Other Commands](#calling-other-commands)

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

Note that the setter of an an attribute `foo` is named `foo()` instead of `setFoo()`. I know that this is not a common convention but it makes sense for me :P

```php
$this->name('blabla'); // will set the name to 'blabla' and return $this
$this->name(); // calling it without parameter will get the value of name
```

# Showing The Help And Version Of A Command	

To show the version of a command, we use the `--version` flag (we will learn after that this is actually a sub command). We also have the `--help` to show the help message:

![Show version and help message](https://raw.githubusercontent.com/tarsana/command/master/docs/screenshots/hello-version-help.png)

# Reading & Writing to The Console

The attribute `console` is used to handle the reading and writing operations to the console. It is using the awesome library [CLImate](http://climate.thephpleague.com/) that you should check to learn more about how to read and write to the console.

# Defining Arguments

Even if `CLImate` provides a nice way to [Defines and parse arguments](http://climate.thephpleague.com/arguments/); I prefered to use [Syntax](https://github.com/tarsana/syntax) which is more easy and powerful. 

Let's start with a command that repeats a word a number of times:

```php
class RepeatCommand extends Command {

    protected function init ()
    {
        $this->name('Repeat')
             ->version('1.0.0')
             ->description('Repeats a word a number of times')
             ->syntax('word #count')
             ->describe('word', 'The word to repeat')
             ->describe('count', 'The number of times to repeat the word', 3);
    }

    protected function execute()
    {
        $result = str_repeat($this->args->word, $this->args->count);
        $this->console->out($result);
    }

}
```

We are using the method `syntax()` to define the syntax of arguments. The syntax string defines the syntax of each argument seperated by space. To learn more about this syntax [Click here](https://github.com/tarsana/syntax). The `syntax()` method can also take an [ObjectSyntax](https://github.com/tarsana/syntax#parsing-and-dumping-objects) instead of a `string`.

The `describe()` method is used to describe an argument by providing a description and a default value to make it optional.

When you define the syntax of the command; arguments are parsed automatically and available in the `execute()` method via the `args` attribute.

The `help` subcommand shows full description of the arguments:

```
Repeat 1.0.0

Repeats a word a number of times

Arguments: word count
  word [string] The word to repeat (Required)
  count [number] The number of times to repeat the word (default: 3 )

Subcommands:
	--help                Shows help message
	--version             Shows the version of the command
```

And the result is:

```
$ php repeat.php foo 5
foofoofoofoofoo
$ php repeat.php bar
barbarbar
```

In the second example, the `count` argument takes autmatically its default value.

**Warning: Giving wrong arguments syntax generates an error**

```
$ php repeat.php
Unable to parse '' as 'string'
Unable to parse the required field 'word' !
Missing required field 'word'
Invalid arguments: '' for command 'Repeat'
```

```
$ php repeat.php foo 11 bar
Too much items; 2 fields but got 3 items !
Invalid arguments: 'foo 11 bar' for command 'Repeat'
```

# Handeling The Filesystem

The `fs` attribute is an instance of `Tarsana\IO\Filesystem` that you can use to handle files and directories. [Read the documentation](https://github.com/tarsana/io#handeling-files-and-directories) for the full API. 

By default, the `Filesystem` instance pointes to the directory from which the command is run. You can also initialize it to with any directory you want:

```php
using Tarsana\IO\Filesystem;
// ...
protected function init()
{
	$this->fs(new Filesystem('path/to/directory/you/want'));
}
```

# Rendering Templates

The `Command` class gives also possibility to render templates. The dafault template engine is [Twig](http://twig.sensiolabs.org) but you can use your favorite one by implementing the interfaces `TemplateLoaderInterface` and `TemplateInterface`.

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
            ->syntax('[name]')
            ->describe('name', 'Your name', 'You')
            ->templatePaths(__DIR__.'/templates'); // defines the path to the templates
    }

    protected function execute()
    {
        $message = $this->template('hello')
            ->render([
                'name' => $this->args->name
            ]);

        $this->console->out($message);
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
    // Assuming that FooCommand and BarCommand are two already defined
    $this->command('foo', new FooCommand)
         ->command('bar', new BarCommand); // this erases the subcommand with key 'bar' if exists
    // Or set all subcommands at once (this will erase any previous subcommands)
    $this->subCommands([
        'foo' => new FooCommand,
        'bar' => new BarCommand
    ]);

    // Later on you can get subcommands
    $this->subCommands(); // returns all the subcommands as key-value array
    $this->command('name'); // gets the subcommand with the given name 
    // will throw a Tarsana\Command\Exceptions\CommandNotFound if the subcommand is missing
    $this->hasCommand('name'); // checks if a subcommand with the given name exists
}
```

Now when you run

```
$ php your-script.php foo other arguments here
```

The `FooCommand` will be run with `other arguments here` as arguments.

# Calling Other Commands

What if you need to call command `Foo` from command `Bar`, but you don't want to add `Foo` as subcommand of `Bar`. You can still do it by adding the `Foo` command to the `Environment` before runing the `Bar` command:

```php
using Tarsana\Command\Command;
using Tarsana\Command\Environment;

class BarCommand extends Command {
    // ...
    protected function execute()
    {
        $this->call('foo', 'command line arguments here');
        // calls the 'foo' command from the environment with the given arguments
        // if the second argument is not given, the FooCommand will try 
        // to read arguments from the command line
    }
}

// To add the Foo command to the environment
// You can add it by class name so that an instance is created only when needed
Environment::get()->command('foo', 'FooCommand');
// Or add an instance directly
Environment::get()->command('foo', new FooCommand);
```


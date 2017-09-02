<?php namespace Tarsana\Command\Tests\Acceptance;

use Tarsana\Command\Command as C;
use Tarsana\Tester\CommandTestCase;


class ShowsHelpTest extends CommandTestCase {

    public function test_it_shows_help_message() {
        $c = C::create()
            ->name('Class Generator')
            ->version('1.0.1')
            ->description('Generates basic code for a class.')
            ->syntax('
                name,
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
            ->describe('name', 'The name of the class.')
            ->describe('parents', 'List of parent classes names.')
            ->describe('interfaces', 'List of implemented interfaces.')
            ->describe('attrs', 'List of attributes of the class.')
            ->describe('attrs.name', 'The name of the attribute.')
            ->describe('attrs.type', 'The type of the attribute.')
            ->describe('attrs.hasGetter', 'Generates a getter for the attribute.')
            ->describe('attrs.hasSetter', 'Generates a setter for the attribute.')
            ->describe('attrs.isStatic', 'The attribute is static.')
            ->describe('methods', 'List of methods of the class.')
            ->describe('methods.name', 'The method name.')
            ->describe('methods.type', 'The method return type.')
            ->describe('methods.args', 'List of arguments of the method.')
            ->describe('methods.isStatic', 'This method is static.');

        $this->command($c, ['--help'])
            ->printsExactly("<info>Class Generator</info> version <info>1.0.1</info><br><br>Generates basic code for a class.<br><br>Syntax: <success>[options] name parents interfaces attrs methods</success><br>Arguments:<br><tab><warn>name</warn> <success>String</success> The name of the class. <info>(required)</info><br><tab><warn>parents</warn> <success>String,...</success> List of parent classes names. <info>(default: [])</info><br><tab><warn>interfaces</warn> <success>String,...</success> List of implemented interfaces. <info>(default: [])</info><br><tab><warn>attrs</warn> <success>name:type:hasGetter:hasSetter:isStatic,...</success> List of attributes of the class. <info>(required)</info><br><tab><tab><warn>name</warn> <success>String</success> The name of the attribute. <info>(required)</info><br><tab><tab><warn>type</warn> <success>String</success> The type of the attribute. <info>(required)</info><br><tab><tab><warn>hasGetter</warn> <success>Boolean</success> Generates a getter for the attribute. <info>(default: true)</info><br><tab><tab><warn>hasSetter</warn> <success>Boolean</success> Generates a setter for the attribute. <info>(default: true)</info><br><tab><tab><warn>isStatic</warn> <success>Boolean</success> The attribute is static. <info>(default: false)</info><br><tab><warn>methods</warn> <success>name:type:args:isStatic,...</success> List of methods of the class. <info>(default: [])</info><br><tab><tab><warn>name</warn> <success>String</success> The method name. <info>(required)</info><br><tab><tab><warn>type</warn> <success>String</success> The method return type. <info>(required)</info><br><tab><tab><warn>args</warn> <success>name.type.default,...</success> List of arguments of the method. <info>(required)</info><br><tab><tab><tab><warn>name</warn> <success>String</success>  <info>(required)</info><br><tab><tab><tab><warn>type</warn> <success>String</success>  <info>(required)</info><br><tab><tab><tab><warn>default</warn> <success>String</success>  <info>(default: \"null\")</info><br><tab><tab><warn>isStatic</warn> <success>Boolean</success> This method is static. <info>(default: false)</info><br>");
    }
}

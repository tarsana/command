<?php
$I = new AcceptanceTester($scenario);

// Reads/Writes to console
$I->wantTo('Read/Write to console');

$I->runShellCommand("echo 'Foo' | php tests/samples/read-write.php");
$I->seeInShellOutput('Hello Foo');


// Parses command line arguments
$I->wantTo('Parse command line arguments');

$I->runShellCommand("php tests/samples/parse-args.php Foo 23 Bar:12,Baz");
$I->seeInShellOutput('{"name":"Foo","age":23,"friends":[{"name":"Bar","age":12},{"name":"Baz","age":""}]}');

$I->runShellCommand("php tests/samples/parse-args.php Foo Bar:12,Baz");
$I->seeInShellOutput('{"name":"Foo","age":"","friends":[{"name":"Bar","age":12},{"name":"Baz","age":""}]}');


/// Has subcommands: version & help are sub commands
// Shows the version
$I->wantTo('Show the version');

$I->runShellCommand("php tests/samples/read-write.php --version");
$I->seeInShellOutput('Read Write Sample version 1.1.0');

// Shows the help message
$I->wantTo('Show the help message');

$I->runShellCommand("php tests/samples/read-write.php help");
$I->seeInShellOutput('Read Write Sample 1.1.0');
$I->seeInShellOutput('Shows a hello message');
$I->seeInShellOutput('Syntax');
$I->seeInShellOutput('Subcommands');


// Handles the filesystem
$I->wantTo('Handle the filesystem');

$I->runShellCommand("php tests/samples/handle-fs.php");
$I->seeFileFound('./tests/samples/files/temp.txt');
$I->openFile('./tests/samples/files/temp.txt');
$I->seeFileContentsEqual('I am a temp file');
$I->deleteDir('./tests/samples/files');

// Renders templates
$I->wantTo('Renders templates');

$I->runShellCommand("php tests/samples/render-twig.php");
$I->seeInShellOutput('Hey Universe, how are you ?');


// Calls other commands
$I->wantTo('Calls other commands');

$I->runShellCommand("php tests/samples/calls-other-command.php");
$I->seeInShellOutput('Hello Happy World');

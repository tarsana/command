<?php
$I = new AcceptanceTester($scenario);

// Read/Write to console
$I->runShellCommand("echo 'Foo' | php tests/samples/read-write.php");
$I->seeInShellOutput('Hello Foo');

// Parse command line arguments
$I->runShellCommand("php tests/samples/parse-args.php Foo 23 Bar:12,Baz");
$I->seeInShellOutput('{"name":"Foo","age":23,"friends":[{"name":"Bar","age":12},{"name":"Baz","age":""}]}');

$I->runShellCommand("php tests/samples/parse-args.php Foo Bar:12,Baz");
$I->seeInShellOutput('{"name":"Foo","age":"","friends":[{"name":"Bar","age":12},{"name":"Baz","age":""}]}');

// Shows the version
$I->runShellCommand("php tests/samples/read-write.php --version");
$I->seeInShellOutput('Read Write version 1.0.0');


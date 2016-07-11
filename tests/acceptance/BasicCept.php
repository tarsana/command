<?php
$I = new AcceptanceTester($scenario);

$I->runShellCommand('php tests/samples/basic.php');
$I->seeInShellOutput('......');

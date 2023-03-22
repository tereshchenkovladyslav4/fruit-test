<?php

namespace App\Tests\Command;

use App\Command\MigrateFruitsCommand;

class MigrateFruitsCommandTest extends AbstractCommandTest
{

    public function testItSendsNoEmailByDefault(): void
    {
        $this->executeCommand([]);

        $this->assertEmailCount(0);
    }

    public function testItSendsAnEmailIfOptionProvided(): void
    {
        $this->executeCommand([ '--send-to' => 'john.doe@symfony.com' ]);

        $this->assertEmailCount(1);
    }

    protected function getCommandFqcn(): string
    {
        return MigrateFruitsCommand::class;
    }
}

<?php

namespace Tests\Unit\Commands;

use Gigasavvy\Uptime\Commands\RunCommand;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\TestCase;

class RunCommandTest extends TestCase
{
    /** @test */
    public function commandRunsSuccessfullyOutput()
    {
        $command = new RunCommand(
            $this->createMock('Gigasavvy\Uptime\UptimeChecker')
        );
        $tester = $this->makeCommandTester($command);

        $tester->execute([
            'command' => $command->getName(),
            'domains' => ['http://google.com', 'http://facebook.com'],
        ]);

        $output = $tester->getDisplay();

        $this->assertContains('Checking HTTPS integrity of the given domains...', $output);
        $this->assertContains('Checker completed with 0 failed domains.', $output);
        $this->assertNotContains('Failed domains:', $output);
    }

    /** @test */
    public function commandFailsOutput()
    {
        $checker = $this->createMock('Gigasavvy\Uptime\UptimeChecker');
        $checker
            ->method('run')
            ->willReturn(['https://foo.bar']);

        $command = new RunCommand($checker);
        $tester = $this->makeCommandTester($command);

        $tester->execute([
            'command' => $command->getName(),
            'domains' => ['http://foo.bar'],
        ]);

        $output = $tester->getDisplay();

        $this->assertContains('Failed domains:', $output);
        $this->assertContains('https://foo.bar', $output);
    }

    /**
     * Make a tester object for testing commands.
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    private function makeCommandTester($command)
    {
        (new Console())
            ->add($command);

        return new CommandTester($command);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Integration\Command;

use App\Command\MapRequestCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class MapInsuranceRequestCommandTest extends TestCase
{
    private CommandTester $commandToTest;

    protected function setUp(): void
    {
        $command = new MapRequestCommand();
        $this->commandToTest = new CommandTester($command);
    }

    /** @test */
    public function test_processes_with_valid_input_file(): void
    {
        // Create a temporary JSON file
        $jsonContent = json_encode([
            "holder" => "CONDUCTOR_PRINCIPAL",
            "occasionalDriver" => "SI",
            "prevInsurance_exists" => "NO",
            "prevInsurance_years" => 10
        ]);
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, $jsonContent);

        $this->commandToTest->execute([
            '--input' => $tempFile,
            '--provider' => 'acme'
        ]);

        unlink($tempFile);

        $output = $this->commandToTest->getDisplay();
        $this->assertStringContainsString('Request processed successfully', $output);
        $this->assertStringContainsString('<TarificacionThirdPartyRequest>', $output);
    }

    /** @test */
    public function test_processes_with_input_file_with_invalid_data(): void
    {
        // Create a temporary JSON file
        $jsonContent = json_encode([
            "holder" => "CONDUCTOR_PRINCIPAL"
        ]);
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, $jsonContent);

        $this->commandToTest->execute([
            '--input' => $tempFile,
            '--provider' => 'acme'
        ]);

        unlink($tempFile);

        $output = $this->commandToTest->getDisplay();
        $this->assertStringContainsString('Validation failed', $output);
        $this->assertEquals(1, $this->commandToTest->getStatusCode());
    }

    /** @test */
    public function test_processes_with_invalid_input_file(): void
    {
        $this->commandToTest->execute([
            '--input' => 'nonexistent.json',
            '--provider' => 'acme'
        ]);

        $output = $this->commandToTest->getDisplay();
        $this->assertStringContainsString('Input file not found', $output);
        $this->assertEquals(1, $this->commandToTest->getStatusCode());
    }
}
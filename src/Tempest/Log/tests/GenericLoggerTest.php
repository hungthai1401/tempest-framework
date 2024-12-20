<?php

declare(strict_types=1);

namespace Tempest\Log\Tests;

use PHPUnit\Framework\TestCase;
use Tempest\Log\Channels\AppendLogChannel;
use Tempest\Log\Channels\DailyLogChannel;
use Tempest\Log\Channels\WeeklyLogChannel;
use Tempest\Log\GenericLogger;
use Tempest\Log\LogConfig;

/**
 * @internal
 */
final class GenericLoggerTest extends TestCase
{
    public function test_append_log_channel_works(): void
    {
        $filePath = __DIR__ . '/logs/tempest.log';

        $config = new LogConfig(
            channels: [
                new AppendLogChannel($filePath),
            ],
        );

        $logger = new GenericLogger($config);

        $logger->info('test');

        $this->assertFileExists($filePath);

        $this->assertStringContainsString('test', file_get_contents($filePath));
    }

    protected function tearDown(): void
    {
        $files = glob(__DIR__ . '/logs/*.log');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function test_daily_log_channel_works(): void
    {
        $filePath = __DIR__ . '/logs/tempest-' . date('Y-m-d') . '.log';

        $config = new LogConfig(
            channels: [
                new DailyLogChannel(__DIR__ . '/logs/tempest.log'),
            ],
        );

        $logger = new GenericLogger($config);

        $logger->info('test');

        $this->assertFileExists($filePath);

        $this->assertStringContainsString('test', file_get_contents($filePath));
    }

    public function test_weekly_log_channel_works(): void
    {
        $filePath = __DIR__ . '/logs/tempest-' . date('Y-W') . '.log';

        $config = new LogConfig(
            channels: [
                new WeeklyLogChannel(__DIR__ . '/logs/tempest.log'),
            ],
        );

        $logger = new GenericLogger($config);

        $logger->info('test');

        $this->assertFileExists($filePath);

        $this->assertStringContainsString('test', file_get_contents($filePath));
    }

    public function test_multiple_same_log_channels_works(): void
    {
        $filePath = __DIR__ . '/logs/multiple-tempest1.log';
        $secondFilePath = __DIR__ . '/logs/multiple-tempest2.log';

        $config = new LogConfig(
            channels: [
                new AppendLogChannel($filePath),
                new AppendLogChannel($secondFilePath),
            ],
        );

        $logger = new GenericLogger($config);
        $logger->info('test');

        $this->assertFileExists($filePath);
        $this->assertStringContainsString('test', file_get_contents($filePath));

        $this->assertFileExists($secondFilePath);
        $this->assertStringContainsString('test', file_get_contents($secondFilePath));
    }
}

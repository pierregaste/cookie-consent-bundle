<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\Tests\DependencyInjection;

use ConnectHolland\CookieConsentBundle\DependencyInjection\CHCookieConsentExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class CHCookieConsentExtensionTest extends TestCase
{
    /**
     * @var CHCookieConsentExtension
     */
    private $chCookieConsentExtension;

    /**
     * @var ContainerBuilder
     */
    private $configuration;

    public function setUp()
    {
        $this->chCookieConsentExtension = new CHCookieConsentExtension();
        $this->configuration            = new ContainerBuilder();
    }

    public function testFullConfiguration(): void
    {
        $this->createConfiguration($this->getFullConfig());

        $this->assertParameter(['analytics', 'tracking', 'marketing', 'social_media'], 'ch_cookie_consent.categories');
        $this->assertParameter('dark', 'ch_cookie_consent.theme');
        $this->assertParameter(['app_cookies'], 'ch_cookie_consent.excluded_routes');
        $this->assertParameter(['/cookies'], 'ch_cookie_consent.excluded_paths');
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidConfiguration(): void
    {
        $this->createConfiguration($this->getInvalidConfig());
    }

    /**
     * create configuration.
     */
    protected function createConfiguration(array $config): void
    {
        $this->chCookieConsentExtension->load([$config], $this->configuration);

        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * get full config.
     */
    protected function getFullConfig(): array
    {
        $yaml = <<<EOF
categories: ['analytics', 'tracking', 'marketing', 'social_media']
theme: 'dark'
excluded_routes: ['app_cookies']
excluded_paths: ['/cookies']
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * get invalid config.
     */
    protected function getInvalidConfig(): array
    {
        $yaml = <<<EOF
theme: 'not_existing'
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * Test if parameter is set.
     */
    private function assertParameter($value, $key)
    {
        $this->assertSame($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }
}
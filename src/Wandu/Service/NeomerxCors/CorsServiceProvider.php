<?php
namespace Wandu\Service\NeomerxCors;

use Neomerx\Cors\Analyzer;
use Neomerx\Cors\Contracts\AnalysisStrategyInterface;
use Neomerx\Cors\Contracts\AnalyzerInterface;
use Neomerx\Cors\Contracts\Strategies\SettingsStrategyInterface;
use Neomerx\Cors\Strategies\Settings;
use Psr\Log\LoggerInterface;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class CorsServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(Settings::class)->after(function (Settings $settings, Config $config) {
            $settings->setServerOrigin($config->get('cors.server_origin'));
            $settings->setRequestAllowedOrigins($config->get('cors.allowed_origins', []));
            $settings->setRequestAllowedMethods($config->get('cors.allowed_methods', []));
            $settings->setRequestAllowedHeaders($config->get('cors.allowed_headers', []));
        });
        $app->alias(AnalysisStrategyInterface::class, Settings::class);
        $app->alias(SettingsStrategyInterface::class, Settings::class);

        $app->bind(Analyzer::class, function (AnalysisStrategyInterface $strategy) {
            return Analyzer::instance($strategy);
        })->after(function (Analyzer $analyzer) use ($app) {
            if ($app->has(LoggerInterface::class)) {
                $analyzer->setLogger($app->get(LoggerInterface::class));
            }
        });
        $app->alias(AnalyzerInterface::class, Analyzer::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}

<?php
namespace Wandu\DI\Providers\Neomerx;

use Neomerx\Cors\Analyzer;
use Neomerx\Cors\Contracts\AnalysisStrategyInterface;
use Neomerx\Cors\Contracts\AnalyzerInterface;
use Neomerx\Cors\Contracts\Strategies\SettingsStrategyInterface;
use Neomerx\Cors\Strategies\Settings;
use Psr\Log\LoggerInterface;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class CorsPsr7ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(Settings::class)->after(function (Settings $settings) use ($app) {
            $config = $app->get(ConfigInterface::class);
            $settings->setServerOrigin($config->get('neomerx.cors-psr7.server-origin'));
            $settings->setRequestAllowedOrigins($config->get('neomerx.cors-psr7.allowed-origins', []));
            $settings->setRequestAllowedMethods($config->get('neomerx.cors-psr7.allowed-methods', []));
            $settings->setRequestAllowedHeaders($config->get('neomerx.cors-psr7.allowed-headers', []));
        });
        $app->alias(AnalysisStrategyInterface::class, Settings::class);
        $app->alias(SettingsStrategyInterface::class, Settings::class);

        $app->closure(Analyzer::class, function (AnalysisStrategyInterface $strategy) {
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

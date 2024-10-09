<?php

namespace App\Providers;

use App\ClickHouse\ModelTransformer;
use App\ClickHouse\ModelTransformerInterface;
use App\ClickHouse\ModelTransformersBag;
use App\ClickHouse\QueryBag;
use App\ClickHouse\QueryInterface;
use App\ClickHouse\Repository;
use App\ClickHouse\RepositoryBag;
use App\ClickHouse\RepositoryInterface;
use App\ClickHouse\View;
use App\ClickHouse\Views\OrdersTotalsToday;
use App\ClickHouse\ViewsCreator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $clickhouseMapping = Config::get('clickhouse.mapping');

        $this->app->when(ModelTransformersBag::class)
            ->needs(ModelTransformerInterface::class)
            ->give(function ($app) use ($clickhouseMapping) {
                $transformers = [];
                foreach ($clickhouseMapping as $modelClass => $mapping) {
                    /** @var ModelTransformer $transformer */
                    $transformer = array_key_exists('transformer', $mapping)
                        ? $app->make($mapping['transformer'])
                        : $app->make(ModelTransformer::class);

                    $transformer->setModelClass($modelClass);

                    $transformers[$modelClass] = $transformer;
                }

                return $transformers;
            });

        $this->app->when(RepositoryBag::class)
            ->needs(RepositoryInterface::class)
            ->give(function ($app) use ($clickhouseMapping) {
                $repositories = [];
                foreach ($clickhouseMapping as $modelClass => $mapping) {
                    /** @var RepositoryInterface $repository */
                    $repository = array_key_exists('repository', $mapping)
                        ? $app->make($mapping['repository'])
                        : $app->make(Repository::class);

                    $repository->setModelClass($modelClass);

                    $repositories[$modelClass] = $repository;
                }

                return $repositories;
            });

        $this->app->tag([
            /// Deprecated, look QuickQueries
        ], [QueryInterface::class]);

        $this->app->when(QueryBag::class)
            ->needs(QueryInterface::class)
            ->giveTagged(QueryInterface::class);

        $this->app->tag([
            OrdersTotalsToday::class
        ], [View::class]);

        $this->app->when(ViewsCreator::class)
            ->needs(View::class)
            ->giveTagged(View::class);

        /**
         * binding clickhouse repos, and set model class (for using without repository bag)
         */
        $this->app->resolving(RepositoryInterface::class, function ($repository) use ($clickhouseMapping) {
            /** @var RepositoryInterface $repository */
            foreach ($clickhouseMapping as $modelClass => $mapping) {
                $classNameFromMapping = Arr::get($mapping, 'repository', '');
                if ($repository instanceof $classNameFromMapping) {
                    $repository->setModelClass($modelClass);
                    break;
                }
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

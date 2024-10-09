<?php

namespace App\Console\Commands\Analytics;

use App\ClickHouse\Models\Base;
use App\ClickHouse\SchemaUpdater;
use App\Entities\AnalyticsSite;
use Doctrine\ORM\EntityManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class CreateSiteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:site:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new site in db';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param EntityManager $entityManager
     * @return int
     */
    public function handle(EntityManager $entityManager)
    {
        try {
            $url = $this->getUrlFromIO();

            $repository = $entityManager->getRepository(AnalyticsSite::class);

            $site = $repository->findOneBy(['name' => $url]);

            if (!$site) {
                $site = new AnalyticsSite();
                $site->setName($url);
                $site->setKey(Base::guidv4());
                $entityManager->persist($site);
                $entityManager->flush();

                $key = $site->getKey();
                $this->output->writeln("\e[32m New site save successful \033[0m");
                $this->output->writeln("\e[32m Key: $key \033[0m");
            } else {
                $this->output->writeln("\e[31m Can't create new site because db contain site with this url \033[0m");
            }

        } catch (\Exception $e) {
            $this->output->writeln("\033[31m " . $e->getMessage() . "\033[0m");
        }

        return 0;
    }

    /**
     * Get url from io.
     * @return mixed
     * @throws ValidationException
     */
    private function getUrlFromIO()
    {
        $url = $this->ask('Write site url (https://example.com):');

        $validator = Validator::make(['url' => $url], [
            'url' => 'required|url'
        ]);

        $validator->validate();
        return $url;
    }
}

<?php

namespace App\Console\Commands;

use App\Entity;
use Illuminate\Console\Command;

class NpaLinkableParseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:entity_npa_linkables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexing entity npas to npa linkables';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $entities = Entity::query()->whereNotIn('type', 'constructor')->get();

        foreach ($entities as $entity) {
            $data = $entity->data()->whereNull('user_id')->orderBy('version', 'DESC')->first();
            if ($data) \Dogovor24\Queue\Jobs\Document\DocumentCreatedJob::dispatch($data->id);
        }
    }
}

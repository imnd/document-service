<?php

namespace App\Console\Commands;

use App\EntityData;
use App\Services\EntityDataService;
use App\Services\EntityService;
use Illuminate\Console\Command;
use Swaggest\JsonDiff\JsonDiff;

class RollbackEntityVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entity:rollback {entity_id} {version} {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback entity version';

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
     * @throws \Swaggest\JsonDiff\Exception
     */
    public function handle()
    {
        $entityId = $this->argument('entity_id');
        $version = $this->argument('version');
        $userId = $this->argument('user_id') ?? null;

        $entityData = EntityData::where('entity_id', $entityId)->where('version', $version)->where('user_id', $userId)->first();
        $payload = json_decode(json_encode((new EntityDataService($entityData))->getPayload()), true);

        $entityService = new EntityService($entityData->entity);
        $previousEntityData = $entityService->getLatestData($userId);

        if ($previousEntityData) {
            $diff  = new JsonDiff($payload, $previousEntityData->payload);
            $patch = $diff->getPatch()->jsonSerialize();
            $previousEntityData->payload = null;
            $previousEntityData->diff    = $patch;
            $previousEntityData->save();
        }

        $newData = new EntityData();

        $newData->user_id = $userId;
        $newData->payload = $payload;
        $newData->version = $previousEntityData ? ++$previousEntityData->version : 1;

        $newData->entity()->associate($entityData->entity);
        $newData->save();
    }
}

<?php

namespace App\Console\Commands;

use App\NpaLink;
use Illuminate\Console\Command;

class NpaLinksPayload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:npa_links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $npas = NpaLink::all();
        $keys = ['part', 'section', 'section_paragraph', 'section_subsection', 'chapter', 'chapter_paragraph', 'chapter_subsection', 'article', 'point', 'subpoint', 'indent', 'piece', 'piece_indent'];

        foreach ($npas as $npa) {
            if (array_diff(array_keys($npa->payload), $keys)) {
                $payload = [];
                foreach ($npa->payload as $key => $value) {
                    if (!is_null($value))
                        $payload[$keys[$key]] = $value;
                }
                $npa->payload = $payload;
                $npa->save();
            }
        }
    }
}

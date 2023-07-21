<?php

namespace ShreyaSarker\LaraCrud\Commands;

use Illuminate\Console\Command;
use ShreyaSarker\LaraCrud\Services\ControllerGeneratorService;
use ShreyaSarker\LaraCrud\Services\MigrationGeneratorService;
use ShreyaSarker\LaraCrud\Services\ModelGeneratorService;
use ShreyaSarker\LaraCrud\Services\RequestGeneratorService;
use ShreyaSarker\LaraCrud\Services\RouteGeneratorService;
use ShreyaSarker\LaraCrud\Services\ViewsGeneratorService;
use ShreyaSarker\LaraCrud\Utils\TypeUtil;

class MakeCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {name} {--fields= : Field names for the form, validation & migration.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create crud operations with a single command';

    /**
     * The service handlers.
     *
     * @var GlobalService
     * @var ModelGeneratorService
     * @var RequestGeneratorService
     * @var ControllerGeneratorService
     */

    protected ModelGeneratorService $modelGeneratorService;
    protected RequestGeneratorService $requestGeneratorService;
    protected ControllerGeneratorService $controllerGeneratorService;
    protected RouteGeneratorService $routeGeneratorService;
    protected ViewsGeneratorService $viewsGeneratorService;
    protected MigrationGeneratorService $migrationGeneratorService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ModelGeneratorService $modelGeneratorService,
        RequestGeneratorService $requestGeneratorService,
        ControllerGeneratorService $controllerGeneratorService,
        RouteGeneratorService $routeGeneratorService,
        ViewsGeneratorService $viewsGeneratorService,
        MigrationGeneratorService $migrationGeneratorService)
    {
        parent::__construct();

        $this->modelGeneratorService = $modelGeneratorService;
        $this->requestGeneratorService = $requestGeneratorService;
        $this->controllerGeneratorService = $controllerGeneratorService;
        $this->routeGeneratorService = $routeGeneratorService;
        $this->viewsGeneratorService = $viewsGeneratorService;
        $this->migrationGeneratorService = $migrationGeneratorService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        $fields = $this->parseFields($this->option('fields'));

        if(gettype($fields) === 'string')
        {
            $this->error($fields);

        } elseif(gettype($fields) === 'array' && count($fields) === 0)
        {
            $confirm = $this->confirm('There is no field specify. Do you want to continue?');
            if ($confirm)
            {
                $this->callHandlers($name, $fields);
            } else
            {
                $this->info('Crud generation canceled.');
                return 1;
            }
        } else
        {
            $this->callHandlers($name, $fields);
        }
        return 0;

    }

    private function callHandlers($name, $fields)
    {
        $this->info($this->migrationGeneratorService->generate($name, $fields));
        $this->info($this->modelGeneratorService->generate($name));
        $this->info($this->requestGeneratorService->generate($name, $fields));
        $this->info($this->controllerGeneratorService->generate($name));
        $this->info($this->viewsGeneratorService->generate($name, $fields));
        $this->info($this->routeGeneratorService->generate($name));
    }

    private function parseFields($fields)
    {
        $validationType = TypeUtil::getValidationType();
        $sqlColumnType = TypeUtil::getSqlColumnType();
        $fields= rtrim($fields, ';');
        $formFields = [];
        $fieldsArray = explode(';', $fields);
        if($fields){
            foreach($fieldsArray as $key => $item){
                $itemArray = explode('#', $item);
                $formFields[$key]['name'] = isset($itemArray[0]) ? trim($itemArray[0]) : null;
                $formFields[$key]['type'] = isset($itemArray[1]) ? trim($itemArray[1]) : null;
                if (!isset($validationType[trim($itemArray[1])]) || !isset($sqlColumnType[trim($itemArray[1])])){
                    return "Sorry! $itemArray[1] type not available to generate CRUD this time. We will add this on later version.";
                }
                $formFields[$key]['validations'] = isset($itemArray[1]) ? $validationType[trim($itemArray[1])] : null;
                $formFields[$key]['sqlColumn'] = isset($itemArray[1]) ? $sqlColumnType[trim($itemArray[1])] : null;

                if ($formFields[$key]['type'] === 'select' || $formFields[$key]['type'] === 'select' && isset($itemArray[2])){
                    $options = isset($itemArray[2]) ? trim($itemArray[2]) : null;

                    if ($options){
                        $options = str_replace('options=', '', $options);
                        $options= preg_replace('/(?<!")(?<!\w)(\w+)(?!")(?!\w)/', '"$1"', $options);
                        $decodedOptions = json_decode($options, true);
                        if (!$decodedOptions) {
                            return 'Please provide a proper format for options.';
                        }

                        $formFields[$key]['options'] = $decodedOptions;
                    }
                }
            }
        }
        return $formFields;
    }
}

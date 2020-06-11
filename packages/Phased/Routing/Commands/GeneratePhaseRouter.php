<?php

declare(strict_types=1);

namespace Phased\Routing\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Phased\Routing\Facades\Phase;

class GeneratePhaseRouter extends Command
{
    /**
     * The console command name.
     *
     * --json => format output as json (as opposed to table view)
     * --advanced => include advanced details (config etc)
     *   should output config as json be a command by itself? `php artisan config:show {config_name} --json`
     *
     * @var string
     */
    protected $signature = 'phase:routes {--json} {--config}';

    /** @var \Illuminate\Routing\Router */
    protected $router;

    /** @var array */
    protected $tableHeaders = ['Group Prefix', 'URI', 'Navigation Name', 'Middleware'];

    /**
     * Create a new route command instance.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function __construct(Router $router)
    {
        parent::__construct();
        $this->router = $router;
    }

    /**
     * Hides the command from the php artisan route helper.
     */
    protected function configure(): void
    {
        $this->setHidden(true);
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($this->option('json')) {
            $this->outputJson();
        } else {
            $this->outputTable();
        }
    }

    public function getFormattedRoutes(): Collection
    {
        return collect(Phase::getRoutes())->map(function ($route) {
            $name = $this->router->getRoutes()->getByAction($route['action']['controller'])->getName()
                ?? str_replace($route['action']['namespace'].'\\', '', $route['action']['controller']);

            return [
                'prefix' => $route['action']['prefix'],
                'uri' => $route['uri'],
                'name' => $name,
                'middleware' => collect($route['action']['middleware'])->join(','),
            ];
        })->sortBy('uri')->values();
    }

    public function getJsonOutput($withConfig = false)
    {
        $routes = $this->getFormattedRoutes();

        if ($withConfig) {
            return json_encode([
                'config' => config('phase'),
                'routes' => $routes,
            ]);
        }

        return json_encode($routes);
    }

    public function outputJson()
    {
        $this->line(
            $this->getJsonOutput(
                $this->option('config')
            )
        );
    }

    public function outputTable(): void
    {
        $this->table($this->tableHeaders, $this->getFormattedRoutes());
    }
}

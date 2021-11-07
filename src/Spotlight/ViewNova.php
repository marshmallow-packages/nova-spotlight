<?php

namespace Marshmallow\NovaSpotlight\Spotlight;

use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use LivewireUI\Spotlight\Spotlight;
use Laravel\Nova\Contracts\QueryBuilder;
use LivewireUI\Spotlight\SpotlightCommand;
use Laravel\Nova\Http\Requests\NovaRequest;
use LivewireUI\Spotlight\SpotlightSearchResult;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightCommandDependencies;

class ViewNova extends SpotlightCommand
{
    protected $query;
    protected $links;

    /**
     * This is the name of the command that will be shown in the Spotlight component.
     */
    protected string $name = 'View';

    /**
     * This is the description of your command which will be shown besides the command name.
     */
    protected string $description = 'View Nova Resources';

    /**
     * Defining dependencies is optional. If you don't have any dependencies you can remove this method.
     * Dependencies are asked from your user in the order you add the dependencies.
     */
    public function dependencies(): ?SpotlightCommandDependencies
    {
        $request = resolve(NovaRequest::class);
        $resources = Nova::globallySearchableResources($request);

        $this->request = $request;
        if (!session()->has('resources')) {
            session()->put('resources', $resources);
        }

        return SpotlightCommandDependencies::collection()
            ->add(
                SpotlightCommandDependency::make('resources')
                    ->setPlaceholder('Which Resource type?')
            )->add(
                SpotlightCommandDependency::make('resource')
                    ->setPlaceholder('Search for a result...')
            );
    }

    /**
     * Spotlight will resolve dependencies by calling the search method followed by your dependency name.
     * The method will receive the search query as the parameter.
     */
    public function searchResources($query)
    {
        $this->query = $query;
        $this->request = resolve(NovaRequest::class);

        foreach (session()->get('resources') as $resourceClass) {
            $resourceTitle = Str::afterLast(
                $resourceClass,
                '\\'
            );

            $resources[] = new SpotlightSearchResult(
                $resourceClass,
                $resourceTitle,
                sprintf('Open %s Resource ', $resourceTitle)
            );
        }

        return $resources;
    }

    /**
     * Spotlight will resolve dependencies by calling the search method followed by your dependency name.
     * The method will receive the search query as the parameter.
     */
    public function searchResource($query, $resources)
    {
        $this->query = $query;
        $this->request = resolve(NovaRequest::class);

        $resourceClass = $resources;

        $query = app()->make(QueryBuilder::class, [$resourceClass])->search(
            $this->request,
            $resourceClass::newModel()->newQuery()->with($resourceClass::$with),
            $this->query
        );

        return $query
            ->get()
            ->mapInto($resourceClass)
            ->map(function ($resource) use ($resourceClass) {
                return $this->transformResult($resourceClass, $resource);
            });
    }


    /**
     * When all dependencies have been resolved the execute method is called.
     * You can type-hint all resolved dependency you defined earlier.
     */
    public function execute(Spotlight $spotlight, $resource, $type = 'view')
    {
        $links = session()->get('links');
        $link = $links[$resource];

        session()->forget(['resources', 'links']);
        $spotlight->redirect($link);
    }

    /**
     * Transform the result from resource.
     *
     * @param  string  $resourceClass
     * @param  \Laravel\Nova\Resource  $resource
     * @return array
     */
    protected function transformResult($resourceClass, Resource $resource)
    {
        $model = $resource->model();

        $this->links[$model->getKey()] = url(Nova::path() . '/resources/' . $resourceClass::uriKey() . '/' . $model->getKey());

        session()->put('links', $this->links);

        return new SpotlightSearchResult(
            $model->getKey(),
            $resource->title(),
            sprintf('View %s', $resourceClass::label())
        );
    }

    /**
     * You can provide any custom logic you want to determine whether the
     * command will be shown in the Spotlight component. If you don't have any
     * logic you can remove this method. You can type-hint any dependencies you
     * need and they will be resolved from the container.
     */
    public function shouldBeShown(): bool
    {
        return true;
    }
}

<?php

namespace Marshmallow\NovaSpotlight\Spotlight;

use Laravel\Nova\Nova;
use Illuminate\Support\Str;
use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;
use Laravel\Nova\Http\Requests\NovaRequest;
use LivewireUI\Spotlight\SpotlightSearchResult;
use LivewireUI\Spotlight\SpotlightCommandDependency;
use LivewireUI\Spotlight\SpotlightCommandDependencies;

class NewNova extends SpotlightCommand
{
    protected $query;
    protected $links;

    /**
     * This is the name of the command that will be shown in the Spotlight component.
     */
    protected string $name = 'New';

    /**
     * This is the description of your command which will be shown besides the command name.
     */
    protected string $description = 'New Nova Resources';

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
                sprintf('New %s Resource ', $resourceTitle)
            );
        }

        return $resources;
    }

    /**
     * When all dependencies have been resolved the execute method is called.
     * You can type-hint all resolved dependency you defined earlier.
     */
    public function execute(Spotlight $spotlight, $resources)
    {
        $link = url(Nova::path() . '/resources/' . $resources::uriKey() . '/new');
        session()->forget(['resources']);
        $spotlight->redirect($link);
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

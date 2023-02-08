<?php

namespace Kiwilan\Steward\Services\Typescriptable;

use Illuminate\Support\Facades\File;
use Kiwilan\Typescriptable\Commands\TypescriptableZiggyCommand;
use Tightenco\Ziggy\Ziggy;

class ZiggyType
{
    protected function __construct(
      public string $filePath,
    ) {
    }

    public static function make(TypescriptableZiggyCommand $command): self
    {
        $outputPath = $command->typescriptable->outputPath;
        $outputFile = $command->typescriptable->outputFile;

        $file = "{$outputPath}/{$outputFile}";
        $service = new self($file);
        $generatedRoutes = $service->generate();

        if (! File::isDirectory($outputPath)) {
            File::makeDirectory($outputPath);
        }
        File::put($file, $generatedRoutes);

        return $service;
    }

    private function generate(): string
    {
        $ziggy = (new Ziggy(false, null));

        $routes = collect($ziggy->toArray()['routes'])
            ->map(function ($route, $key) {
                $methods = json_encode($route['methods'] ?? []);

                return "  '{$key}': { 'uri': '{$route['uri']}', 'methods': {$methods} }";
            })
            ->join("\n");

        $route = '$route';
        $isRoute = '$isRoute';
        $currentRoute = '$currentRoute';
        $page = '$page';

        return <<<typescript
// This file is auto generated by GenerateTypeCommand.
import {Config, InputParams, Router, RouteParamsWithQueryOverload} from "ziggy-js";

declare type LaravelRoutes = {
{$routes}
};

declare interface IPage {
  props: {
    user: App.Models.User
    jetstream: { canCreateTeams: boolean, hasTeamFeatures: boolean, managesProfilePhotos: boolean, hasApiFeatures: boolean, canUpdateProfileInformation: boolean, canUpdatePassword: boolean, canManageTwoFactorAuthentication: boolean, hasAccountDeletionFeatures: boolean }
    [x: string]: unknown;
    errors: import("@inertiajs/core").Errors & import("@inertiajs/core").ErrorBag;
  }
  url?: string;
  version?: string;
  scrollRegions?: { top: number; left: number; }[];
  rememberedState?: Record<string, unknown>;
  resolvedErrors?: import("@inertiajs/core").Errors;
};

declare global {
  declare interface ZiggyLaravelRoutes extends LaravelRoutes {}
  declare interface InertiaPage extends IPage {}
}

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    $route: (name: keyof ZiggyLaravelRoutes, params?: RouteParamsWithQueryOverload | RouteParam, absolute?: boolean, customZiggy?: Config) => string;
    $isRoute: (name: keyof ZiggyLaravelRoutes, params?: RouteParamsWithQueryOverload) => boolean;
    $currentRoute: () => string;
    $page: InertiaPage
    sessions: { agent: { is_desktop: boolean; browser: string; platform: string; }, ip_address: string; is_current_device: boolean; last_active: string; }[];
  }
}

export { LaravelRoutes };
typescript;
    }
}

<?php

namespace App\Facade;

use App\Services\Application\Contracts\ApplicationServiceInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin ApplicationServiceInterface
 */
final class ApplicationSettings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ApplicationServiceInterface::class;
    }
}

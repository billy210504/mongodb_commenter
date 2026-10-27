<?php

namespace Billy\MongodbCommenter\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use MongoDB\Laravel\Eloquent\Builder;

trait HasComments
{
    /**
     * Boot the trait to attach a global scope to all MongoDB queries.
     */
    public static function bootHasComments(): void
    {
        static::addGlobalScope('mongo_commenter', function (Builder $builder): void {
            if (! config('mongodb-commenter.enabled', true)) {
                return;
            }

            $comment = static::resolveMongoComment();
            if ($comment !== '') {
                $query = $builder->getQuery();
                if (isset($query->options) && is_array($query->options)) {
                    $query->options['comment'] = $comment;
                } else {
                    $query->options = array_merge((array) ($query->options ?? []), ['comment' => $comment]);
                }
            }
        });
    }

    /**
     * Resolve the formatted SQLCommenter string for the current execution context.
     */
    public static function resolveMongoComment(): string
    {
        $tags = [];

        // 1. Tag Application Name (Berlaku untuk HTTP maupun Console/Queue)
        if (config('mongodb-commenter.include_app_name', true)) {
            $appName = config('mongodb-commenter.app_name') ?: config('app.name');
            if ($appName) {
                $tags['app_name'] = $appName;
            }
        }

        // 2. HTTP Request Context
        if (Route::current()) {
            $route = Route::current();

            if (config('mongodb-commenter.include_controller', true)) {
                $action = $route->getActionName();
                if ($action) {
                    if (str_contains($action, '@')) {
                        [$controller, $method] = explode('@', $action);
                        $tags['controller'] = class_basename($controller);
                        $tags['action'] = $method;
                    } elseif ($action !== 'Closure') {
                        $tags['controller'] = class_basename($action);
                        $tags['action'] = '__invoke';
                    } else {
                        $tags['action'] = 'Closure';
                    }
                }
            }

            if (config('mongodb-commenter.include_route', true)) {
                $tags['route'] = $route->getName() ?: ($route->uri() ?: request()->path());
            }

            if (config('mongodb-commenter.include_user', true)) {
                $user = Auth::user();
                if ($user) {
                    $userIdentifier = $user->nup ?? (method_exists($user, 'getAuthIdentifier') ? $user->getAuthIdentifier() : ($user->id ?? null));
                    if ($userIdentifier !== null && $userIdentifier !== '') {
                        $tags['user'] = (string) $userIdentifier;
                    }
                }
            }
            // 3. Console / Artisan Command Context
        } elseif (App::runningInConsole()) {
            if (config('mongodb-commenter.include_command', true)) {
                $command = static::resolveConsoleCommand();
                if ($command !== '') {
                    $tags['command'] = $command;
                }
            }
            // 4. Fallback jika HTTP request tidak cocok dengan registered route
        } else {
            if (config('mongodb-commenter.include_route', true)) {
                $tags['route'] = request()?->path() ?: '';
            }
        }

        if (empty($tags)) {
            return '';
        }

        // Urutkan tags secara alfabetis (standar SQLCommenter)
        ksort($tags);

        $formatted = [];
        foreach ($tags as $key => $val) {
            $valEscaped = str_replace("'", "\'", (string) $val);
            $formatted[] = "{$key}='{$valEscaped}'";
        }

        return implode(',', $formatted);
    }

    /**
     * Resolve the active artisan command name when running in console.
     */
    protected static function resolveConsoleCommand(): string
    {
        $argv = $_SERVER['argv'] ?? [];
        if (count($argv) > 1) {
            $args = array_slice($argv, 1);

            return implode(' ', $args);
        }

        return 'console';
    }
}

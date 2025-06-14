<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\PortalPage;

class CheckPermission
{
    public function handle($request, Closure $next, $action)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $role = $user->role;

        $pageId = $this->getPageIdFromRequest($request);

        if ($role && $role->permissions()->where('page_id', $pageId)->where($action, 1)->exists()) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    private function getPageIdFromRequest($request)
    {
        $requestUri = $request->getRequestUri();
        $path = trim(parse_url($requestUri, PHP_URL_PATH), '/');
        $segments = explode('/', $path);

        if (in_array($request->method(), ['PUT', 'DELETE'])) {
            $endpoint = $segments[count($segments) - 2] ?? null;
        } else {
            $endpoint = end($segments);
        }

        return $endpoint ? PortalPage::where('endpoint', $endpoint)->value('id') : null;
    }

}

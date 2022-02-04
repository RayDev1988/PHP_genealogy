<?php declare(strict_types=1);

namespace App\Tenant\Middleware;

use Closure;
use App\Tenant\Manager;
use LaravelEnso\Companies\Models\Company;

class Multitenancy
{
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return $next($request);
        }

        $company = ($request->user()->belongsToAdminGroup() && $request->has('_tenantId'))
            ? Company::find($request->get('_tenantId'))
            : $request->user()->company();


        if (optional($company)->isTenant()) {
            Manager::fromModel($company)->connect();
        }

        if ($request->has('_tenantId')) {
            $request->request->remove('_tenantId');
        }

        return $next($request);
    }
}

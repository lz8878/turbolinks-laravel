<?php

namespace Lym125\Turbolinks;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Session\Session;

/**
 * Reference: https://github.com/turbolinks/turbolinks-rails.
 */
class Turbolinks
{
    /**
     * @var \Illuminate\Contracts\Session\Session
     */
    protected $session;

    /**
     * Create a new turbolinks middleware instance.
     * 
     * @param \Illuminate\Contracts\Session\Session $session $session
     *
     * @return void
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $turbolinksLocation = $this->pullTurbolinksLocationFromSession();

        $response = $next($request);

        // Set Turbolinks-Location response header.
        if ($turbolinksLocation) {
            $response->withHeaders([
                'Turbolinks-Location' => $turbolinksLocation,
            ]);
        }

        if ($response instanceof RedirectResponse) {
            $turbolinks = $this->session->pull('turbolinks');
            $location = $response->getTargetUrl();

            if (false !== $turbolinks && $request->ajax() && !$request->isMethod('get')) {
                return $response->setStatusCode(201)
                                ->setContent($this->turbolinksVisitScript($location, $turbolinks))
                                ->withHeaders(['Content-Type' => 'text/javascript']);
            } elseif ($request->hasHeader('Turbolinks-Referrer')) {
                $this->storeTurbolinksLocationInSession($location);
            }
        }

        return $response;
    }

    /**
     * Turbolinks visit script.
     *
     * @param string $location
     * @param string $action
     *
     * @return string
     */
    protected function turbolinksVisitScript(string $location, string $action = null)
    {
        $action = 'advance' === $action ? $action : 'replace';

        $scripts = [];
        $scripts[] = 'Turbolinks.clearCache()';
        $scripts[] = "Turbolinks.visit('{$location}', { action: '{$action}' })";

        return implode("\n", $scripts);
    }

    /**
     * Pull turbolinks location from session.
     *
     * @return string|null
     */
    protected function pullTurbolinksLocationFromSession()
    {
        if ($location = $this->session->get($this->turbolinksLocationKey())) {
            $this->session->forget($this->turbolinksLocationKey());
        }

        return $location;
    }

    /**
     * Store turbolinks location in session.
     *
     * @param string $location
     *
     * @return void
     */
    protected function storeTurbolinksLocationInSession(string $location)
    {
        $this->session->put($this->turbolinksLocationKey(), $location);
    }

    /**
     * Turbolinks Location key.
     *
     * @return string
     */
    protected function turbolinksLocationKey()
    {
        return '_turbolinks_location';
    }
}

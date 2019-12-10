<?php

namespace Zdrojowa\KernelConnector\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Kosinski\Amqp\Facades\Amqp;
use Selene\Support\Facades\Core;
use Zdrojowa\AuthenticationLink\AuthenticationLink;
use Zdrojowa\KernelConnector\Producers\AclPresenceProducer;

class KernelSynchronizationController extends Controller
{

    public function __construct(AuthenticationLink $authenticationLink)
    {


        $this->authenticationlink = $authenticationLink;
    }

    public function sync(Request $request) {
        $this->system = $this->authenticationlink->currentSystem();

        if(!$this->system || !Hash::check($this->authenticationlink->getSystemCode(), $request->code)) {
            return response('Unauthorized', 401);
        };


        $this->producePresence(Core::aclRepository()->getPresences()->toArray(), null);

        return response('Synchronized', 200);
    }

    private function producePresence(array $aclPresences, string $parentAnchor = null) {
        foreach ($aclPresences as $presence) {
            Amqp::publish(new AclPresenceProducer($presence, $this->authenticationlink, $this->system->id, $parentAnchor));

            $children = $presence->getChildren();
            if($children !== null && $children->count() > 0) $this->producePresence($children->toArray(), $presence->getAnchor());
        }
    }
}

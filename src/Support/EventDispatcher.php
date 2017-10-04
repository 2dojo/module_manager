<?php

namespace TwoDojo\ModuleManager\Support;

use TwoDojo\Module\Contracts\EventListener;

class EventDispatcher
{
    protected $listeners = [];

    public function registerListener(EventListener $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * Dispatch an event
     *
     * @param string $group
     * @param string $event
     * @param array $arguments
     */
    public function dispatchEvent(string $group, string $event, array $arguments = [])
    {
        foreach ($this->listeners as $listener) {
            if ($listener->getEventGroup() === $group) {
                $listener->onEventReceived($event, $arguments);
            }
        }
    }
}

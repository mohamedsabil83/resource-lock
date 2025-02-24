<?php

namespace Kenepa\ResourceLock\Resources\Pages\Concerns;

trait UsesSimpleResourceLock
{
    use UsesLocks;

    public string $returnUrl;

    public $resourceRecord;

    public string $resourceLockType;

    private bool $isLockable = true;

    public function bootUsesSimpleResourceLock(): void
    {
        $this->listeners = array_merge($this->listeners, [
            'resourceLockObserver::unload' => 'resourceLockObserverUnload',
            'resourceLockObserver::unlock' => 'resourceLockObserverUnlock',
            $this->id . '-table-action' => 'test',
        ]);
    }

    public function mountTableAction(string $name, ?string $record = null)
    {
        parent::mountTableAction($name, $record);
        $this->resourceRecord = $this->getMountedTableActionRecord();

        $this->returnUrl = $this->getResource()::getUrl('index');
        $this->checkIfResourceLockHasExpired($this->resourceRecord);
        $this->lockResource($this->resourceRecord);
    }

    public function callMountedTableAction(?string $arguments = null) {
        if (config('resource-lock.check_locks_before_saving', true)) {
            $this->resourceRecord->refresh();
            if ($this->resourceRecord->isLocked() && !$this->resourceRecord->isLockedByCurrentUser()) {
                $this->checkIfResourceLockHasExpired($this->resourceRecord);
                $this->lockResource($this->resourceRecord);
                return;
            }
        }
        parent::callMountedTableAction($arguments);
    }

    public function resourceLockObserverUnload()
    {
        $this->resourceRecord->unlock();
    }

    public function resourceLockObserverUnlock()
    {
        if ($this->resourceRecord->unlock(force: true)) {
            $this->closeLockedResourceModal();
            $this->resourceRecord->lock();
        }
    }
}

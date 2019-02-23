<?php namespace Greenf\Quasar\Modules\Context\Domain;

interface ContextRepository {

    public function save(Context $context): void;

    /**
     * @param string $name
     *
     * @return Context
     */
    public function get(string $name);
}
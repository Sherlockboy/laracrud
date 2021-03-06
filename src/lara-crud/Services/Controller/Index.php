<?php

namespace LaraCrud\Services\Controller;

use Illuminate\Support\Str;
use LaraCrud\Contracts\ViewAbleMethod;
use LaraCrud\Services\FullTextSearch;
use Laravel\Scout\Searchable;

class Index extends ControllerMethod implements ViewAbleMethod
{
    /**
     * @return bool
     */
    public function isSearchAble()
    {
        $traits = class_uses($this->model);

        return in_array(Searchable::class, $traits) || in_array(FullTextSearch::class, $traits);
    }

    /**
     * Set necessary data.
     *
     * @throws \ReflectionException
     *
     * @return $this
     */
    protected function beforeGenerate()
    {
        $this->setParameter('Request', '$request');

        if ($this->parentModel) {
            $this->setVariable($this->getParentShortName(), '$'.$this->getParentShortName())
                ->setParameter(ucfirst($this->getParentShortName()), '$'.$this->getParentShortName());
        }

        $this->setVariable(Str::plural($this->getModelShortName()), '$builder->paginate(10)');

        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        $body = '$builder = '.ucfirst($this->getModelShortName()).'::';
        $body .= $this->isSearchAble() ? 'search($request->get(\'search\'))' : 'query';

        return $body.';';
    }
}

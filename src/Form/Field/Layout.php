<?php

namespace Kirby\Form\Field;

use Kirby\Cms\Layouts;
use Kirby\Toolkit\Str;

class Layout extends Blocks
{
    protected $layouts;

    public function __construct(array $params)
    {
        $this->setLayouts($params['layouts'] ?? ['1/1']);
        parent::__construct($params);
    }

    public function fill($value = null)
    {
        $value   = $this->valueFromJson($value);
        $layouts = Layouts::factory($value, ['parent' => $this->model])->toArray();

        foreach ($layouts as $layoutIndex => $layout) {
            foreach ($layout['columns'] as $columnIndex => $column) {
                $layouts[$layoutIndex]['columns'][$columnIndex]['blocks'] = $this->blocks->value($column['blocks'], false);
            }
        }

        $this->value = $layouts;
    }

    public function layouts(): ?array
    {
        return $this->layouts;
    }

    public function props(): array
    {
        return [
            'layouts' => $this->layouts()
        ] + parent::props();
    }

    protected function setLayouts(array $layouts = [])
    {
        $this->layouts = array_map(function ($layout) {
            return Str::split($layout);
        }, $layouts);
    }

    public function store($value)
    {
        $value = Layouts::factory($value, ['parent' => $this->model])->toArray();

        foreach ($value as $layoutIndex => $layout) {
            foreach ($layout['columns'] as $columnIndex => $column) {
                $value[$layoutIndex]['columns'][$columnIndex]['blocks'] = $this->blocks->toArray($column['blocks'] ?? []);
            }
        }

        return $this->valueToJson($value, $this->pretty());
    }

    public function validations(): array
    {
        return [];
    }

}

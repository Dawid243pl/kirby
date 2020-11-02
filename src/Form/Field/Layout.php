<?php

namespace Kirby\Form\Field;

use Kirby\Cms\BlocksField;
use Kirby\Cms\Layouts;
use Kirby\Form\Mixin\EmptyState;
use Kirby\Form\FieldClass;

class Layout extends FieldClass
{

    use EmptyState;

    protected $blocks;
    protected $fieldsets;
    protected $layouts;

    public function __construct(array $params)
    {
        $this->blocks = new BlocksField($params['model'] ?? site(), [
            'fieldsets' => $params['fieldsets'] ?? null
        ]);

        $this->fieldsets = $this->blocks->fieldsets();
        $this->value     = [];

        parent::__construct($params);

        $this->setEmpty($params['empty'] ?? null);
    }

    public function blocks()
    {
        return $this->blocks;
    }

    public function fieldsets()
    {
        return $this->fieldsets;
    }

    public function fill($value = null)
    {
        $this->value = $this->valueFromJson($value);
    }

    public function layouts(): ?array
    {
        return $this->layouts;
    }

    public function props(): array
    {
        $props = parent::props();

        $props['fieldsets'] = $this->fieldsets();
        $props['empty']     = $this->empty();
        $props['layouts']   = $this->layouts();

        return $props;
    }

    public function routes(): array
    {

        $field = $this;

        return [
            [
                'pattern' => 'uuid',
                'action'  => function () {
                    return ['uuid' => uuid()];
                }
            ],
            [
                'pattern' => 'fieldsets/(:any)',
                'method'  => 'GET',
                'action'  => function ($type) use ($field) {
                    $blocks   = $field->blocks();
                    $fields   = $blocks->fields($type);
                    $defaults = $blocks->form($fields, [])->data(true);
                    $content  = $blocks->form($fields, $defaults)->values();

                    return Block::factory([
                        'content' => $content,
                        'type'    => $type
                    ])->toArray();
                }
            ],
            [
                'pattern' => 'fieldsets/(:any)/fields/(:any)/(:all?)',
                'method'  => 'ALL',
                'action'  => function (string $fieldsetType, string $fieldName, string $path = null) use ($field) {
                    $blocks = $field->blocks();
                    $fields = $blocks->fields($fieldsetType);
                    $field  = $blocks->form($fields)->field($fieldName);

                    $fieldApi = $this->clone([
                        'routes' => $field->api(),
                        'data'   => array_merge($this->data(), ['field' => $field])
                    ]);

                    return $fieldApi->call($path, $this->requestMethod(), $this->requestData());
                }
            ],
        ];

    }

    protected function setLayouts(array $layouts = [])
    {
        $this->layouts = array_map(function ($layout) {
            return Str::split($layout);
        }, $layouts);
    }

    public function valueForPHP($value)
    {
        $value = Layouts::factory($value, ['parent' => $this->model])->toArray();

        foreach ($value as $layoutIndex => $layout) {
            foreach ($layout['columns'] as $columnIndex => $column) {
                $value[$layoutIndex]['columns'][$columnIndex]['blocks'] = $this->blocks->value($column['blocks'], false);
            }
        }

        return $value;
    }

    public function valueForTxt($value)
    {
        $value = Layouts::factory($value, ['parent' => $this->model])->toArray();

        foreach ($value as $layoutIndex => $layout) {
            foreach ($layout['columns'] as $columnIndex => $column) {
                $value[$layoutIndex]['columns'][$columnIndex]['blocks'] = $this->blocks->toArray($column['blocks'] ?? []);
            }
        }

        return json_encode($value);
    }

}

<?php

namespace Kirby\Form\Field;

use Kirby\Cms\Block;
use Kirby\Cms\BlocksField;
use Kirby\Form\FieldClass;
use Kirby\Form\Mixin\EmptyState;
use Kirby\Form\Mixin\Max;
use Kirby\Form\Mixin\Min;

class Blocks extends FieldClass
{
    use EmptyState;
    use Max;
    use Min;

    protected $fieldsets;
    protected $blocks;
    protected $value = [];

    public function __construct(array $params = [])
    {
        $this->blocks = new BlocksField($params['model'] ?? site(), $params);

        $this->fieldsets = $this->blocks->fieldsets();

        parent::__construct($params);

        $this->setGroup($params['group'] ?? 'blocks');
        $this->setMax($params['max'] ?? null);
        $this->setMin($params['min'] ?? null);
        $this->setPretty($params['pretty'] ?? false);
    }

    public function blocks()
    {
        return $this->blocks;
    }

    public function fieldsets(): array
    {
        return $this->fieldsets;
    }

    public function fill($value = null)
    {
        $this->value = $this->blocks()->value($this->valueFromJson($value));
    }

    public function isEmpty(): bool
    {
        return count($this->value()) === 0;
    }

    public function group(): string
    {
        return $this->group;
    }

    public function pretty(): bool
    {
        return $this->pretty;
    }

    public function props(): array
    {
        return [
            'fieldsets' => $this->fieldsets(),
            'group'     => $this->group(),
            'max'       => $this->max(),
            'min'       => $this->min(),
            'empty'     => $this->empty(),
        ] + parent::props();
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
                    $blocksField = $field->blocks();
                    $fields      = $blocksField->fields($type);
                    $defaults    = $blocksField->form($fields, [])->data(true);
                    $content     = $blocksField->form($fields, $defaults)->values();

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
                    $blocksField = $field->blocks();
                    $fields      = $blocksField->fields($fieldsetType);
                    $field       = $blocksField->form($fields)->field($fieldName);

                    $fieldApi = $this->clone([
                        'routes' => $field->api(),
                        'data'   => array_merge($this->data(), ['field' => $field])
                    ]);

                    return $fieldApi->call($path, $this->requestMethod(), $this->requestData());
                }
            ],
        ];
    }

    public function store($value)
    {
        return $this->valueToJson(
            $this->blocks()->toArray($value),
            $this->pretty()
        );
    }

    protected function setGroup(string $group = null)
    {
        $this->group = $group;
    }

    protected function setPretty(bool $pretty = false)
    {
        $this->pretty = $pretty;
    }

    public function validations(): array
    {
        return [
            'blocks' => function ($value) {
                return $this->blocks()->validate($value);
            }
        ];
    }
}

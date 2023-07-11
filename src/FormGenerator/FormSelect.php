<?php declare(strict_types=1);

namespace nortedevbr\eventoprobr\forms\FormGenerator;

use DOMDocument;
use nortedevbr\eventoprobr\forms\Traits\Tags;

/**
 *
 */
class FormSelect implements FormTagsInterface
{
    use Tags;

    /**
     * @var int
     */
    private int $index = 0;
    /**
     * @var DOMDocument
     */
    private DOMDocument $dom;
    /**
     * @var array|null
     */
    private ?array $selects = null;
    /**
     * @var array|null
     */
    private ?array $parents = null;
    /**
     * @var array|null
     */
    private ?array $labels = null;
    /**
     * @var array|null
     */
    private ?array $helpers = null;

    /**
     * @param DOMDocument $dom
     */
    public function __construct(DOMDocument $dom)
    {
        $this->dom = $dom;
    }

    /**
     * @param int $index
     * @return void
     */
    public function setIndex(int $index)
    {
        $this->index = $index;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        $fields = [];
        if (!empty($this->selects)) {
            foreach ($this->selects as $key => $select) {
                if (isset($this->parents[$key])) {
                    if (isset($this->labels[$key])) {
                        $this->parents[$key]->appendChild($this->labels[$key]);
                    }

                    $this->parents[$key]->appendChild($select);

                    if (isset($this->helpers[$key])) {
                        $this->parents[$key]->appendChild($this->helpers[$key]);
                    }

                    $fields[$key] = $this->parents[$key];
                } else {
                    if (isset($this->labels[$key]) || isset($this->helpers[$key])) {
                        $fieldset = $this->createTag('fieldset');
                        if (isset($this->labels[$key])) {
                            $fieldset->appendChild($this->labels[$key]);
                        }

                        $fieldset->appendChild($select);

                        if (isset($this->helpers[$key])) {
                            $fieldset->appendChild($this->helpers[$key]);
                        }

                        $fields[$key] = $fieldset;
                    } else {
                        $fields[$key] = $select;
                    }
                }
            }
        }
        return $fields;
    }

    /**
     * @param $selected
     * @param array|null $options
     * @param array|null $attributes
     * @return FormSelect
     */
    public function create($selected = null, array $options = null, array $attributes = null): FormSelect
    {
        $opts = [];

        if(!empty($options)) {
            foreach ($options as $value => $item) {
                $attr = [
                    'value' => $value
                ];
                if (!empty($selected) && $selected == $value) {
                    $attr['selected'] = 'selected';
                }
                $opts[] = $this->createTag('option', $item, $attr);
            }
        }

        $this->selects[$this->getIndex()] = $this->tagsIntTag(
            $this->createTag('select', null, $attributes),
            $opts
        );

        return $this;
    }

    /**
     * @param string $tag_name
     * @param string|null $value
     * @param array|null $attributes
     * @return FormSelect
     */
    public function parent(string $tag_name, string $value = null, array $attributes = null): FormSelect
    {
        $this->parents[$this->getIndex()] = $this->createTag($tag_name, $value, $attributes);
        return $this;
    }

    /**
     * @param string|null $value
     * @param array|null $attributes
     * @return FormSelect
     */
    public function label(string $value = null, array $attributes = null): FormSelect
    {
        $this->labels[$this->getIndex()] = $this->createTag('label', $value, $attributes);
        return $this;
    }

    /**
     * @param string $tag_name
     * @param string|null $value
     * @param array|null $attributes
     * @return FormSelect
     */
    public function helper(string $tag_name, string $value = null, array $attributes = null): FormSelect
    {
        if (!empty($value) && preg_match("/<[^<>]+>/", $value)) {// Se a string contiver HTML, criar um fragmento HTML
            $fragment = $this->dom->createDocumentFragment();
            $fragment->appendXML($value);
            $tag = $this->createTag($tag_name, null, $attributes);
            $tag->appendChild($fragment);
        } else {
            $tag = $this->createTag($tag_name, $value, $attributes);
        }

        $this->helpers[$this->getIndex()] = $tag;

        return $this;
    }
}
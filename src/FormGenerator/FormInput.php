<?php declare(strict_types=1);

namespace nortedevbr\eventoprobr\forms\FormGenerator;

use DOMDocument;
use DOMElement;
use nortedevbr\eventoprobr\forms\Traits\Tags;

/**
 *
 */
class FormInput implements FormTagsInterface
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
    private ?array $inputs = null;
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
     * @var array|null
     */
    private ?array $datalists = null;

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
        if (!empty($this->inputs)) {
            foreach ($this->inputs as $key => $input) {
                if (isset($this->parents[$key])) {
                    if (isset($this->labels[$key])) {
                        $this->parents[$key]->appendChild($this->labels[$key]);
                    }

                    $this->parents[$key]->appendChild($input);

                    if (isset($this->datalists[$key])) {
                        $this->parents[$key]->appendChild($this->datalists[$key]);
                    }

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

                        $fieldset->appendChild($input);

                        if (isset($this->datalists[$key])) {
                            $fieldset->appendChild($this->datalists[$key]);
                        }

                        if (isset($this->helpers[$key])) {
                            $fieldset->appendChild($this->helpers[$key]);
                        }

                        $fields[$key] = $fieldset;
                    } else {
                        $fields[$key] = $input;
                    }
                }
            }
        }
        return $fields;
    }

    /**
     * @param array $attributes
     * @return FormInput
     */
    public function text(array $attributes): FormInput
    {
        $this->inputs[$this->getIndex()] = $this->createTag('input', null, $attributes);
        return $this;
    }

    /**
     * @param string $name
     * @param string|null $selected
     * @param string|null $id
     * @param array $options
     * @param array $attributes
     * @param array $attributes_options
     * @return FormInput
     */
    public function options(
        string $name,
        string $selected = null,
        string $id = null,
        array  $options = [],
        array  $attributes = [],
        array  $attributes_options = []
    ): FormInput
    {
        $opts = [];
        foreach ($options as $value => $item) {
            $attributes['id'] = $id . "_" . $item;
            $attributes['value'] = $value;
            $attributes['name'] = $name;

            if ($selected == $value) {
                $attributes['selected'] = '';
            }

            if ($attributes['type'] == 'radio') {
                $attributes['name'] .= $value;
            }

            $opts[] = $this->createTag('input', $value, $attributes);
            $opts[] = $this->createTag('label', $item, ['for' => $attributes['id']]);
        }

        $this->inputs[$this->getIndex()] = $this->tagsIntTag(
            $this->createTag('div', null, $attributes_options), $opts
        );
        return $this;
    }

    public function dataList(FormOptions $options = null): FormInput
    {
        /** @var DOMElement $input */
        if ($input = $this->inputs[$this->getIndex()]) {
            $name = "list" . ucfirst($input->getAttribute('name'));
            $this->inputs[$this->getIndex()]->setAttribute('list', $name);
            $opts = [];
            if (!empty($options)) {
                foreach ($options->data() as $value => $item) {
                    $opts[] = $this->createTag('option', $item, ['value' => $value]);
                }
            }

            $this->datalists[$this->getIndex()] = $this->tagsIntTag(
                $this->createTag('datalist', null, ['id' => $name]),
                $opts
            );
        }

        return $this;
    }

    /**
     * @param string $tag_name
     * @param string|null $value
     * @param array|null $attributes
     * @return FormInput
     */
    public function parent(string $tag_name, string $value = null, array $attributes = null): FormInput
    {
        $this->parents[$this->getIndex()] = $this->createTag($tag_name, $value, $attributes);
        return $this;
    }

    /**
     * @param string|null $value
     * @param array|null $attributes
     * @return FormInput
     */
    public function label(string $value = null, array $attributes = null): FormInput
    {
        $this->labels[$this->getIndex()] = $this->createTag('label', $value, $attributes);
        return $this;
    }

    /**
     * @param string $tag_name
     * @param string|null $value
     * @param array|null $attributes
     * @return FormInput
     */
    public function helper(string $tag_name, string $value = null, array $attributes = null): FormInput
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
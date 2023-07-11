<?php declare(strict_types=1);

namespace nortedevbr\eventoprobr\forms\FormGenerator;

use DOMDocument;
use nortedevbr\eventoprobr\forms\Traits\Tags;

/**
 *
 */
class FormTextArea implements FormTagsInterface
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
    private ?array $textareas = null;
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
        if (!empty($this->textareas)) {
            foreach ($this->textareas as $key => $select) {
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
     * @param string|null $value
     * @param array|null $attributes
     * @return FormTextArea
     */
    public function create(string $value = null, array $attributes = null): FormTextArea
    {
        $this->textareas[$this->getIndex()] = $this->createTag('textarea', $value, $attributes);
        return $this;
    }

    /**
     * @param string $tag_name
     * @param string|null $value
     * @param array|null $attributes
     * @return FormTextArea
     */
    public function parent(string $tag_name, string $value = null, array $attributes = null): FormTextArea
    {
        $this->parents[$this->getIndex()] = $this->createTag($tag_name, $value, $attributes);
        return $this;
    }

    /**
     * @param string|null $value
     * @param array|null $attributes
     * @return FormTextArea
     */
    public function label(string $value = null, array $attributes = null): FormTextArea
    {
        $this->labels[$this->getIndex()] = $this->createTag('label', $value, $attributes);
        return $this;
    }

    /**
     * @param string $tag_name
     * @param string|null $value
     * @param array|null $attributes
     * @return FormTextArea
     */
    public function helper(string $tag_name, string $value = null, array $attributes = null): FormTextArea
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
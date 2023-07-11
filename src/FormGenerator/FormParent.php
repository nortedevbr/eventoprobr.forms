<?php declare(strict_types=1);

namespace nortedevbr\eventoprobr\forms\FormGenerator;

use DOMDocument;
use nortedevbr\eventoprobr\forms\Traits\Tags;

/**
 *
 */
class FormParent
{
    use Tags;

    /**
     * @var DOMDocument
     */
    private DOMDocument $dom;
    /**
     * @var array|null
     */
    private ?array $formParent = null;

    /**
     * @param DOMDocument $dom
     */
    public function __construct(DOMDocument $dom)
    {
        $this->dom = $dom;
    }

    /**
     * @return array|null
     */
    public function getFormParent(): ?array
    {
        return $this->formParent;
    }

    /**
     * @param string $form_parent_tag_name
     * @param string|null $form_parent_value
     * @param array|null $form_parent_attributes
     * @return FormParent
     */
    public function setFormParent(string $form_parent_tag_name, string $form_parent_value = null, array $form_parent_attributes = null): FormParent
    {
        $this->formParent[] = $this->createTag($form_parent_tag_name, $form_parent_value, $form_parent_attributes);
        return $this;
    }
}
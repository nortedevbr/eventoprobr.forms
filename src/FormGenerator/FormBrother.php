<?php declare(strict_types=1);

namespace nortedevbr\eventoprobr\forms\FormGenerator;

use DOMDocument;
use nortedevbr\eventoprobr\forms\Traits\Tags;

/**
 *
 */
class FormBrother
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
    private ?array $formBrother = null;
    /**
     * @var array|null
     */
    private ?array $children = null;

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
    public function getFormBrother(): ?array
    {
        $formBrother = null;
        foreach ($this->formBrother as $index => $itemBrother) {
            if ($children = $this->children[$index]) {
                if ($children->preserve_content) {
                    if ($children->add_first_content) {
                        $itemBrother->insertBefore($children->tag, $itemBrother->firstChild);
                    } else {
                        $itemBrother->appendChild($children->tag);
                    }
                } else {
                    while ($itemBrother->firstChild) {
                        $itemBrother->removeChild($itemBrother->firstChild);
                    }
                    $itemBrother->appendChild($children->tag);
                }

            }
            $formBrother[] = $itemBrother;

        }
        return $formBrother;
    }

    /**
     * @param string $form_brother_tag_name
     * @param string|null $form_brother_value
     * @param array|null $form_brother_attributes
     * @return FormBrother
     */
    public function setFormBrother(string $form_brother_tag_name, string $form_brother_value = null, array $form_brother_attributes = null): FormBrother
    {
        $this->formBrother[] = $this->createTag($form_brother_tag_name, $form_brother_value, $form_brother_attributes);
        $this->index = count($this->formBrother) - 1;
        return $this;
    }

    /**
     * @param string $tag_name
     * @param string|null $value
     * @param array|null $attributes
     * @param bool $preserve_content
     * @param bool $add_first_content
     * @return FormBrother
     */
    public function child(string $tag_name, string $value = null, array $attributes = null, bool $preserve_content = true, bool $add_first_content = true): FormBrother
    {
        if (isset($this->formBrother[$this->index])) {
            $this->children[$this->index] = (object)[
                'add_first_content' => $add_first_content,
                'preserve_content' => $preserve_content,
                'tag' => $this->createTag($tag_name, $value, $attributes)
            ];
        }
        return $this;
    }

    /**
     * @return void
     */
    private function loadHtml()
    {
        //$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    }
}
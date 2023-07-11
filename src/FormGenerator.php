<?php declare(strict_types=1);

namespace nortedevbr\eventoprobr\forms;

use DOMDocument;
use DOMElement;
use DOMException;
use nortedevbr\eventoprobr\forms\FormGenerator\FormBrother;
use nortedevbr\eventoprobr\forms\FormGenerator\FormInput;
use nortedevbr\eventoprobr\forms\FormGenerator\FormOptions;
use nortedevbr\eventoprobr\forms\FormGenerator\FormParent;
use nortedevbr\eventoprobr\forms\FormGenerator\FormSelect;
use nortedevbr\eventoprobr\forms\FormGenerator\FormTextArea;
use nortedevbr\eventoprobr\forms\Traits\Tags;

/**
 *
 */
class FormGenerator
{
    use Tags;

    /**
     * @var array
     */
    private array $form_attributes;
    /**
     * @var DOMDocument
     */
    private DOMDocument $dom;
    /**
     * @var FormParent
     */
    private FormParent $parent;
    /**
     * @var FormBrother
     */
    private FormBrother $brother;
    /**
     * @var FormInput
     */
    private FormInput $input;
    /**
     * @var FormSelect
     */
    private FormSelect $select;
    /**
     * @var FormTextArea
     */
    private FormTextArea $textarea;
    /**
     * @var array|null
     */
    private ?array $errors = null;
    /**
     * @var array|null
     */
    private ?array $hidden_fields = null;
    /**
     * @var int
     */
    private int $index = 0;

    /**
     * @param string|null $form_id
     * @param string|null $form_name
     * @param string|null $form_action
     * @param string|null $form_method
     * @param string|null $form_enctype
     */
    public function __construct(
        string  $form_id = null,
        string  $form_name = null,
        string  $form_action = null,
        ?string $form_method = "post",
        ?string $form_enctype = "multipart/form-data"
    )
    {
        if ($form_id)
            $this->setFormAttributes('id', $form_id);
        if ($form_name)
            $this->setFormAttributes('name', $form_name);
        if ($form_action)
            $this->setFormAttributes('action', $form_action);
        if ($form_method)
            $this->setFormAttributes('method', $form_method);
        if ($form_enctype)
            $this->setFormAttributes('enctype', $form_enctype);

        $this->dom = new DOMDocument('5.0', 'UTF-8');
        $this->parent = new FormParent($this->dom);
        $this->brother = new FormBrother($this->dom);
        $this->input = new FormInput($this->dom);
        $this->select = new FormSelect($this->dom);
        $this->textarea = new FormTextArea($this->dom);
    }

    /**
     * @param string $id
     * @return FormGenerator
     */
    public function setFormId(string $id): FormGenerator
    {
        $this->setFormAttributes('id', $id);
        return $this;
    }

    /**
     * @param string $name
     * @return FormGenerator
     */
    public function setFormName(string $name): FormGenerator
    {
        $this->setFormAttributes('name', $name);
        return $this;
    }

    /**
     * @param string $action
     * @return FormGenerator
     */
    public function setFormAction(string $action): FormGenerator
    {
        $this->setFormAttributes('action', $action);
        return $this;
    }

    /**
     * @param string $method
     * @return FormGenerator
     */
    public function setFormMethod(string $method): FormGenerator
    {
        $this->setFormAttributes('method', $method);
        return $this;
    }

    /**
     * @param string $enctype
     * @return FormGenerator
     */
    public function setFormEnctype(string $enctype): FormGenerator
    {
        $this->setFormAttributes('enctype', $enctype);
        return $this;
    }

    /**
     * @param string $form_parent_tag_name
     * @param string|null $form_parent_value
     * @param array|null $form_parent_attributes
     * @return FormParent
     */
    public function setFormParent(string $form_parent_tag_name, string $form_parent_value = null, array $form_parent_attributes = null): FormParent
    {
        $this->parent->setFormParent($form_parent_tag_name, $form_parent_value, $form_parent_attributes);
        return $this->parent;
    }

    /**
     * @param string $form_brother_tag_name
     * @param string|null $form_brother_value
     * @param array|null $form_brother_attributes
     * @return FormBrother
     */
    public function setFormBrother(string $form_brother_tag_name, string $form_brother_value = null, array $form_brother_attributes = null): FormBrother
    {
        $this->brother->setFormBrother($form_brother_tag_name, $form_brother_value, $form_brother_attributes);
        return $this->brother;
    }


    /**
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getFormAttributes(): array
    {
        return $this->form_attributes;
    }

    /**
     * @param string $key
     * @param string|null $value
     * @return FormGenerator
     */
    public function setFormAttributes(string $key, string $value = null): FormGenerator
    {
        $this->form_attributes[$key] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @param string|null $id
     * @param array|null $attributes
     * @return $this
     */
    public function inputHidden(string $name, string $value, string $id = null, ?array $attributes = []): FormGenerator
    {
        $this->hidden_fields[] = $this->createTag('input', $value, array_merge(
            [
                "type" => "hidden",
                "name" => $name,
                "id" => $id ?? "hidden_" . $name
            ],
            $attributes ?? []
        ));

        return $this;
    }

    /**
     * @param string $name
     * @param string|null $value
     * @param string|null $id
     * @param array|null $attributes
     * @param string $type
     * @return FormInput
     */
    public function input(
        string $name,
        string $value = null,
        string $id = null,
        array  $attributes = null,
        string $type = 'text'
    ): FormInput
    {
        $attributes = array_merge([
            'id' => $id ?? $type . "_" . $name,
            'name' => $name,
            'type' => $type,
            'value' => $value ?? ''
        ], $attributes ?? []);

        $this->index++;
        $this->input->setIndex($this->index);

        $this->input->text($attributes);

        return $this->input;
    }

    /**
     * @param string $name
     * @param string|null $selected
     * @param string|null $id
     * @param array|null $options
     * @param array|null $attributes
     * @param array|null $attributes_options
     * @param string $type
     * @return FormInput
     */
    public function inputOptions(
        string $name,
        string $selected = null,
        string $id = null,
        array  $options = null,
        array  $attributes = null,
        array  $attributes_options = null,
        string $type = 'checkbox'
    ): FormInput
    {
        $attributes = array_merge([
            'type' => $type
        ], $attributes ?? []);

        $this->index++;
        $this->input->setIndex($this->index);

        $this->input->options($name, $selected, $id, $options ?? [], $attributes ?? [], $attributes_options ?? []);

        return $this->input;
    }

    /**
     * @param string $name
     * @param string|null $value
     * @param string|null $id
     * @param array|null $attributes
     * @return FormInput
     */
    public function checkbox(
        string $name,
        string $value = null,
        string $id = null,
        array  $attributes = null
    ): FormInput
    {
        return $this->input($name, $value, $id, $attributes, 'checkbox');
    }

    /**
     * @param string $name
     * @param string|null $selected
     * @param string|null $id
     * @param FormOptions|null $options
     * @param array|null $attributes
     * @param array|null $attributes_options
     * @return FormInput
     */
    public function checkboxOptions(
        string      $name,
        string      $selected = null,
        string      $id = null,
        FormOptions $options = null,
        array       $attributes = null,
        array       $attributes_options = null
    ): FormInput
    {
        return $this->inputOptions($name, $selected, $id, $options ? $options->data() : null, $attributes, $attributes_options, 'checkbox');
    }

    /**
     * @param string $name
     * @param string|null $value
     * @param string|null $id
     * @param array|null $attributes
     * @return FormInput
     */
    public function radio(
        string $name,
        string $value = null,
        string $id = null,
        array  $attributes = null
    ): FormInput
    {
        return $this->input($name, $value, $id, $attributes, 'radio');
    }

    /**
     * @param string $name
     * @param string|null $selected
     * @param string|null $id
     * @param FormOptions|null $options
     * @param array|null $attributes
     * @param array|null $attributes_options
     * @return FormInput
     */
    public function radioOptions(
        string      $name,
        string      $selected = null,
        string      $id = null,
        FormOptions $options = null,
        array       $attributes = null,
        array       $attributes_options = null
    ): FormInput
    {
        return $this->inputOptions($name, $selected, $id, $options ? $options->data() : null, $attributes, $attributes_options, 'radio');
    }

    /**
     * @param string $name
     * @param null $selected
     * @param FormOptions|null $options
     * @param string|null $id
     * @param array|null $attributes
     * @return FormSelect
     */
    public function select(
        string      $name,
                    $selected = null,
        FormOptions $options = null,
        string      $id = null,
        array       $attributes = null
    ): FormSelect
    {
        $attributes = array_merge([
            'id' => $id ?? "sel" . "_" . $name,
            'name' => $name
        ], $attributes ?? []);

        $this->index++;
        $this->select->setIndex($this->index);

        $this->select->create($selected, $options ? $options->data() : null, $attributes);

        return $this->select;
    }

    /**
     * @return FormTextArea
     */
    public function textarea(
        string $name,
        string $value = null,
        string $id = null,
        array  $attributes = null
    ): FormTextArea
    {
        $attributes = array_merge([
            'id' => $id ?? "sel" . "_" . $name,
            'name' => $name
        ], $attributes ?? []);

        $this->index++;
        $this->textarea->setIndex($this->index);

        $this->textarea->create($value, $attributes);

        return $this->textarea;
    }

    /**
     * @param DOMElement $form
     * @return DOMElement
     */
    public function getHiddenFields(DOMElement $form): DOMElement
    {
        return $this->tagsIntTag($form, $this->hidden_fields);
    }

    /**
     * @param DOMElement $form
     * @return DOMElement
     */
    public function getFields(DOMElement $form): DOMElement
    {
        $fields = [];

        foreach ($this->input->getFields() as $k => $item) {
            $fields[$k] = $item;
        }

        foreach ($this->select->getFields() as $k => $item) {
            $fields[$k] = $item;
        }

        foreach ($this->textarea->getFields() as $k => $item) {
            $fields[$k] = $item;
        }

        ksort($fields);

        return $this->tagsIntTag($form, $fields);
    }

    /**
     * @return string|null
     */
    public function show(): ?string
    {
        try {
            $dom = $this->dom;
            //Create Form
            $form = $dom->createElement('form');
            foreach ($this->getFormAttributes() as $key => $value) {
                $form->setAttribute($key, $value ?? "");
            }

            $form = $this->getHiddenFields($form);
            $form = $this->getFields($form);

            $dom = $this->addFormParent($dom, $form);

            return $dom->saveHTML();
        } catch (DOMException $e) {
            $this->errors[] = [
                'method' => 'show',
                'tag_name' => null,
                'error' => $e->getMessage()
            ];
            return null;
        }
    }

    /**
     * @param $dom
     * @param $form
     * @return mixed
     */
    private function addFormParent($dom, $form)
    {
        if ($formParent = $this->parent->getFormParent()) {
            $parent = null;
            $parentIndex = 1;
            $parentTotal = count($formParent);
            foreach ($formParent as $itemParent) {
                if ($parent) {
                    if ($parentIndex >= $parentTotal) {
                        $itemParent = $this->addFormBrother($itemParent);
                        $itemParent->appendChild($form);
                    }
                    $parent->appendChild($itemParent);
                } else {
                    $parent = $itemParent;
                }
                $parentIndex++;
            }
            $dom->appendChild($parent);
        } else {
            $dom = $this->addFormBrother($dom);
            $dom->appendChild($form);
        }
        return $dom;
    }

    /**
     * @param $dom
     * @return mixed
     */
    private function addFormBrother($dom)
    {
        if ($formBrother = $this->brother->getFormBrother()) {
            foreach ($formBrother as $itemBrother) {
                $dom->appendChild($itemBrother);
            }
        }
        return $dom;
    }
}
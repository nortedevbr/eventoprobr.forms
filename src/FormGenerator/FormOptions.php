<?php declare(strict_types=1);

namespace nortedevbr\eventoprobr\forms\FormGenerator;

/**
 *
 */
class FormOptions
{
    /**
     * @var array
     */
    public array $data = [];

    /**
     * @param string|null $key
     * @param string|null $value
     * @param array|null $list
     * @return FormOptions
     */
    public static function byArray(string $key = null, string $value = null, array $list = null): FormOptions
    {
        $options = new static();

        if (!empty($list)) {
            foreach ($list as $item) {
                if ($key && $value) {
                    $options->data[$item[$key]] = $item[$value];
                } elseif (!$key && $value) {
                    $options->data[$item[$value]] = $item[$value];
                } elseif ($key && !$value) {
                    $options->data[$item[$key]] = $item[$key];
                } else {
                    $options->data[$item] = $item;
                }
            }
        }

        return $options;
    }

    /**
     * @param string $key
     * @param string $value
     * @param $list
     * @return FormOptions
     */
    public static function byObject(string $key, string $value, $list = null): FormOptions
    {
        $options = new static();

        if (!empty($list)) {
            foreach ($list as $item) {
                if (isset($item->$key)) {
                    $options->data[$item->$key] = $item->$value ?? "";
                }
            }
        }

        return $options;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }
}
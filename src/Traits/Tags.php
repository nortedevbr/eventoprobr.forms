<?php declare(strict_types=1);

namespace nortedevbr\eventoprobr\forms\Traits;

use DOMElement;

trait Tags
{
    private function createTag(string $tag_name, string $tag_value = null, array $tag_attributes = null): ?DOMElement
    {
        try {
            $tag = $this->dom->createElement($tag_name, $tag_value ?? '');
            if ($tag_attributes) {
                foreach ($tag_attributes as $key => $value) {
                    if (is_numeric($key)) {
                        $tag->setAttribute((string)$value, (string)$value ?? '');
                    } else {
                        $tag->setAttribute((string)$key, (string)$value ?? '');
                    }
                }
            }
            return $tag;
        } catch (\DOMException $e) {
            $this->errors[] = [
                'method' => 'createTag',
                'tag_name' => $tag_name,
                'error' => $e->getMessage()
            ];
            return null;
        }
    }

    private function tagsIntTag(DOMElement $tag, ?array $tags = []): DOMElement
    {
        if (!empty($tags)) {
            foreach ($tags as $item) {
                $tag->appendChild($item);
            }
        }
        return $tag;
    }
}
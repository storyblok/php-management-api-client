<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields;

use Storyblok\ManagementApi\Data\BaseData;

class RichtextField extends BaseData implements FieldValueInterface
{
    /**
     * @param array<mixed> $content
     */
    public function __construct(array $content = [])
    {
        $this->data = [
            "type" => "doc",
            "content" => $content,
        ];
    }

    public static function paragraph(string $text): self
    {
        return (new self())->addParagraph($text);
    }

    /**
     * @return array<mixed>
     */
    public function content(): array
    {
        return $this->getArray("content");
    }

    /**
     * @param array<mixed> $content
     */
    public function setContent(array $content): static
    {
        $this->set("content", $content);
        return $this;
    }

    /**
     * @param array<mixed> $node
     */
    public function addNode(array $node): static
    {
        $content = $this->content();
        $content[] = $node;
        $this->set("content", $content);
        return $this;
    }

    public function addParagraph(string $text): static
    {
        return $this->addNode([
            "type" => "paragraph",
            "content" => [
                [
                    "text" => $text,
                    "type" => "text",
                ],
            ],
        ]);
    }
}

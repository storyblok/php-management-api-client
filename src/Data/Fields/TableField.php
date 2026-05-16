<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data\Fields;

use Storyblok\ManagementApi\Data\BaseData;

class TableField extends BaseData implements FieldValueInterface
{
    /**
     * @param string[] $headers
     * @param array<int, string[]> $rows
     */
    public function __construct(array $headers = [], array $rows = [])
    {
        $this->data = [
            "tbody" => [],
            "thead" => [],
            "fieldtype" => "table",
        ];

        if ($headers !== []) {
            $this->setHeaders($headers);
        }

        if ($rows !== []) {
            $this->setRows($rows);
        }
    }

    /**
     * @param string[] $headers
     * @param array<int, string[]> $rows
     */
    public static function fromRows(array $headers, array $rows): self
    {
        return new self($headers, $rows);
    }

    /**
     * @return array<mixed>
     */
    public function thead(): array
    {
        return $this->getArray("thead");
    }

    /**
     * @param array<mixed> $thead
     */
    public function setThead(array $thead): static
    {
        $this->set("thead", $thead);
        return $this;
    }

    /**
     * @param string[] $headers
     */
    public function setHeaders(array $headers): static
    {
        $this->set(
            "thead",
            array_map(
                fn(string $value): array => $this->makeTableCell("_table_head", $value),
                $headers,
            ),
        );
        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function tbody(): array
    {
        return $this->getArray("tbody");
    }

    /**
     * @param array<mixed> $tbody
     */
    public function setTbody(array $tbody): static
    {
        $this->set("tbody", $tbody);
        return $this;
    }

    /**
     * @param array<int, string[]> $rows
     */
    public function setRows(array $rows): static
    {
        $this->set("tbody", []);
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /**
     * @param string[] $values
     */
    public function addRow(array $values): static
    {
        $rows = $this->tbody();
        $rows[] = [
            "_uid" => $this->makeUid(),
            "component" => "_table_row",
            "body" => array_map(
                fn(string $value): array => $this->makeTableCell("_table_col", $value),
                $values,
            ),
        ];
        $this->set("tbody", $rows);
        return $this;
    }

    /**
     * @return array<string, string>
     */
    private function makeTableCell(string $component, string $value): array
    {
        return [
            "_uid" => $this->makeUid(),
            "component" => $component,
            "value" => $value,
        ];
    }

    private function makeUid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($bytes), 4));
    }
}

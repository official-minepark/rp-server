<?php

namespace minepark\common\configuration;

use minepark\common\utils\DataStructureValidation;
use pocketmine\utils\Filesystem;
use RuntimeException;

class ConfigurationManager
{
    private array $data;

    public function __construct(
        private string $filePath,
        private array  $dataStructure,
        private array  $defaults
    )
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        if (!file_exists($this->filePath)) {
            $this->restoreDefaults();
            return;
        }

        $data = yaml_parse(file_get_contents($this->filePath));

        if (!$data) {
            throw new RuntimeException("Error with parsing yml file at " . $this->filePath);
        }

        $this->data = $data;
        $this->restoreDefaults();
    }

    private function restoreDefaults(): void
    {
        foreach ($this->defaults as $entry => $value) {
            if (!isset($this->data[$entry])) {
                $this->data[$entry] = $value;
            }
        }

        $this->saveData();
    }

    private function saveData(): void
    {
        DataStructureValidation::validateData($this->dataStructure, $this->data);

        Filesystem::safeFilePutContents($this->filePath, yaml_emit($this->data, YAML_UTF8_ENCODING));
    }

    public function getEntry(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }
}
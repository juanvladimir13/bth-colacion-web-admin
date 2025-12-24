<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Core\Http\Models;

use PGDatabase\Postgres;

abstract class Model
{
    protected string $TABLE_NAME = '';
    protected string $ORDER_BY_COLUMNS = 'id';

    protected bool $EDITABLE = false;
    protected bool $SOFT_DELETE = false;
    private array $values = [];

    protected int $id;

    abstract public function setRequest(array $request): void;

    abstract public function getData(): array;

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function save(): array
    {
        $rowsValues = $this->values ? $this->getValues() : $this->getData();
        if ($this->id !== 0) {
            $conditions = ['id' => $this->id];
            if ($this->SOFT_DELETE) {
                $conditions['soft_delete'] = false;
            }

            if ($this->EDITABLE) {
                $conditions['editable'] = true;
            }

            $sw = Postgres::update($this->TABLE_NAME, $rowsValues, $conditions);
            $error = ['error' => Postgres::getError()];
            $data = $this->SOFT_DELETE ?
                $this->find($this->id) : $this->findHard($this->id);
            return $sw && $data ? $data : $error;
        }

        $id = Postgres::insert($this->TABLE_NAME, $rowsValues);
        $error = ['error' => Postgres::getError()];
        $data = $this->SOFT_DELETE ?
            $this->find($id) : $this->findHard($id);
        return ($id !== 0 && $data) ? $data : $error;
    }

    public function saveById(int $id, string $columnName, array $values = []): array
    {
        $rowsValues = $values ?? $this->getData();
        if ($id !== 0) {
            $conditions = [$columnName => $id];
            if ($this->SOFT_DELETE) {
                $conditions['soft_delete'] = false;
            }

            if ($this->EDITABLE) {
                $conditions['editable'] = true;
            }

            $sw = Postgres::update($this->TABLE_NAME, $rowsValues, $conditions);
            $error = ['error' => Postgres::getError()];
            $data = $this->SOFT_DELETE ?
                $this->find($id, $columnName) : $this->findHard($id, $columnName);
            return $sw && $data ? $data : $error;
        }

        $idProccess = Postgres::insert($this->TABLE_NAME, $rowsValues);
        $error = ['error' => Postgres::getError()];
        $data = $this->SOFT_DELETE ?
            $this->find($id, $columnName) : $this->findHard($id, $columnName);
        return $idProccess !== 0 && $data ? $data : $error;
    }

    public function saveHard(): array
    {
        $rowsValues = $this->values ? $this->getValues() : $this->getData();
        if ($this->id !== 0) {
            $sw = Postgres::update($this->TABLE_NAME, $rowsValues, ['id' => $this->id]);
            return $sw ? $this->findHard($this->id) : ['error' => Postgres::getError()];
        }

        $id = Postgres::insert($this->TABLE_NAME, $rowsValues);
        return $id !== 0 ? $this->findHard($id) : ['error' => Postgres::getError()];
    }

    public function update(array $setValues, array $whereValues, $findValueId, string $findColumnName = 'id'): array
    {
        $conditions = $whereValues;

        if ($this->SOFT_DELETE) {
            $conditions['soft_delete'] = false;
        }

        if ($this->EDITABLE) {
            $conditions['editable'] = true;
        }

        $sw = Postgres::update(
            $this->TABLE_NAME,
            $setValues,
            $conditions
        );

        $error = ['error' => Postgres::getError()];
        $data = $this->SOFT_DELETE ?
            $this->find($findValueId, $findColumnName) : $this->findHard($findValueId, $findColumnName);
        return $sw && $data ? $data : $error;
    }

    public function restore(int $id): bool
    {
        if (!$this->SOFT_DELETE) {
            return false;
        }

        return Postgres::update($this->TABLE_NAME, ['soft_delete' => false], ['id' => $id, 'soft_delete' => true]);
    }

    public function onEditable(int $id): bool
    {
        if (!$this->EDITABLE) {
            return false;
        }

        $conditions = ['id' => $id, 'editable' => false];
        if ($this->SOFT_DELETE) {
            $conditions['soft_delete'] = false;
        }

        return Postgres::update($this->TABLE_NAME, ['editable' => true], $conditions);
    }

    public function offEditable(int $id): bool
    {
        if (!$this->EDITABLE) {
            return false;
        }

        $conditions = ['id' => $id, 'editable' => true];
        if ($this->SOFT_DELETE) {
            $conditions['soft_delete'] = false;
        }

        return Postgres::update($this->TABLE_NAME, ['editable' => false], $conditions);
    }

    public function delete(int $id): bool
    {
        if (!$this->SOFT_DELETE) {
            return false;
        }

        return Postgres::update($this->TABLE_NAME, ['soft_delete' => true], ['id' => $id, 'soft_delete' => false]);
    }

    public function hardDelete(int $id): bool
    {
        return Postgres::delete($this->TABLE_NAME, ['id' => $id]);
    }

    public function find($value, string $column = 'id'): array
    {
        if (!$this->SOFT_DELETE) {
            return [];
        }

        $query = sprintf('SELECT * FROM %s WHERE %s=$1 and soft_delete is false;', $this->TABLE_NAME, $column);
        $rows = Postgres::fetchAllParams($query, [$value]);
        return $rows ? $rows[0] : [];
    }

    public function findHard($value, string $column = 'id'): array
    {
        $query = sprintf('SELECT * FROM %s WHERE %s=$1;', $this->TABLE_NAME, $column);
        $rows = Postgres::fetchAllParams($query, [$value]);
        return $rows ? $rows[0] : [];
    }

    public function findAll(): array
    {
        if (!$this->SOFT_DELETE) {
            return [];
        }

        $query = sprintf('SELECT * FROM %s WHERE soft_delete is false ORDER BY %s;', $this->TABLE_NAME, $this->ORDER_BY_COLUMNS);
        return Postgres::fetchAll($query);
    }

    public function findAllHard(): array
    {
        $query = sprintf('SELECT * FROM %s ORDER BY %s;', $this->TABLE_NAME, $this->ORDER_BY_COLUMNS);
        return Postgres::fetchAll($query);
    }

    public static function extractDataValues(array $attributes, array $request): array
    {
        $newValues = [];

        foreach ($attributes as $key => $options) {
            $dataType = $options['datatype'] ?? DataType::STRING;
            $defaultValue = $options['default'] ?? null;

            $rawValue = $request[$key] ?? $defaultValue;

            $newValues[$key] = match ($dataType) {
                DataType::INT => is_numeric($rawValue) ? (int)$rawValue : (int)$defaultValue,
                DataType::FLOAT => is_numeric($rawValue) ? (float)$rawValue : (float)$defaultValue,
                DataType::BOOL => filter_var($rawValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (bool)$defaultValue,
                DataType::STRING => is_string($rawValue) ? trim($rawValue) : (string)$defaultValue,
                DataType::STRING_UPPER => is_string($rawValue) ? strtoupper(trim($rawValue)) : (string)$defaultValue,
                DataType::UTF8 => is_string($rawValue) ? mb_convert_encoding($rawValue, 'UTF-8') : mb_convert_encoding((string)$defaultValue, 'UTF-8'),
                DataType::UTF8_UPPER => is_string($rawValue) ? mb_strtoupper($rawValue, 'UTF-8') : mb_strtoupper((string)$defaultValue, 'UTF-8'),
                default => (string)$rawValue,
            };
        }

        return $newValues;
    }
}

<?php

namespace App\Core;

/**
 * Input Validator
 * Validates request data against a set of rules.
 */
class Validator
{
    private array $errors = [];
    private array $data = [];

    public function __construct(private array $rules = [])
    {
    }

    /**
     * Validate data
     */
    public function validate(array $data): bool
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            $rules = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $data[$field] ?? null;
            $label = ucfirst(str_replace('_', ' ', $field));

            foreach ($rules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $method = 'rule' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    $error = $this->$method($field, $value, $label, $params);
                    if ($error) {
                        $this->errors[$field][] = $error;
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error for a field
     */
    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Check if field has error
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    // --- Validation Rules ---

    private function ruleRequired(string $field, mixed $value, string $label, array $params): ?string
    {
        if ($value === null || $value === '' || $value === []) {
            return "{$label} wajib diisi.";
        }
        return null;
    }

    private function ruleEmail(string $field, mixed $value, string $label, array $params): ?string
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "{$label} harus berupa email yang valid.";
        }
        return null;
    }

    private function ruleMin(string $field, mixed $value, string $label, array $params): ?string
    {
        $min = (int) ($params[0] ?? 0);
        if (!empty($value) && strlen($value) < $min) {
            return "{$label} minimal {$min} karakter.";
        }
        return null;
    }

    private function ruleMax(string $field, mixed $value, string $label, array $params): ?string
    {
        $max = (int) ($params[0] ?? 255);
        if (!empty($value) && strlen($value) > $max) {
            return "{$label} maksimal {$max} karakter.";
        }
        return null;
    }

    private function ruleUnique(string $field, mixed $value, string $label, array $params): ?string
    {
        if (empty($value)) return null;
        
        $table = $params[0] ?? '';
        $column = $params[1] ?? $field;
        $exceptId = $params[2] ?? null;

        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
        $sqlParams = [$value];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $sqlParams[] = $exceptId;
        }

        $count = (int) $db->fetchColumn($sql, $sqlParams);
        if ($count > 0) {
            return "{$label} sudah digunakan.";
        }
        return null;
    }

    private function ruleConfirmed(string $field, mixed $value, string $label, array $params): ?string
    {
        $confirmField = $field . '_confirmation';
        $confirmValue = $this->data[$confirmField] ?? null;
        
        if (!empty($value) && $value !== $confirmValue) {
            return "Konfirmasi {$label} tidak cocok.";
        }
        return null;
    }

    private function ruleNumeric(string $field, mixed $value, string $label, array $params): ?string
    {
        if (!empty($value) && !is_numeric($value)) {
            return "{$label} harus berupa angka.";
        }
        return null;
    }

    private function ruleIn(string $field, mixed $value, string $label, array $params): ?string
    {
        if (!empty($value) && !in_array($value, $params)) {
            return "{$label} tidak valid.";
        }
        return null;
    }
}

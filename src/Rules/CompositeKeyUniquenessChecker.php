<?php

namespace TwenyCode\LaravelBlueprint\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class CompositeKeyUniquenessChecker implements ValidationRule
{

    private string $tableName;
    private array $compositeColsKeyValue = [];
    private $rowId;

    //  Create a new rule instance
    public function __construct(string $tableName, array $compositeColsKeyValue, $rowId = null)
    {
        $this->tableName = $tableName;
        $this->compositeColsKeyValue = $compositeColsKeyValue;
        $this->rowId = $rowId;
    }


    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
       $passes = true;

        if($this->rowId) {
            $record = DB::table($this->tableName)->where($this->compositeColsKeyValue)->first();
            $passes = !$record || ($record && $record->id == $this->rowId);
        }
        else {
            $passes = !DB::table($this->tableName)->where($this->compositeColsKeyValue)->exists();
        }

        if(!$passes) {
            $fail($this->errorMessage());
        }

    }

    private function errorMessage()
    {
        $colNames = '';

        foreach ($this->compositeColsKeyValue as $col => $value) {
            $colNames .= $col . ', ';
        }
        $colNames = str_replace('_id','',rtrim($colNames, ','));

        return "The combination of $colNames must be unique.";

    }
}

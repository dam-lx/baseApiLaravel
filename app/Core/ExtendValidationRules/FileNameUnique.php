<?php
namespace App\Core\ExtendValidationRules;

use App\Core\Dao\SDB;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileNameUnique implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $table;
    protected $field;
    protected $tableWait;
    public function __construct($table,$field,$tableWait = null)
    {
        $this->table = $table;
        $this->field = $field;
        $this->tableWait = $tableWait;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        Log::debug($attribute);
        $countPdfName = SDB::table($this->table)->where($this->field, $value->getClientOriginalName())->count();
        if($this->tableWait != null){
            $countPdfNameWait = SDB::table($this->tableWait)->where($this->field, $value->getClientOriginalName())->count();
            if($countPdfNameWait >= 1){
                return false;
            }
        }
        if ($countPdfName >= 1) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.file_name_unique');
    }
}

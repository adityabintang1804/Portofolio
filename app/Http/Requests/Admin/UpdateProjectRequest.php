<?php

namespace App\Http\Requests\Admin;

class UpdateProjectRequest extends StoreProjectRequest
{
    public function rules(): array
    {
        return $this->projectRules($this->route('project')->id);
    }
}

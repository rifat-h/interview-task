<?php

namespace App\Models;

use App\Helper\FormBuilder\FormBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiteSetting extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function EditForm()
    {
        return FormBuilder::build([
            [
                'type' => 'text',
                'col' => 6,
                'name' => 'website_name',
                'id' => 'website_name',
                'label' => 'Website Name',
                'value' => $this->website_name,
                'placeholder' => 'Enter Site Name',
                'required' => true,
            ],
            [
                'type' => 'file',
                'col' => 6,
                'label' => 'Site Logo',
                'name' => 'site_logo',
                'id' => 'site_logo',
                'required' => false,
            ]
        ]);
        
    }
}

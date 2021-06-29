<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MediaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MediaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MediaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Media::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/media');
        CRUD::setEntityNameStrings('media', 'medias');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('type');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $media_type= [
            "App\Model\Audio" => "Audio",
            "App\Model\Text"  => "Text",
            "App\Model\Video" => "Video",
        ];

        CRUD::setValidation(MediaRequest::class);

        $this->crud->addField([
            'name'  => 'name',
            'label' => 'Nom du media',
            'type'  => 'text',
        ]);

        $this->crud->addField([
            'name'  => 'type',
            'label'   => 'Type du Media',
            'type'    => 'select_from_array',
            'options' => $media_type,
            'default' => 'App\Model\Text',
        ]);

        $this->crud->addField([
            'name'  => 'url',
            'label' => 'Upload un fichier',
            'type'  => 'upload',
            'tab'     => 'Fichier',
        ]);

        $this->crud->addField([   // CKEditor
            'name'          => 'content',
            'label'         => 'Content',
            'type'          => 'ckeditor',
            'tab'           => 'Text',
        ]);

        $this->crud->addField(
            [  // Select
                'label'     => "Oeuver",
                'type'      => 'select',
                'name'      => 'artwork_id', // the db column for the foreign key

                // optional
                // 'entity' should point to the method that defines the relationship in your Model
                // defining entity will make Backpack guess 'model' and 'attribute'
                'entity'    => 'artwork',

                // optional - manually specify the related model and attribute
                'model'     => "App\Models\Artwork", // related model
                'attribute' => 'name', // foreign key attribute that is shown to user

                // optional - force the related options to be a custom query, instead of all();
                'options'   => (function ($query) {
                    return $query->orderBy('name', 'ASC')->get();
                }), //  you can use this to filter the results show in the select
            ],
        );

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function setImageAttribute($value)
    {
        $attribute_name = "url";
        $disk = "public";
        $destination_path = "image/";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);

        // return $this->attributes[{$attribute_name}]; // uncomment if this is a translatable field
    }
}

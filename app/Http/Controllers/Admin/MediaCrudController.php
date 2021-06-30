<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MediaRequest;
use App\Models\Artwork;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;

/**
 * Class MediaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MediaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate;}
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
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
        $this->crud->allowAccess('reorder');
        $this->crud->enableReorder('name', 1);

        $this->crud->addFilter([
            'name'  => 'artwork',
            'type'  => 'select2',
            'label' => 'Artwork'
        ], function () {
            return Artwork::all()->keyBy('id')->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('select', 'medias.*');
            $this->crud->addClause('where', 'artwork_id', $value);
        });
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
        //CRUD::column('lft');

        $this->crud->removeButton('reorder');
    }

    protected function setupReorderOperation()
    {
        $this->crud->set('reorder.label', 'name');
        $this->crud->set('reorder.max_level', 1);
        $this->crud->set('reorder.filters', $this->crud->filters());
    }

    /**
     * Save the new order, using the Nested Set pattern.
     *
     * Database columns needed: id, lft, name/title
     *
     * @return
     */
    public function saveReorder()
    {
        $this->crud->hasAccessOrFail('reorder');

        $all_entries = \Request::input('tree');

        if (count($all_entries)) {
            $count = $this->updateTreeOrder($all_entries);
        } else {
            return false;
        }

        return 'success for '.$count.' items';
    }

    protected function updateTreeOrder($request)
    {
        $count = 0;

        foreach ($request as $key => $entry) {
            if ($entry['item_id'] != '' && $entry['item_id'] != null) {
                $item = $this->crud->model->find($entry['item_id']);
                $item->lft = $key;
                $item->save();

                $count++;
            }
        }

        return $count;
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
            "App\Model\Image"  => "Image",
            "App\Model\Text"  => "Text",
            "App\Model\Video" => "Video",
        ];

        CRUD::setValidation(MediaRequest::class);

        $this->crud->addField([
            'name'  => 'name',
            'label' => 'Nom du media',
            'type'  => 'text',
            'attributes' => [
                'required' => true,
            ]
        ]);

        $this->crud->addField([
            'name'  => 'type',
            'label'   => 'Type du Media',
            'type'    => 'select_from_array',
            'options' => $media_type,
            'default' => 'App\Model\Text',
            'attributes' => [
                'required' => true,
            ]
        ]);

        $this->crud->addField([
            'name'  => 'url',
            'label' => 'Upload un fichier',
            'type'  => 'upload',
            'tab'     => 'Fichier',
            'upload'    => true,
            'disk'      => 'public',
        ]);

        $this->crud->addField([   // CKEditor
            'name'          => 'content',
            'label'         => 'Content',
            'type'          => 'ckeditor',
            'tab'           => 'Text',
        ]);

        $this->crud->addField(
            [  // Select
                'label'     => "Oeuvre",
                'type'      => 'select',
                'name'      => 'artwork_id', // the db column for the foreign key
                // optional - manually specify the related model and attribute
                'model'     => "App\Models\Artwork", // related model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'attributes' => [
                    'required' => true,
                ]
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

    public function destroy($id)
    {
        $media = Media::where('id', $id)->first();
        if (isset($media->url)) {
            Storage::disk('public')->delete($media->url);
        }

        return $this->crud->delete($id);
    }

    public function update()
    {
        $response = $this->traitUpdate();
        if (isset($response->url)) {
            Storage::disk('public')->delete($response->url);
        }

        return $response;
    }
}

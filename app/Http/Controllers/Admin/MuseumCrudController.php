<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MuseumRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MuseumCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MuseumCrudController extends CrudController
{
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
        CRUD::setModel(\App\Models\Museum::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/museum');
        CRUD::setEntityNameStrings('museum', 'museums');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MuseumRequest::class);

        CRUD::field('name');
        $this->crud->addField(
            [   // Address algolia
                'name'          => 'adress',
                'label'         => 'Address',
                'type'          => 'address_algolia',
                // optional
                'store_as_json' => true,
                'attributes' => [
                    'required' => true,
                ]
            ],
        );

        $this->crud->addField([
            'label' => "Profile Image",
            'name' => "logo",
            'type' => 'image',
            'crop' => true, // set to true to allow cropping, false to disable
            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
            // 'disk'      => 's3_bucket', // in case you need to show images from a different disk
            // 'prefix'    => 'uploads/images/profile_pictures/' // in case your db value is only the file name (no path), you can use this to prepend your path to the image src (in HTML), before it's shown to the user;
        ]);

        $this->crud->addField(
            [   // Textarea
                'name'  => 'description',
                'label' => 'Description',
                'type'  => 'textarea',
                'attributes' => [
                    'required' => true,
                ]
            ],
        );

        $this->crud->addField([
            'name'  => 'qrCodeSize',
            'label' => 'Taille du QR Code (pixel)',
            'type'  => 'number',
            'tab' => 'Settings',
            //optional
            'attributes' => [
                'required' => true,
            ]
        ]);



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
}

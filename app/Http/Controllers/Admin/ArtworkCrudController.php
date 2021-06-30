<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ArtworkRequest;
use Illuminate\Http\Response;
use App\Models\Artwork;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Class ArtworkCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ArtworkCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Artwork::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/artwork');
        CRUD::setEntityNameStrings('artwork', 'artworks');
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

        $this->crud->query->withCount('medias');
        $this->crud->addColumn([
            'name'      => 'medias_count', // name of relationship method in the model
            'type'      => 'text',
            'label'     => 'Nombre de média', // Table column heading
        ]);

        $this->crud->addButtonFromView('line', 'generate', 'generate', 'beginning');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ArtworkRequest::class);

        $this->crud->addField([
            'name'  => 'name',
            'label' => 'Nom de l\'oeuvre',
            'type'  => 'text',
            'attributes' => [
                'required' => true,
            ]
        ]);
        CRUD::field('metadata');

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

    public function generate ($id) {
        $artwork = Artwork::where('id', $id)->first();
        $qrcode = QrCode::format('svg')->size(200)->generate($artwork->name);
        $headers = array(
            'Content-Type: image/svg',
        );

        Storage::disk('public')->put('qrcode.svg', $qrcode);

        return response()->download(Storage::disk('public')->path('qrcode.svg'), "$artwork->name-qrcode.svg", $headers)->deleteFileAfterSend(true);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ArtworkRequest;
use App\Models\Museum;
use http\Env\Request;
use Illuminate\Http\Response;
use App\Models\Artwork;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
use Backpack\CRUD\app\Library\Widget;


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
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
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
        $this->crud->allowAccess('reorder');
        $this->crud->enableReorder('name', 1);
        $this->crud->orderBy('lft');

        $this->crud->addButtonFromView('line', 'generate', 'generate', 'beginning');
        $this->crud->addButtonFromView('line', 'media_reorder', 'reorder', 'beginning');


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
            'label'     => 'Nombre de m??dia', // Table column heading
        ]);

        $this->crud->removeButton('reorder');

        $this->crud->addColumn([
            'name' => 'viewNumber',
            'type'=> 'text',
            'label' =>'nombre de vue']);


    }

    protected function setupShowOperation()
    {
        $widget_definition_array= [
            'type'     => 'view',
            'view'     => 'iframe',
            'content'  => 'test',
        ];

        Widget::add($widget_definition_array)->to('after_content');
    }

    protected function setupReorderOperation()
    {
        $this->crud->set('reorder.label', 'name');
        $this->crud->set('reorder.max_level', 1);
    }



    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $colors = [
            "white" => "blanc",
            "black"  => "noir",
            "blue"  => "bleu",
            "red" => "rouge",
            "green" => "vert",
            "orange" => "orange",
            "purple" => "violet",
        ];

        CRUD::setValidation(ArtworkRequest::class);

        $this->crud->addField([
            'name'  => 'name',
            'label' => 'Nom de l\'oeuvre',
            'type'  => 'text',
            'attributes' => [
                'required' => true,
            ]
        ]);



        $this->crud->addField([
            'name'  => 'color',
            'label' => 'Couleur th??me de l\'oeuvre',
            'type'    => 'select_from_array',
            'options' => $colors,
            'default' => 'white',
            'attributes' => [
                'required' => true,
            ]
        ]);


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
        $qrCodeSize = Museum::first()->qrCodeSize ? Museum::first()->qrCodeSize : 200;
        $qrcode = QrCode::format('svg')->size($qrCodeSize)->generate(route('artwork.index', [ 'id' => $id]));
        $headers = array(
            'Content-Type: image/svg',
        );

        Storage::disk('public')->put('qrcode.svg', $qrcode);

        return response()->download(Storage::disk('public')->path('qrcode.svg'), "$artwork->name-qrcode.svg", $headers)->deleteFileAfterSend(true);
    }

    public function preview ($id) {

        header("Location: http://www.museaum.app/artwork/".$id);
    }

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

    /**
     * Change the order and parents of the given elements, according to the NestedSortable AJAX call.
     *
     * @param  [Request] The entire request from the NestedSortable AJAX Call.
     * @return [integer] The number of items whose position in the tree has been changed.
     */
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
}

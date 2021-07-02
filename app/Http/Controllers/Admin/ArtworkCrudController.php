<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ArtworkRequest;
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

    public $qrCodeSize = 200;

    public function setQrCodeSize(Request $request): void
    {
        $data = $request->getForm();
        //$this->qrCodeSize = $qrCodeSize;

        redirect(backpack_url('artwork'));
    }
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

        $widget_qrcode_array = [
            'type'       => 'card',
            'wrapper' => ['class' => 'col-sm-3 col-md-4 mx-n3'], // optional
            // 'class'   => 'card bg-dark text-white', // optional
            'content'    => [
                'header' => 'Set QR Code size', // optional
                'body'   => '<form action="'.backpack_url("artwork/qrcodesize").'" method="post" class="mt-3"> <input class="form-control" value="'.$this->qrCodeSize.'" type="number" name="size"> <button class="mt-2 btn btn-primary w-100">Set</button></form>',
            ]
        ];

        Widget::add($widget_qrcode_array)->to('after_content');
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

        $this->crud->removeButton('reorder');
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
        $qrcode = QrCode::format('svg')->size($this->qrCodeSize)->generate($artwork->name);
        $headers = array(
            'Content-Type: image/svg',
        );

        Storage::disk('public')->put('qrcode.svg', $qrcode);

        return response()->download(Storage::disk('public')->path('qrcode.svg'), "$artwork->name-qrcode.svg", $headers)->deleteFileAfterSend(true);
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

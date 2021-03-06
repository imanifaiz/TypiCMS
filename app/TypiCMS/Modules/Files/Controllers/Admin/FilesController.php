<?php
namespace TypiCMS\Modules\Files\Controllers\Admin;

use View;
use Input;
use Request;
use Redirect;
use Response;
use Paginator;
use Notification;

use TypiCMS\Modules\Files\Repositories\FileInterface;
use TypiCMS\Modules\Files\Services\Form\FileForm;

// Presenter
use TypiCMS\Presenters\Presenter;
use TypiCMS\Modules\Files\Presenters\FilePresenter;

// Base controller
use TypiCMS\Controllers\BaseController;

class FilesController extends BaseController
{

    public function __construct(FileInterface $file, FileForm $fileform, Presenter $presenter)
    {
        parent::__construct($file, $fileform, $presenter);
        $this->title['parent'] = trans_choice('files::global.files', 2);
    }

    /**
     * List models
     * GET /admin/model
     */
    public function index($parent = null)
    {
        $page = Input::get('page');

        if ($parent) {
            $itemsPerPage = 100;
            $data = $this->repository->byPageFrom($page, $itemsPerPage, $parent, array('translations'), true);
        } else {
            $itemsPerPage = 10;
            $data = $this->repository->byPage($page, $itemsPerPage, array('translations'), true);
        }
        $models = Paginator::make($data->items, $data->totalItems, $itemsPerPage);

        $models = $this->presenter->paginator($models, new FilePresenter);

        if ($parent) {
            $this->layout->content = View::make('files.admin.index')
                ->withModels($models)
                ->withParent($parent);
        } else {
            $this->layout->content = View::make('files.admin.all')
                ->withModels($models);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($parent)
    {
        $model = $this->repository->getModel();
        $this->title['child'] = trans('files::global.New');
        $this->layout->content = View::make('files.admin.create')
            ->withModel($model)
            ->withParent($parent);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int      $id
     * @return Response
     */
    public function edit($parent, $model)
    {
        $this->title['child'] = trans('files::global.Edit');
        $this->layout->content = View::make('files.admin.edit')
            ->withModel($model)
            ->withParent($parent);
    }

    /**
     * Show resource.
     *
     * @param  int      $id
     * @return Response
     */
    public function show($parent, $model)
    {
        return Redirect::route('admin.' . $parent->route . '.files.edit', array($parent->id, $model->id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($parent)
    {

        if ($model = $this->form->save(Input::all())) {
            if (Request::ajax()) {
                echo json_encode(array('id' => $model->id));
                exit();
            }

            return (Input::get('exit')) ? Redirect::route('admin.' . $parent->route . '.files.index', $parent->id) : Redirect::route('admin.' . $parent->route . '.files.edit', array($parent->id, $model->id)) ;
        }

        if (Request::ajax()) {
            return Response::json('error', 400);
        }

        return Redirect::route('admin.' . $parent->route . '.files.create', $parent->id)
            ->withInput()
            ->withErrors($this->form->errors());

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int      $id
     * @return Response
     */
    public function update($parent, $model)
    {

        Request::ajax() and exit($this->repository->update(Input::all()));

        if ($this->form->update(Input::all())) {
            return (Input::get('exit')) ? Redirect::route('admin.' . $parent->route . '.files.index', $parent->id) : Redirect::route('admin.' . $parent->route . '.files.edit', array($parent->id, $model->id)) ;
        }

        return Redirect::route('admin.' . $parent->route . '.files.edit', array($parent->id, $model->id))
            ->withInput()
            ->withErrors($this->form->errors());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int      $id
     * @return Response
     */
    public function sort()
    {
        $sort = $this->repository->sort(Input::all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int      $id
     * @return Response
     */
    public function destroy($parent, $model)
    {
        if ($this->repository->delete($model)) {
            if (! Request::ajax()) {
                Notification::success('File '.$model->filename.' deleted.');

                return Redirect::back();
            }
        } else {
            Notification::error('Error deleting file '.$model->filename.'.');
        }
    }
}

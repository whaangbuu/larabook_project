<?php

use Larabook\Forms\PublishStatusForm;
use Larabook\Statuses\PublishStatusCommand;
use Larabook\Statuses\StatusRepository;

class StatusesController extends \BaseController {

    /**
     * @var StatusRepository
     */
    protected $statusRepository;

    /**
     * @var PublishStatusForm
     */
    protected $publishStatusForm;

    /**
     * @param PublishStatusForm $publishStatusForm
     * @param StatusRepository $statusRepository
     */
    function __construct(PublishStatusForm $publishStatusForm, StatusRepository $statusRepository)
    {
        $this->publishStatusForm = $publishStatusForm;
        $this->statusRepository = $statusRepository;

        $this->beforeFilter('auth');
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

        $statuses = $this->statusRepository->getFeedForUser(Auth::user());
        return View::make('statuses.index', compact('statuses'));
		
	}

	/**
	 * Save a new status
	 *
	 * @return Response
	 */
	public function store()
	{
		$pusher = new Pusher('db44e6f643901a1da9b8', '9366930f5d8629fc6bb5', '117191');

        $input = array_add(Input::get(), 'userId', Auth::id());

        $this->publishStatusForm->validate($input);

        $this->execute(PublishStatusCommand::class, $input);

        $pusher->trigger('larabook', 'StatusWasPublished', $input);

        Flash::message('Your status has been updated!');

        return Redirect::back();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

}

<?php

namespace OzSpy\Http\Controllers\API\Models\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OzSpy\Http\Controllers\Controller;
use OzSpy\Models\Auth\User;
use OzSpy\Services\Entities\User\DestroyService;
use OzSpy\Services\Entities\User\LoadService;
use OzSpy\Services\Entities\User\StoreService;
use OzSpy\Services\Entities\User\UpdateService;

class UsersController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @param LoadService $loadService
     * @return \Illuminate\Http\Response
     */
    public function index(LoadService $loadService)
    {
        $data = $loadService->handle();
        return new Response($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param StoreService $storeService
     * @return Response
     */
    public function store(Request $request, StoreService $storeService)
    {
        $user = $storeService->handle($request->all());
        $data = $user;
        return new Response(compact(['data']));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Response
     */
    public function show(User $user)
    {
        $data = $user;
        return new Response(compact(['data']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @param UpdateService $updateService
     * @return Response
     */
    public function update(Request $request, User $user, UpdateService $updateService)
    {
        $result = $updateService->handle($user, $request->all());
        if ($result === true) {
            return new Response(null, 204);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @param DestroyService $destroyService
     * @return Response
     */
    public function destroy(User $user, DestroyService $destroyService)
    {
        $result = $destroyService->handle($user);
        if ($result === true) {
            return new Response(null, 204);
        }
    }
}

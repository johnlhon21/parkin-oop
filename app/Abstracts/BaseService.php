<?php


namespace App\Abstracts;


use App\Services\Security\Token;
use Illuminate\Http\Request;

abstract class BaseService
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function response(): Response
    {
        return (new Response());
    }
}

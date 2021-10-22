<?php

namespace TotalCRM\TinkoffAcquiring\Core\Http;

interface Client
{
    /**
     * @param Request $req
     * @return Response
     */
    public function send(Request $req): Response;
}
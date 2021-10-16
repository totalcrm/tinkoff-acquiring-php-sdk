<?php

namespace TotalCRM\TinkoffAcquiring\Core\Http;

interface Client {

	function send(Request $req): Response;

}
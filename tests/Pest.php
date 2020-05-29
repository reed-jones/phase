<?php

use Phased\Tests\Routing\TestCase as RoutingTestCase;
use Phased\Tests\State\TestCase as StateTestCase;

uses(RoutingTestCase::class)->group('routing')->in('Phased/Routing');
uses(StateTestCase::class)->group('state')->in('Phased/State');

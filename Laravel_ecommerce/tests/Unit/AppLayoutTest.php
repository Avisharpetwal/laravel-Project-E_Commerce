<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\View\Components\AppLayout;
use Illuminate\Support\Facades\View;

class AppLayoutTest extends TestCase
{
    
    public function test_it_returns_the_correct_view()
    {
        $component = new AppLayout();

        $view = $component->render();

        $this->assertEquals('layouts.app', $view->name());
    }

    
    public function test_it_renders_without_errors()
    {
        $component = new AppLayout();

        $html = $component->render()->with([])->render();

        $this->assertStringContainsString('<!DOCTYPE html>', $html); // assuming layouts.app starts with HTML
    }
}

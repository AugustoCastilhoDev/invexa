<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_renders_and_reflects_fourteen_day_trial_policy()
    {
        $response = $this->get(route('landing'));

        $response->assertOk();
        $response->assertSee('14 dias');
        $response->assertSee('Pro');
        $response->assertSee('Business');

        // Sem plano free permanente: assinatura passa a ser obrigatória após o trial.
        $response->assertDontSee('para sempre gratuito');
        $response->assertDontSee('Criar conta gratuita');
        $response->assertDontSee('30 dias');
    }
}

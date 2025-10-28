<?php

namespace Tests\Feature\Http\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test about page loads successfully.
     *
     * @return void
     */
    public function test_about_page_loads()
    {
        $this->get(route('about.view'))
            ->assertStatus(200)
            ->assertSeeLivewire('about-page')
            ->assertSee('About');
    }

    /**
     * Test contact page loads successfully.
     *
     * @return void
     */
    public function test_contact_page_loads()
    {
        $this->get(route('contact.view'))
            ->assertStatus(200)
            ->assertSeeLivewire('contact-page')
            ->assertSee('Contact Us');
    }

    /**
     * Test shipping returns page loads successfully.
     *
     * @return void
     */
    public function test_shipping_returns_page_loads()
    {
        $this->get(route('shipping-returns.view'))
            ->assertStatus(200)
            ->assertSeeLivewire('shipping-returns-page')
            ->assertSee('Shipping')
            ->assertSee('Returns');
    }

    /**
     * Test size guide page loads successfully.
     *
     * @return void
     */
    public function test_size_guide_page_loads()
    {
        $this->get(route('size-guide.view'))
            ->assertStatus(200)
            ->assertSeeLivewire('size-guide-page')
            ->assertSee('Size Guide');
    }

    /**
     * Test faq page loads successfully.
     *
     * @return void
     */
    public function test_faq_page_loads()
    {
        $this->get(route('faq.view'))
            ->assertStatus(200)
            ->assertSeeLivewire('faq-page')
            ->assertSee('Frequently Asked Questions');
    }

    /**
     * Test cookies page loads successfully.
     *
     * @return void
     */
    public function test_cookies_page_loads()
    {
        $this->get(route('cookies.view'))
            ->assertStatus(200)
            ->assertSeeLivewire('cookies-page')
            ->assertSee('Cookie Policy');
    }
}

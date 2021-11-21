<?php

namespace Esign\UnderscoreTranslatable;

use Esign\UnderscoreTranslatable\Tests\Models\Post;
use Esign\UnderscoreTranslatable\Tests\TestCase;
use Illuminate\Support\Facades\App;

class UnderscoreTranslatableTest extends TestCase
{
    /** @test */
    public function it_can_check_if_an_attribute_is_translatable()
    {
        $post = new Post();
        $this->assertTrue($post->isTranslatableAttribute('title'));
        $this->assertFalse($post->isTranslatableAttribute('non-translatable-field'));
    }

    /** @test */
    public function it_can_get_a_translatable_attribute_name()
    {
        $post = new Post();
        App::setLocale('nl');
        $this->assertEquals('title_nl', $post->getTranslatableAttributeName('title'));
        $this->assertEquals('title_en', $post->getTranslatableAttributeName('title', 'en'));
    }

    /** @test */
    public function it_wont_interfere_when_getting_non_translatable_attributes()
    {
        $post = new Post();
        $post->body = 'Test';

        $this->assertEquals('Test', $post->body);
    }

    /** @test */
    public function it_wont_interfere_when_setting_non_translatable_attributes()
    {
        $post = new Post();
        $post->setAttribute('body', 'Test');

        $this->assertEquals('Test', $post->body);
    }

    /** @test */
    public function it_can_get_a_translation()
    {
        $post = new Post();
        $post->title_en = 'Test en';
        $post->title_nl = 'Test nl';

        $this->assertEquals('Test nl', $post->getTranslation('title', 'nl'));
        $this->assertEquals('Test en', $post->getTranslation('title', 'en'));
        $this->assertNull($post->getTranslation('title', 'fr'));
    }

    /** @test */
    public function it_can_get_a_translation_using_a_fallback()
    {
        $post = new Post();
        $post->title_en = 'Test en';
        $post->title_nl = 'Test nl';

        $this->assertEquals('Test nl', $post->getTranslation('title', 'nl', true));
        $this->assertEquals('Test en', $post->getTranslation('title', 'en', true));
        $this->assertEquals('Test en', $post->getTranslation('title', 'fr', true));
    }

    /** @test */
    public function it_can_get_a_translation_without_using_a_fallback()
    {
        $post = new Post();
        $post->title_en = 'Test en';
        $post->title_nl = 'Test nl';

        $this->assertEquals('Test nl', $post->getTranslation('title', 'nl', false));
        $this->assertEquals('Test en', $post->getTranslation('title', 'en', false));
        $this->assertNull($post->getTranslation('title', 'fr', false));
    }

    /** @test */
    public function it_can_get_a_translation_with_a_fallback()
    {
        $post = new Post();
        $post->title_en = 'Test en';
        $post->title_nl = 'Test nl';

        $this->assertEquals('Test nl', $post->getTranslationWithFallback('title', 'nl'));
        $this->assertEquals('Test en', $post->getTranslationWithFallback('title', 'en'));
        $this->assertEquals('Test en', $post->getTranslationWithFallback('title', 'fr'));
    }

    /** @test */
    public function it_can_get_a_translation_without_a_fallback()
    {
        $post = new Post();
        $post->title_en = 'Test en';
        $post->title_nl = 'Test nl';

        $this->assertEquals('Test nl', $post->getTranslationWithoutFallback('title', 'nl'));
        $this->assertEquals('Test en', $post->getTranslationWithoutFallback('title', 'en'));
        $this->assertNull($post->getTranslationWithoutFallback('title', 'fr'));
    }

    /** @test */
    public function it_can_get_a_translatable_attribute_using_a_method_with_a_fallback()
    {
        $post = new Post();
        $post->title_en = 'Test en';
        $post->title_nl = null;

        $this->assertEquals('Test en', $post->getTranslation('title', 'nl', true));
        $this->assertEquals('Test en', $post->getTranslation('title', 'en', true));
        $this->assertEquals('Test en', $post->getTranslation('title', 'fr', true));
    }

    /** @test */
    public function it_can_get_a_translation_using_a_property()
    {
        $post = new Post();
        $post->title_en = 'Test en';
        $post->title_nl = 'Test nl';

        App::setLocale('nl');
        $this->assertEquals('Test nl', $post->title);

        App::setLocale('en');
        $this->assertEquals('Test en', $post->title);

        App::setLocale('fr');
        $this->assertNull($post->title);
    }

    /** @test */
    public function it_can_get_a_translation_using_an_accessor()
    {
        $post = new Post();
        $post->field_with_accessor_en = 'Test en';

        App::setLocale('en');
        $this->assertEquals('test en', $post->field_with_accessor);
    }

    /** @test */
    public function it_can_set_a_translatable_attribute_using_a_method()
    {
        $post = new Post();
        $post->setTranslation('title', 'nl', 'Test nl');
        $post->setTranslation('title', 'en', 'Test en');

        $this->assertEquals('Test nl', $post->title_nl);
        $this->assertEquals('Test en', $post->title_en);
    }

    /** @test */
    public function it_can_set_a_translatable_attribute_using_a_property()
    {
        $post = new Post();
        $post->title = 'Test en';

        $this->assertEquals('Test en', $post->title_en);
    }

    /** @test */
    public function it_can_set_a_translated_attribute_using_a_property()
    {
        $post = new Post();
        $post->title_nl = 'Test nl';
        $post->title_en = 'Test en';

        $this->assertEquals('Test nl', $post->title_nl);
        $this->assertEquals('Test en', $post->title_en);
    }

    /** @test */
    public function it_can_set_a_translatable_attribute_using_a_mutator()
    {
        $post = new Post();
        $post->setTranslation('field_with_mutator', 'nl', 'Test nl');

        $this->assertEquals('TEST NL', $post->field_with_mutator_nl);
    }

    /** @test */
    public function it_can_set_a_translatable_attribute_using_make()
    {
        $post = Post::make([
            'title' => 'Test en',
        ]);

        $this->assertEquals('Test en', $post->title_en);
    }

    /** @test */
    public function it_can_set_multiple_translatable_attributes_using_a_method()
    {
        $post = new Post();
        $post->setTranslations('title', [
            'nl' => 'Test nl',
            'en' => 'Test en',
        ]);

        $this->assertEquals('Test nl', $post->title_nl);
        $this->assertEquals('Test en', $post->title_en);
    }

    /** @test */
    public function it_can_set_multiple_translatable_attributes_using_make()
    {
        $post = Post::make([
            'title' => [
                'nl' => 'Test nl',
                'en' => 'Test en',
            ],
        ]);

        $this->assertEquals('Test nl', $post->title_nl);
        $this->assertEquals('Test en', $post->title_en);
    }
}

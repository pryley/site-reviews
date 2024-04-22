<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Html\FieldCondition;
use GeminiLabs\SiteReviews\Modules\Html\ReviewForm;
use GeminiLabs\SiteReviews\Modules\Validator\DefaultValidator;
use GeminiLabs\SiteReviews\Request;

class ConditionsTest extends \WP_UnitTestCase
{
    use Setup;

    protected array $required;
    protected $config;

    public function testConditionContains()
    {
        $field = $this->field([
            'type' => 'text',
            'value' => 'foo bar',
        ]);
        $values = [
            '' => true,
            'foo' => true,
            'bar' => true,
            'foo bar' => true,
            'foo,bar' => false,
            'foobar' => false,
        ];
        foreach ($values as $value => $expected) {
            $this->assertTrue($expected === $this->testCondition($field, 'contains', $value));
        }
    }

    public function testConditionContainsWithMultiField()
    {
        $field = $this->field([
            'type' => 'select',
            'multiple' => true,
            'options' => ['Foo', 'Bar', 'Abc', 'Xyz'],
            'value' => ['Foo', 'Bar'],
        ]);
        $values = [
            '' => true,
            'Foo' => true,
            'Bar' => true,
            'Abc, Foo' => true,
            'Abc' => false,
        ];
        foreach ($values as $value => $expected) {
            $this->assertTrue($expected === $this->testCondition($field, 'contains', $value));
        }
    }

    public function testConditionEquals()
    {
        $field = $this->field([
            'type' => 'text',
            'value' => 'foo bar',
        ]);
        $values = [
            '' => false,
            'foo' => false,
            'bar' => false,
            'foo bar' => true,
        ];
        foreach ($values as $value => $expected) {
            $this->assertTrue($expected === $this->testCondition($field, 'equals', $value));
        }
        $field = $this->field([
            'type' => 'rating',
            'value' => 5,
        ]);
        $values = [
            '' => false,
            0 => false,
            '0' => false,
            4 => false,
            '4' => false,
            5 => true,
            '5' => true,
        ];
        foreach ($values as $value => $expected) {
            $this->assertTrue($expected === $this->testCondition($field, 'equals', $value));
        }
    }

    public function testConditionEqualsWithMultiField()
    {
        $field = $this->field([
            'type' => 'select',
            'multiple' => true,
            'options' => ['Foo', 'Bar', 'Abc', 'Xyz'],
            'value' => ['Foo', 'Bar'],
        ]);
        $values = [
            '' => false,
            'Foo' => false,
            'Bar' => false,
            'Abc, Foo' => false,
            'Foo,Bar' => true,
            'Foo, Bar' => true,
            'Bar, Foo' => true,
        ];
        foreach ($values as $value => $expected) {
            $this->assertTrue($expected === $this->testCondition($field, 'equals', $value));
        }
    }

    public function testConditionGreater()
    {
        $field = $this->field([
            'type' => 'rating',
            'value' => 3,
        ]);
        $values = [
            '' => true,
            '0' => true,
            '1' => true,
            '4' => false,
            '5' => false,
            0 => true,
            1 => true,
            4 => false,
            5 => false,
        ];
        foreach ($values as $value => $expected) {
            $this->assertTrue($expected === $this->testCondition($field, 'greater', $value));
        }
    }

    public function testConditionLess()
    {
        $field = $this->field([
            'type' => 'rating',
            'value' => 3,
        ]);
        $values = [
            '' => false,
            '0' => false,
            '4' => true,
            '5' => true,
            0 => false,
            4 => true,
            5 => true,
        ];
        foreach ($values as $value => $expected) {
            $this->assertTrue($expected === $this->testCondition($field, 'less', $value));
        }
    }

    public function testConditionNot()
    {
        $field = $this->field([
            'type' => 'text',
            'value' => 'foo bar',
        ]);
        $values = [
            '' => true,
            'foo' => true,
            'bar' => true,
            'foo bar' => false,
        ];
        foreach ($values as $value => $expected) {
            $this->assertTrue($expected === $this->testCondition($field, 'not', $value));
        }
        $field = $this->field([
            'type' => 'rating',
            'value' => 5,
        ]);
        $values = [
            '' => true,
            0 => true,
            '0' => true,
            4 => true,
            '4' => true,
            5 => false,
            '5' => false,
        ];
        foreach ($values as $value => $expected) {
            $this->assertTrue($expected === $this->testCondition($field, 'not', $value));
        }
    }

    public function testConditionNotWithMultiField()
    {
        $field = $this->field([
            'type' => 'select',
            'multiple' => true,
            'options' => ['Foo', 'Bar', 'Abc', 'Xyz'],
            'value' => ['Foo', 'Bar'],
        ]);
        $values = [
            '' => true,
            'Foo' => false,
            'Bar' => false,
            'Abc, Foo' => false,
            'Foo,Bar' => false,
            'Foo, Bar' => false,
            'Bar, Foo' => false,
            'Abc' => true,
            'Abc, Xyz' => true,
        ];
        foreach ($values as $value => $expected) {
            $this->assertTrue($expected === $this->testCondition($field, 'not', $value));
        }
    }

    public function testConditionOverridesRequired()
    {
        $config = [
            'rating' => [
                'type' => 'rating',
                'required' => true,
            ],
            'title' => [
                'type' => 'text',
                'conditions' => 'all|rating:equals:5',
                'required' => true,
            ],
            'content' => [
                'type' => 'textarea',
                'conditions' => 'any|rating:equals:5',
                'required' => true,
            ],
        ];
        $this->overrideConfig($config);
        $request = new Request([
            'rating' => 1,
        ]);
        $this->assertTrue(
            glsr(DefaultValidator::class, compact('request'))->isValid()
        );
        $this->restoreConfig();
    }

    public function testMultiFieldConditionOverridesRequired()
    {
        $config = [
            'rating' => [
                'type' => 'rating',
                'required' => true,
            ],
            'title' => [
                'type' => 'text',
                'conditions' => 'any|rating:equals:5|foo:equals:Bar',
                'required' => true,
            ],
            'foo' => [
                'type' => 'checkbox',
                'options' => [
                    'Foo' => 'Foo',
                    'Bar' => 'Bar',
                    'Xyz' => 'Xyz',
                ],
                'required' => true,
            ],
        ];
        $this->overrideConfig($config);
        $request = new Request([
            'rating' => 1,
            'foo' => 'Bar',
        ]);
        $this->assertFalse(
            glsr(DefaultValidator::class, compact('request'))->isValid()
        );
        $request = new Request([
            'rating' => 1,
            'foo' => 'Xyz',
        ]);
        $this->assertTrue(
            glsr(DefaultValidator::class, compact('request'))->isValid()
        );
        $this->restoreConfig();
    }

    protected function field(array $args): FieldContract
    {
        return glsr(ReviewForm::class)->field('foobar', $args);
    }

    protected function overrideConfig(array $config): void
    {
        $this->config = fn () => $config;
        $this->required = glsr(OptionManager::class)->getArray('settings.forms.required');
        add_filter('site-reviews/review-form/fields', $this->config, 10);
        glsr(OptionManager::class)->set('settings.forms.required', []);
    }

    protected function restoreConfig(): void
    {
        if ($this->config) {
            remove_filter('site-reviews/review-form/fields', $this->config, 10);
        }
        if (isset($this->required)) {
            glsr(OptionManager::class)->set('settings.forms.required', $this->required);
        }
    }

    protected function testCondition(FieldContract $field, string $operator, $value): bool
    {
        $values = wp_parse_args(compact('operator', 'value'), [
            'name' => 'foobar',
        ]);
        return (new FieldCondition($values, $field))->isValid();
    }
}

<?php

namespace ShreyaSarker\LaraCrud\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ShreyaSarker\LaraCrud\Utils\TypeUtil;

class TypeUtilTest extends TestCase
{
    /** @test */
    public function it_returns_correct_field_types(): void
    {
        $types = TypeUtil::getFieldType();

        $this->assertEquals('text', $types['string']);
        $this->assertEquals('textarea', $types['text']);
        $this->assertEquals('password', $types['password']);
        $this->assertEquals('email', $types['email']);
        $this->assertEquals('number', $types['integer']);
        $this->assertEquals('checkbox', $types['boolean']);
        $this->assertEquals('date', $types['date']);
        $this->assertEquals('datetime-local', $types['datetime']);
        $this->assertEquals('select', $types['select']);
    }

    /** @test */
    public function it_returns_correct_validation_types(): void
    {
        $validations = TypeUtil::getValidationType();

        $this->assertEquals('required|string|max:255', $validations['string']);
        $this->assertEquals('nullable|string', $validations['text']);
        $this->assertEquals('required|email|max:255', $validations['email']);
        $this->assertEquals('required|integer', $validations['integer']);
        $this->assertEquals('required|boolean', $validations['boolean']);
        $this->assertEquals('required|date', $validations['date']);
    }

    /** @test */
    public function it_returns_correct_sql_column_types(): void
    {
        $sqlTypes = TypeUtil::getSqlColumnType();

        $this->assertEquals('string', $sqlTypes['string']);
        $this->assertEquals('text', $sqlTypes['text']);
        $this->assertEquals('mediumText', $sqlTypes['mediumtext']);
        $this->assertEquals('longText', $sqlTypes['longtext']);
        $this->assertEquals('integer', $sqlTypes['integer']);
        $this->assertEquals('bigInteger', $sqlTypes['bigint']);
        $this->assertEquals('decimal', $sqlTypes['decimal']);
        $this->assertEquals('boolean', $sqlTypes['boolean']);
        $this->assertEquals('date', $sqlTypes['date']);
        $this->assertEquals('dateTime', $sqlTypes['datetime']);
    }

    /** @test */
    public function all_field_types_have_validation_rules(): void
    {
        $fieldTypes = array_keys(TypeUtil::getFieldType());
        $validationTypes = array_keys(TypeUtil::getValidationType());

        foreach ($fieldTypes as $fieldType) {
            $this->assertContains(
                $fieldType,
                $validationTypes,
                "Field type '{$fieldType}' is missing validation rules"
            );
        }
    }

    /** @test */
    public function all_field_types_have_sql_column_types(): void
    {
        $fieldTypes = array_keys(TypeUtil::getFieldType());
        $sqlTypes = array_keys(TypeUtil::getSqlColumnType());

        foreach ($fieldTypes as $fieldType) {
            $this->assertContains(
                $fieldType,
                $sqlTypes,
                "Field type '{$fieldType}' is missing SQL column type"
            );
        }
    }
}

<?php

namespace Zhalil\Mapper\Tests;

use PHPUnit\Framework\TestCase;
use Zhalil\Mapper\Attribute\ArrayOf;
use Zhalil\Mapper\Attribute\CastWith;
use Zhalil\Mapper\Attribute\Hidden;
use Zhalil\Mapper\Attribute\MapFrom;
use Zhalil\Mapper\Attribute\MapInputName;
use Zhalil\Mapper\Attribute\MapTo;
use Zhalil\Mapper\Attribute\Strict;
use Zhalil\Mapper\Contract\CasterInterface;
use Zhalil\Mapper\Exception\MappingException;
use Zhalil\Mapper\Exception\ValidationException;
use Zhalil\Mapper\NamingStrategy\SnakeCaseNamingStrategy;
use Zhalil\Mapper\Validation\Attribute\Email;
use Zhalil\Mapper\Validation\Attribute\Required;
use function Zhalil\Mapper\map;

class MapperTest extends TestCase
{
    public function testMapWithCamelCaseDefault(): void
    {
        $object = map([
            'firstName' => 'John',
            'email' => 'john@example.com',
        ])->to(SimpleDto::class);

        $this->assertSame('John', $object->firstName);
        $this->assertSame('john@example.com', $object->email);
    }

    public function testMapWithSnakeCase(): void
    {
        $object = map([
            'first_name' => 'John',
            'email' => 'john@example.com',
        ])->to(SnakeCaseDto::class);

        $this->assertSame('John', $object->firstName);
        $this->assertSame('john@example.com', $object->email);
    }

    public function testNullablePropertyReturnsNullWhenMissing(): void
    {
        $object = map([
            'firstName' => 'John',
            'email' => 'john@example.com',
        ])->to(NullableDto::class);

        $this->assertNull($object->middleName);
    }

    public function testNonNullablePropertyDoesNotThrowWhenNotStrict(): void
    {
        $object = map([
            'email' => 'john@example.com',
        ])->to(SimpleDto::class);

        $this->assertSame('john@example.com', $object->email);
    }

    public function testStrictClassThrowsExceptionOnMissingProperty(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage("Failed to map data to Zhalil\Mapper\Tests\StrictClassDto");

        map([
            'email' => 'john@example.com',
        ])->to(StrictClassDto::class);
    }

    public function testStrictPropertyThrowsExceptionWhenMissing(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage("Failed to map data to Zhalil\Mapper\Tests\StrictPropertyDto");

        map([])->to(StrictPropertyDto::class);
    }

    public function testValidationCollectsAllErrors(): void
    {
        try {
            map([
                'first_name' => '',
                'email' => 'invalid',
            ])->to(ValidationDto::class);
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $this->assertTrue($errors->has('firstName'));
            $this->assertTrue($errors->has('email'));
            $this->assertGreaterThanOrEqual(2, $errors->count());
        }
    }

    public function testPropertyAttributeOverridesClassAttribute(): void
    {
        $object = map([
            'first_name' => 'John',
            'email' => 'john@example.com',
        ])->to(MixedStrategyDto::class);

        $this->assertSame('John', $object->firstName);
        $this->assertSame('john@example.com', $object->email);
    }

    public function testMapFromAttribute(): void
    {
        $object = map([
            'book_title' => 'Timeline Taxi',
            'author_name' => 'Brent',
        ])->to(MapFromDto::class);

        $this->assertSame('Timeline Taxi', $object->title);
        $this->assertSame('Brent', $object->author);
    }

    public function testMapToAttribute(): void
    {
        $object = new MapToDto();
        $object->title = 'Test Book';
        $object->author = 'John';

        $array = map($object)->toArray();

        $this->assertArrayHasKey('book_title', $array);
        $this->assertArrayHasKey('author_name', $array);
        $this->assertSame('Test Book', $array['book_title']);
        $this->assertSame('John', $array['author_name']);
    }

    public function testHiddenAttributeExcludesFromSerialization(): void
    {
        $object = new HiddenDto();
        $object->email = 'user@example.com';
        $object->password = 'secret';

        $array = map($object)->toArray();

        $this->assertArrayHasKey('email', $array);
        $this->assertArrayNotHasKey('password', $array);
        $this->assertSame('user@example.com', $array['email']);
    }

    public function testHiddenAttributeExcludesFromJson(): void
    {
        $object = new HiddenDto();
        $object->email = 'user@example.com';
        $object->password = 'secret';

        $json = map($object)->toJson();
        $decoded = json_decode($json, true);

        $this->assertArrayHasKey('email', $decoded);
        $this->assertArrayNotHasKey('password', $decoded);
    }

    public function testCastWithAttribute(): void
    {
        $object = map([
            'address' => '123 Main St, New York, NY 10001',
        ])->to(CastWithDto::class);

        $this->assertInstanceOf(Address::class, $object->address);
        $this->assertSame('123 Main St', $object->address->street);
        $this->assertSame('New York', $object->address->city);
    }

    public function testCollectionMapping(): void
    {
        $books = map([
            ['title' => 'Book 1', 'author' => 'Author 1'],
            ['title' => 'Book 2', 'author' => 'Author 2'],
            ['title' => 'Book 3', 'author' => 'Author 3'],
        ])->collection()->to(BookDto::class);

        $this->assertIsArray($books);
        $this->assertCount(3, $books);
        $this->assertSame('Book 1', $books[0]->title);
        $this->assertSame('Book 2', $books[1]->title);
        $this->assertSame('Book 3', $books[2]->title);
    }

    public function testToArrayWithNestedObjects(): void
    {
        $object = new NestedParentDto();
        $object->name = 'Parent';
        $object->child = new NestedChildDto();
        $object->child->value = 'Child Value';

        $array = map($object)->toArray();

        $this->assertIsArray($array['child']);
        $this->assertSame('Child Value', $array['child']['value']);
    }

    public function testToJson(): void
    {
        $object = new SimpleDto();
        $object->firstName = 'John';
        $object->email = 'john@example.com';

        $json = map($object)->toJson();
        $decoded = json_decode($json, true);

        $this->assertSame('John', $decoded['firstName']);
        $this->assertSame('john@example.com', $decoded['email']);
    }

    public function testToArrayWithDateTime(): void
    {
        $object = new DateTimeDto();
        $object->createdAt = new \DateTime('2024-01-15 10:30:00');

        $array = map($object)->toArray();

        $this->assertArrayHasKey('createdAt', $array);
        $this->assertStringContainsString('2024-01-15', $array['createdAt']);
    }

    public function testToArrayWithEnum(): void
    {
        $object = new EnumDto();
        $object->status = Status::ACTIVE;
        $object->priority = Priority::HIGH;

        $array = map($object)->toArray();

        $this->assertSame('active', $array['status']);
        $this->assertSame('HIGH', $array['priority']);
    }

    public function testDefaultValuesPreserved(): void
    {
        $object = map([
            'firstName' => 'John',
        ])->to(DefaultValueDto::class);

        $this->assertSame('John', $object->firstName);
        $this->assertSame('default@example.com', $object->email);
    }

    public function testPrivatePropertiesSupported(): void
    {
        $object = map([
            'publicField' => 'public value',
            'privateField' => 'private value',
        ])->to(PrivatePropertyDto::class);

        $this->assertSame('public value', $object->publicField);
        $this->assertSame('private value', $object->getPrivateField());
    }

    public function testMappingExceptionContainsContext(): void
    {
        try {
            map([
                'email' => 'test@example.com',
            ])->to(StrictClassDto::class);
        } catch (MappingException $e) {
            $this->assertSame(StrictClassDto::class, $e->getClassName());
            $this->assertSame(['firstName'], $e->getMissingFields());
            $this->assertSame(['email' => 'test@example.com'], $e->getProvidedData());
        }
    }

    public function testValidationExceptionContainsClassName(): void
    {
        try {
            map([
                'first_name' => '',
                'email' => 'invalid',
            ])->to(ValidationDto::class);
        } catch (ValidationException $e) {
            $this->assertSame(ValidationDto::class, $e->getClassName());
            $this->assertStringContainsString('Validation failed', $e->getMessage());
        }
    }

    public function testNestedObjectWithConstructorPropertyPromotion(): void
    {
        $object = map([
            'title' => 'Test Order',
            'items' => [
                ['productId' => '1', 'quantity' => 2],
                ['productId' => '2', 'quantity' => 3],
            ],
        ])->to(OrderDto::class);

        $this->assertSame('Test Order', $object->getTitle());
        $this->assertCount(2, $object->getItems());
        $this->assertInstanceOf(OrderItemDto::class, $object->getItems()[0]);
        $this->assertSame('1', $object->getItems()[0]->productId);
        $this->assertSame(3, $object->getItems()[1]->quantity);
    }

    public function testNestedObjectWithArrayOfAttribute(): void
    {
        $object = map([
            'tags' => [
                ['name' => 'php'],
                ['name' => 'mapper'],
            ],
        ])->to(TagsContainerDto::class);

        $this->assertCount(2, $object->tags);
        $this->assertInstanceOf(TagDto::class, $object->tags[0]);
        $this->assertSame('php', $object->tags[0]->name);
        $this->assertSame('mapper', $object->tags[1]->name);
    }

    public function testDeeplyNestedObjects(): void
    {
        $object = map([
            'name' => 'Company',
            'departments' => [
                [
                    'name' => 'IT',
                    'employees' => [
                        ['name' => 'John', 'email' => 'john@company.com'],
                        ['name' => 'Jane', 'email' => 'jane@company.com'],
                    ],
                ],
            ],
        ])->to(CompanyDto::class);

        $this->assertSame('Company', $object->name);
        $this->assertCount(1, $object->departments);
        $this->assertInstanceOf(DepartmentDto::class, $object->departments[0]);
        $this->assertCount(2, $object->departments[0]->employees);
        $this->assertInstanceOf(EmployeeDto::class, $object->departments[0]->employees[0]);
        $this->assertSame('John', $object->departments[0]->employees[0]->name);
    }

    public function testNestedSingleObject(): void
    {
        $object = map([
            'title' => 'Article',
            'author' => [
                'name' => 'John',
                'email' => 'john@example.com',
            ],
        ])->to(ArticleDto::class);

        $this->assertSame('Article', $object->title);
        $this->assertInstanceOf(AuthorDto::class, $object->author);
        $this->assertSame('John', $object->author->name);
    }

    public function testDifferentNamespaceWithUseStatement(): void
    {
        $object = map([
            'test' => 'tr',
            'items' => [
                ['id' => '1'],
                ['id' => '2'],
            ],
        ])->to(Fixtures\SomeTestClass::class);

        $this->assertSame('tr', $object->getTest());
        $this->assertCount(2, $object->getItems());
        $this->assertInstanceOf(Fixtures\SomeItem::class, $object->getItems()[0]);
        $this->assertSame('1', $object->getItems()[0]->id);
    }

    public function testCrossNamespaceWithUseStatement(): void
    {
        $object = map([
            'test' => 'cross-ns',
            'items' => [
                ['id' => 'a'],
                ['id' => 'b'],
            ],
        ])->to(Fixtures\AnotherTestClass::class);

        $this->assertSame('cross-ns', $object->getTest());
        $this->assertCount(2, $object->getItems());
        $this->assertInstanceOf(\Zhalil\Mapper\ddd\SomeItem::class, $object->getItems()[0]);
        $this->assertSame('a', $object->getItems()[0]->id);
    }

    public function testDateValidationRules(): void
    {
        $object = map([
            'startDate' => '2024-01-15',
            'endDate' => '2024-01-20',
        ])->to(DateDto::class);

        $this->assertSame('2024-01-15', $object->startDate);
        $this->assertSame('2024-01-20', $object->endDate);
    }

    public function testDateAfterValidationFails(): void
    {
        try {
            map([
                'startDate' => '2023-12-25',
                'endDate' => '2024-01-20',
            ])->to(DateDto::class);
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $this->assertTrue($e->errors()->has('startDate'));
        }
    }

    public function testPasswordValidation(): void
    {
        $object = map([
            'password' => 'StrongP@ss123',
        ])->to(PasswordDto::class);

        $this->assertSame('StrongP@ss123', $object->password);
    }

    public function testPasswordValidationFailsTooShort(): void
    {
        try {
            map([
                'password' => 'short',
            ])->to(PasswordDto::class);
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $this->assertTrue($e->errors()->has('password'));
        }
    }

    public function testErrorBagToJson(): void
    {
        try {
            map([
                'first_name' => '',
                'email' => 'invalid',
            ])->to(ValidationDto::class);
        } catch (ValidationException $e) {
            $json = $e->errors()->toJson();
            $this->assertStringContainsString('firstName', $json);
            $this->assertStringContainsString('email', $json);
        }
    }
}

class SimpleDto
{
    public string $firstName;
    public string $email;
}

#[MapInputName(SnakeCaseNamingStrategy::class)]
class SnakeCaseDto
{
    public string $firstName;
    public string $email;
}

class NullableDto
{
    public string $firstName;
    public string $email;
    public ?string $middleName = null;
}

#[Strict]
class StrictClassDto
{
    public string $firstName;
    public string $email;
}

class StrictPropertyDto
{
    #[Strict]
    public string $requiredField;
    public ?string $optionalField = null;
}

#[MapInputName(SnakeCaseNamingStrategy::class)]
class ValidationDto
{
    #[Required]
    public string $firstName;

    #[Email]
    public string $email;
}

#[MapInputName(SnakeCaseNamingStrategy::class)]
class MixedStrategyDto
{
    public string $firstName;

    #[MapInputName(\Zhalil\Mapper\NamingStrategy\CamelCaseNamingStrategy::class)]
    public string $email;
}

class MapFromDto
{
    #[MapFrom('book_title')]
    public string $title;

    #[MapFrom('author_name')]
    public string $author;
}

class MapToDto
{
    #[MapTo('book_title')]
    public string $title;

    #[MapTo('author_name')]
    public string $author;
}

class HiddenDto
{
    public string $email;

    #[Hidden]
    public string $password;
}

class Address
{
    public string $street;
    public string $city;
}

class AddressCaster implements CasterInterface
{
    public function cast(mixed $input): Address
    {
        $parts = explode(', ', $input);
        $address = new Address();
        $address->street = $parts[0] ?? '';
        $address->city = $parts[1] ?? '';
        return $address;
    }
}

class CastWithDto
{
    #[CastWith(AddressCaster::class)]
    public Address $address;
}

class BookDto
{
    public string $title;
    public string $author;
}

class NestedChildDto
{
    public string $value;
}

class NestedParentDto
{
    public string $name;
    public NestedChildDto $child;
}

class DateTimeDto
{
    public \DateTime $createdAt;
}

enum Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

enum Priority
{
    case LOW;
    case MEDIUM;
    case HIGH;
}

class EnumDto
{
    public Status $status;
    public Priority $priority;
}

class DefaultValueDto
{
    public string $firstName;
    public string $email = 'default@example.com';
}

class PrivatePropertyDto
{
    private string $privateField;
    public string $publicField;

    public function getPrivateField(): string
    {
        return $this->privateField;
    }
}

class OrderItemDto
{
    public string $productId;
    public int $quantity;
}

class OrderDto
{
    /**
     * @param string $title
     * @param OrderItemDto[] $items
     */
    public function __construct(
        private string $title,
        private array $items
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}

class TagDto
{
    public string $name;
}

class TagsContainerDto
{
    public function __construct(
        #[ArrayOf(TagDto::class)]
        public array $tags
    ) {}
}

class EmployeeDto
{
    public string $name;
    public string $email;
}

class DepartmentDto
{
    /**
     * @param string $name
     * @param EmployeeDto[] $employees
     */
    public function __construct(
        public string $name,
        public array $employees
    ) {}
}

class CompanyDto
{
    /**
     * @param string $name
     * @param DepartmentDto[] $departments
     */
    public function __construct(
        public string $name,
        public array $departments
    ) {}
}

class AuthorDto
{
    public string $name;
    public string $email;
}

class ArticleDto
{
    public string $title;
    public AuthorDto $author;
}

class DateDto
{
    #[\Zhalil\Mapper\Validation\Attribute\After('2024-01-01')]
    public string $startDate;

    #[\Zhalil\Mapper\Validation\Attribute\Before('2024-12-31')]
    public string $endDate;
}

class PasswordDto
{
    #[\Zhalil\Mapper\Validation\Attribute\Password(min: 8, letters: true, numbers: true)]
    public string $password;
}

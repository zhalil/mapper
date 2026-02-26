# Zhalil Mapper

PHP-библиотека для маппинга данных в объекты с автоматической валидацией. Требует PHP 8.2+.

## Установка

```bash
composer require zhalil/mapper
```

## Быстрый старт

```php
use Zhalil\Mapper\Validation\Attribute\{Required, Email, Min};
use function Zhalil\Mapper\map;

class UserDTO
{
    #[Required]
    #[Min(2)]
    public string $name;

    #[Required]
    #[Email]
    public string $email;

    public ?int $age = null;
}

$user = map([
    'name' => 'John',
    'email' => 'john@example.com',
])->to(UserDTO::class);

echo $user->name; // John
```

---

## Содержание

- [Маппинг](#маппинг)
- [Стратегии именования](#стратегии-именования)
- [Атрибуты маппинга](#атрибуты-маппинга)
- [Валидация](#валидация)
- [Коллекции](#коллекции)
- [Сериализация (объект → массив)](#сериализация-объект--массив)
- [Обработка ошибок](#обработка-ошибок)
- [Кастомные кастеры](#кастомные-кастеры)

---

## Маппинг

### Базовое использование

```php
use function Zhalil\Mapper\map;

class Product
{
    public string $name;
    public float $price;
    public ?string $description = null;
}

$product = map([
    'name' => 'iPhone',
    'price' => 999.99,
])->to(Product::class);
```

### Вложенные объекты

Маппер автоматически создаёт вложенные объекты по типу свойства:

```php
class Address
{
    public string $city;
    public string $street;
}

class User
{
    public string $name;
    public Address $address;
}

$user = map([
    'name' => 'John',
    'address' => [
        'city' => 'Moscow',
        'street' => 'Tverskaya',
    ],
])->to(User::class);

echo $user->address->city; // Moscow
```

### Массивы объектов

Используйте атрибут `#[ArrayOf]` или PHPDoc `@var ClassName[]`:

```php
use Zhalil\Mapper\Attribute\ArrayOf;

class Order
{
    public int $id;

    /** @var Item[] */
    public array $items;
}

class Item
{
    public string $name;
    public int $quantity;
}

$order = map([
    'id' => 1,
    'items' => [
        ['name' => 'Product 1', 'quantity' => 2],
        ['name' => 'Product 2', 'quantity' => 1],
    ],
])->to(Order::class);
```

Или с атрибутом:

```php
class Order
{
    public int $id;

    #[ArrayOf(Item::class)]
    public array $items;
}
```

---

## Стратегии именования

### Доступные стратегии

| Стратегия | Описание | Пример |
|-----------|----------|--------|
| `CamelCaseNamingStrategy` | По умолчанию, без изменений | `firstName` → `firstName` |
| `SnakeCaseNamingStrategy` | Преобразует в snake_case | `firstName` → `first_name` |

### Использование на уровне класса

```php
use Zhalil\Mapper\Attribute\MapInputName;
use Zhalil\Mapper\NamingStrategy\SnakeCaseNamingStrategy;

#[MapInputName(SnakeCaseNamingStrategy::class)]
class UserDTO
{
    public string $firstName;
    public string $lastName;
}

$user = map([
    'first_name' => 'John',
    'last_name' => 'Doe',
])->to(UserDTO::class);
```

### Переопределение на уровне свойства

```php
#[MapInputName(SnakeCaseNamingStrategy::class)]
class UserDTO
{
    public string $firstName;

    #[MapInputName(CamelCaseNamingStrategy::class)]
    public string $lastName;
}

$user = map([
    'first_name' => 'John',
    'lastName' => 'Doe',
])->to(UserDTO::class);
```

---

## Атрибуты маппинга

### #[MapFrom]

Явное указание ключа во входных данных:

```php
use Zhalil\Mapper\Attribute\MapFrom;

class User
{
    #[MapFrom('user_name')]
    public string $name;

    #[MapFrom('user_email')]
    public string $email;
}
```

### #[MapTo]

Явное указание ключа при сериализации:

```php
use Zhalil\Mapper\Attribute\MapTo;

class User
{
    #[MapTo('user_name')]
    public string $name;
}

$user = map(['name' => 'John'])->to(User::class);
$data = map($user)->toArray();
// ['user_name' => 'John']
```

### #[Strict]

Строгий режим — выбрасывает исключение при отсутствии обязательного поля:

```php
use Zhalil\Mapper\Attribute\Strict;

#[Strict]
class User
{
    public string $name; // Обязательно
    public ?string $email = null; // Опционально (nullable)
}

// Выбросит MappingException, т.к. 'name' отсутствует
map([])->to(User::class);
```

Можно применять к отдельным свойствам:

```php
class User
{
    #[Strict]
    public string $name;

    public ?string $email = null;
}
```

### #[Hidden]

Скрыть свойство при сериализации:

```php
use Zhalil\Mapper\Attribute\Hidden;

class User
{
    public string $name;

    #[Hidden]
    public string $password;
}

$user = map(['name' => 'John', 'password' => 'secret'])->to(User::class);
map($user)->toArray(); // ['name' => 'John'] — password скрыт
```

### #[MapOutputName]

Стратегия именования при сериализации (объект → массив):

```php
use Zhalil\Mapper\Attribute\MapOutputName;
use Zhalil\Mapper\NamingStrategy\SnakeCaseNamingStrategy;

#[MapOutputName(SnakeCaseNamingStrategy::class)]
class User
{
    public string $firstName;
    public string $lastName;
}

$user = map(['firstName' => 'John'])->to(User::class);
map($user)->toArray(); // ['first_name' => 'John', 'last_name' => null]
```

### #[CastWith]

Кастомное преобразование значения:

```php
use Zhalil\Mapper\Attribute\CastWith;
use Zhalil\Mapper\Contract\CasterInterface;

class UpperCaseCaster implements CasterInterface
{
    public function cast(mixed $input): mixed
    {
        return strtoupper($input);
    }
}

class User
{
    #[CastWith(UpperCaseCaster::class)]
    public string $name;
}

$user = map(['name' => 'john'])->to(User::class);
echo $user->name; // JOHN
```

---

## Валидация

### Правила валидации

| Атрибут | Параметры | Описание |
|---------|-----------|----------|
| `#[Required]` | — | Поле обязательно |
| `#[Nullable]` | — | Поле может быть null |
| `#[Email]` | — | Валидный email |
| `#[Url]` | — | Валидный URL |
| `#[Min(int\|float)]` | $min | Минимальное значение/длина |
| `#[Max(int\|float)]` | $max | Максимальное значение/длина |
| `#[Between(int, int)]` | $min, $max | Диапазон значений |
| `#[Size(int)]` | $size | Точное значение/длина |
| `#[In(...$values)]` | $values | Значение должно быть в списке |
| `#[NotIn(...$values)]` | $values | Значение не должно быть в списке |
| `#[Regex(string)]` | $pattern | Соответствие регулярному выражению |
| `#[Enum(string)]` | $enumClass | Значение из enum |
| `#[Confirmed]` | — | Подтверждение (field + field_confirmation) |
| `#[StringType]` | — | Строка |
| `#[IntegerType]` | — | Целое число |
| `#[Numeric]` | — | Число (int или float) |
| `#[BooleanType]` | — | Булево значение |
| `#[ArrayType]` | — | Массив |
| `#[Date]` | — | Валидная дата |
| `#[DateFormat(string)]` | $format | Дата в формате |
| `#[After(string)]` | $date | Дата после указанной |
| `#[Before(string)]` | $date | Дата до указанной |
| `#[AfterOrEqual(string)]` | $date | Дата после или равна |
| `#[BeforeOrEqual(string)]` | $date | Дата до или равна |
| `#[DateEquals(string)]` | $date | Дата равна |
| `#[Uuid]` | — | Валидный UUID |
| `#[Ip]` | — | Валидный IP |
| `#[Ipv4]` | — | Валидный IPv4 |
| `#[Ipv6]` | — | Валидный IPv6 |
| `#[MacAddress]` | — | Валидный MAC-адрес |
| `#[Ulid]` | — | Валидный ULID |
| `#[Timezone]` | — | Валидный часовой пояс |
| `#[Json]` | — | Валидный JSON |
| `#[Alpha]` | — | Только буквы |
| `#[AlphaNumeric]` | — | Буквы и цифры |
| `#[AlphaDash]` | — | Буквы, цифры, дефис, подчёркивание |
| `#[Uppercase]` | — | Верхний регистр |
| `#[Lowercase]` | — | Нижний регистр |
| `#[StartsWith(string)]` | $prefix | Начинается с |
| `#[EndsWith(string)]` | $suffix | Заканчивается на |
| `#[DoesntStartWith(string)]` | $prefix | Не начинается с |
| `#[DoesntEndWith(string)]` | $suffix | Не заканчивается на |
| `#[Digits(int)]` | $count | Точное количество цифр |
| `#[DigitsBetween(int, int)]` | $min, $max | Диапазон цифр |
| `#[Same(string)]` | $field | Равно другому полю |
| `#[Different(string)]` | $field | Отлично от другого поля |
| `#[GreaterThan(string)]` | $field | Больше другого поля |
| `#[GreaterThanOrEqualTo(string)]` | $field | Больше или равно |
| `#[LessThan(string)]` | $field | Меньше другого поля |
| `#[LessThanOrEqualTo(string)]` | $field | Меньше или равно |
| `#[MultipleOf(int)]` | $value | Кратно числу |
| `#[Prohibited]` | — | Запрещено |
| `#[ProhibitedIf(string, ...$values)]` | $field, $values | Запрещено, если поле = значение |
| `#[ProhibitedUnless(string, ...$values)]` | $field, $values | Запрещено, если поле ≠ значение |

### Password

```php
use Zhalil\Mapper\Validation\Attribute\Password;

class UserDTO
{
    #[Password(
        min: 8,          // Минимум 8 символов
        letters: true,   // Хотя бы одна буква
        mixedCase: true, // Верхний и нижний регистр
        numbers: true,   // Хотя бы одна цифра
        symbols: true    // Хотя бы один символ
    )]
    public string $password;
}
```

### Условные правила

```php
use Zhalil\Mapper\Validation\Attribute\Required;
use Zhalil\Mapper\Validation\Rule\{RequiredIf, RequiredWith, RequiredWithout};

class PaymentDTO
{
    public ?string $paymentMethod = null;

    #[Required]
    public string $cardNumber;
}

// Или через атрибуты условий (требуют реализации атрибутов):
// #[RequiredIf('paymentMethod', 'card')]
// #[RequiredWith('otherField')]
// #[RequiredWithout('otherField')]
```

### Примеры валидации

```php
use Zhalil\Mapper\Validation\Attribute\{Required, Email, Min, Max, Between, In, Regex};

class CreateUserDTO
{
    #[Required]
    #[Min(2)]
    #[Max(50)]
    public string $name;

    #[Required]
    #[Email]
    public string $email;

    #[Between(18, 100)]
    public int $age;

    #[In('admin', 'user', 'guest')]
    public string $role;

    #[Regex('/^[A-Z]{2}\d{6}$/')]
    public ?string $passportNumber = null;
}
```

---

## Коллекции

Для маппинга массива объектов используйте `collection()`:

```php
class User
{
    public string $name;
    public string $email;
}

$users = map([
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'Jane', 'email' => 'jane@example.com'],
])->collection()->to(User::class);

// $users — массив объектов User
```

---

## Сериализация (объект → массив)

Маппер поддерживает преобразование объектов обратно в массивы с учётом атрибутов.

### Базовое использование

```php
class User
{
    public string $name;
    public string $email;
}

$user = map(['name' => 'John', 'email' => 'john@example.com'])->to(User::class);
$data = map($user)->toArray();
// ['name' => 'John', 'email' => 'john@example.com']
```

### toJson()

```php
$json = map($user)->toJson();
```

### Атрибуты сериализации

#### #[MapTo] — переименование ключа

```php
use Zhalil\Mapper\Attribute\MapTo;

class User
{
    #[MapTo('user_name')]
    public string $name;
}

$user = map(['name' => 'John'])->to(User::class);
$data = map($user)->toArray();
// ['user_name' => 'John']
```

#### #[Hidden] — скрыть свойство

```php
use Zhalil\Mapper\Attribute\Hidden;

class User
{
    public string $name;

    #[Hidden]
    public string $password;
}

$user = map(['name' => 'John', 'password' => 'secret'])->to(User::class);
$data = map($user)->toArray();
// ['name' => 'John'] — password скрыт
```

#### #[MapOutputName] — стратегия именования

Преобразует имена свойств при сериализации:

```php
use Zhalil\Mapper\Attribute\MapOutputName;
use Zhalil\Mapper\NamingStrategy\SnakeCaseNamingStrategy;

#[MapOutputName(SnakeCaseNamingStrategy::class)]
class User
{
    public string $firstName;
    public string $lastName;
}

$user = map(['firstName' => 'John', 'lastName' => 'Doe'])->to(User::class);
$data = map($user)->toArray();
// ['first_name' => 'John', 'last_name' => 'Doe']
```

Переопределение на уровне свойства:

```php
#[MapOutputName(SnakeCaseNamingStrategy::class)]
class User
{
    public string $firstName;

    #[MapOutputName(CamelCaseNamingStrategy::class)]
    public string $lastName;
}

$user = map(['firstName' => 'John', 'lastName' => 'Doe'])->to(User::class);
$data = map($user)->toArray();
// ['first_name' => 'John', 'lastName' => 'Doe']
```

### Автоматическая сериализация типов

| Тип | Результат |
|-----|-----------|
| `BackedEnum` | `$enum->value` |
| `UnitEnum` | `$enum->name` |
| `DateTimeInterface` | ISO 8601 формат |
| Вложенный объект | Рекурсивный toArray() |
| Массив объектов | Рекурсивная обработка |

```php
enum Status: string
{
    case Active = 'active';
}

class Address
{
    public string $city;
}

class User
{
    public string $name;
    public Status $status;
    public \DateTime $createdAt;
    public Address $address;
}

$user = map([
    'name' => 'John',
    'status' => 'active',
    'createdAt' => '2024-01-01',
    'address' => ['city' => 'Moscow'],
])->to(User::class);

$data = map($user)->toArray();
// [
//     'name' => 'John',
//     'status' => 'active',
//     'createdAt' => '2024-01-01T00:00:00+00:00',
//     'address' => ['city' => 'Moscow']
// ]
```

### Сериализация коллекций

```php
$users = [/* массив объектов User */];
$data = map($users)->collection()->toArray();
// Массив массивов
```

---

## Обработка ошибок

### MappingException

Выбрасывается при ошибках маппинга:

```php
use Zhalil\Mapper\Exception\MappingException;

try {
    $user = map([])->to(User::class);
} catch (MappingException $e) {
    echo $e->getMessage();
    // "Failed to map data to User. Missing required fields: ['name', 'email']"
    
    $missing = $e->getMissingFields(); // ['name', 'email']
    $provided = $e->getProvidedData(); // []
}
```

### ValidationException

Выбрасывается при ошибках валидации:

```php
use Zhalil\Mapper\Exception\ValidationException;

try {
    $user = map([
        'name' => 'J',
        'email' => 'invalid',
    ])->to(UserDTO::class);
} catch (ValidationException $e) {
    $errors = $e->errors(); // ErrorBag
    
    $errors->all();      // ['name' => ['...'], 'email' => ['...']]
    $errors->has('email'); // true
    $errors->get('email'); // ['This value is not a valid email address.']
    $errors->isEmpty();    // false
    $errors->count();      // 2
    $errors->toJson();     // JSON-представление
}
```

---

## Кастомные кастеры

Создайте класс, реализующий `CasterInterface`:

```php
use Zhalil\Mapper\Contract\CasterInterface;

class DateTimeCaster implements CasterInterface
{
    public function cast(mixed $input): mixed
    {
        if ($input instanceof \DateTimeInterface) {
            return $input;
        }
        
        return new \DateTime($input);
    }
}
```

Использование:

```php
use Zhalil\Mapper\Attribute\CastWith;

class Event
{
    public string $title;

    #[CastWith(DateTimeCaster::class)]
    public \DateTime $startsAt;
}

$event = map([
    'title' => 'Conference',
    'startsAt' => '2024-06-15 10:00:00',
])->to(Event::class);

echo $event->startsAt->format('Y-m-d'); // 2024-06-15
```

---

## Полный пример

```php
use Zhalil\Mapper\Attribute\{MapInputName, MapOutputName, MapFrom, Hidden, Strict, ArrayOf};
use Zhalil\Mapper\NamingStrategy\SnakeCaseNamingStrategy;
use Zhalil\Mapper\Validation\Attribute\{Required, Email, Min, Max, Password};
use function Zhalil\Mapper\map;

class Address
{
    public string $city;
    public string $street;
}

#[MapInputName(SnakeCaseNamingStrategy::class)]
#[MapOutputName(SnakeCaseNamingStrategy::class)]
#[Strict]
class CreateUserDTO
{
    #[Required]
    #[Min(2)]
    #[Max(50)]
    public string $firstName;

    #[Required]
    #[Min(2)]
    #[Max(50)]
    public string $lastName;

    #[Required]
    #[Email]
    public string $email;

    #[Password(min: 8, letters: true, numbers: true)]
    public string $password;

    #[Hidden]
    public string $passwordHash;

    #[ArrayOf(Address::class)]
    public array $addresses = [];

    public function __construct()
    {
        $this->passwordHash = '';
    }
}

try {
    $dto = map([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'password' => 'Secure123',
        'addresses' => [
            ['city' => 'Moscow', 'street' => 'Tverskaya'],
        ],
    ])->to(CreateUserDTO::class);

    echo $dto->firstName; // John
    echo $dto->addresses[0]->city; // Moscow

    $data = map($dto)->toArray();
    // passwordHash скрыт, ключи в snake_case:
    // ['first_name' => 'John', 'last_name' => 'Doe', 'email' => '...', ...]

} catch (\Zhalil\Mapper\Exception\ValidationException $e) {
    foreach ($e->errors()->all() as $field => $messages) {
        foreach ($messages as $message) {
            echo "$field: $message\n";
        }
    }
} catch (\Zhalil\Mapper\Exception\MappingException $e) {
    echo "Mapping error: " . $e->getMessage();
}
```

---

## Тестирование

```bash
composer test
```

---

## Лицензия

MIT

<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Test\Unit\Model\Account\Data;

use PHPUnit\Framework\TestCase;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeDataGenerator;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;

/**
 * @since 3.5.3
 */
class FakeDataGeneratorTest extends TestCase
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeDataGenerator
     */
    private $fakeDataGenerator;

    protected function setUp(): void
    {
        $configMock = $this->createMock(Config::class);

        $fakeEmailMock = $this->getMockBuilder(FakeEmail::class)
                              ->disableOriginalConstructor()
                              ->onlyMethods(['generate', 'changeToReal'])
                              ->getMock();

        $this->fakeDataGenerator = new FakeDataGenerator($fakeEmailMock, $configMock);
    }

    /**
     * @dataProvider fakeEmailDataProvider
     * @dataProvider separatedEmailDataProvider
     * @dataProvider oneWordEmailDataProvider
     * @dataProvider multyseparatorEmailDataProvider
     *
     * @param array $dataIn
     * @param array $dataOut
     */
    public function testMultyseparatorEmail(array $dataIn, array $dataOut): void
    {
        self::assertSame($dataOut, $this->fakeDataGenerator->names($dataIn));
    }

    /**
     * @return \Generator
     */
    public function fakeEmailDataProvider(): \Generator
    {
        yield [
            'dataIn' => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
            'dataOut' => [
                'firstname' => 'Customer',
                'lastname'  => 'Unknown',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => '',
                'lastname'  => 'Parker',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
            'dataOut' => [
                'firstname' => 'Customer',
                'lastname'  => 'Parker',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => 'John',
                'lastname'  => '',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Unknown',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => 'John',
                'lastname'  => 'Parker',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Parker',
                'email'     => FakeEmail::FAKE_EMAIL_PREFIX . 'test@example.com'
            ],
        ];
    }

    /**
     * @return \Generator
     */
    public function separatedEmailDataProvider(): \Generator
    {
        yield [
            'dataIn' => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'jim.parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim',
                'lastname'  => 'Parker',
                'email'     => 'jim.parker@xyz.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => '',
                'lastname'  => 'Parker-Blake',
                'email'     => 'jim.parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim',
                'lastname'  => 'Parker-Blake',
                'email'     => 'jim.parker@xyz.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => 'John',
                'lastname'  => '',
                'email'     => 'jim-parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Parker',
                'email'     => 'jim-parker@xyz.com'
            ],
        ];
        yield [
            'dataIn' => [
                'firstname' => 'John',
                'lastname'  => 'Parker-Blake',
                'email'     => 'jim-parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Parker-Blake',
                'email'     => 'jim-parker@xyz.com'
            ],
        ];
    }

    /**
     * @return \Generator
     */
    public function oneWordEmailDataProvider(): \Generator
    {
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Customer',
                'lastname'  => 'Parker',
                'email'     => 'parker@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => 'Parker-Blake',
                'email'     => 'parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Customer',
                'lastname'  => 'Parker-Blake',
                'email'     => 'parker@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => 'John',
                'lastname'  => 'Parker-Blake',
                'email'     => 'parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Parker-Blake',
                'email'     => 'parker@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => 'John',
                'lastname'  => '',
                'email'     => 'qwerty@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Qwerty',
                'email'     => 'qwerty@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => 'John',
                'lastname'  => '',
                'email'     => 'qwerty123@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'John',
                'lastname'  => 'Qwerty123',
                'email'     => 'qwerty123@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => 'Parker-Blake',
                'email'     => 'qwerty123@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Customer',
                'lastname'  => 'Parker-Blake',
                'email'     => 'qwerty123@xyz.com'
            ],
        ];
    }

    /**
     * @return \Generator
     */
    public function multyseparatorEmailDataProvider(): \Generator
    {
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'jim-test@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim',
                'lastname'  => 'Test',
                'email'     => 'jim-test@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'jim.parker-man@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim',
                'lastname'  => 'Parker-man',
                'email'     => 'jim.parker-man@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'jim-test.parker@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim-test',
                'lastname'  => 'Parker',
                'email'     => 'jim-test.parker@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => '',
                'lastname'  => '',
                'email'     => 'jim-test.parker.office@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim-test',
                'lastname'  => 'Parker',
                'email'     => 'jim-test.parker.office@xyz.com'
            ],
        ];
        yield [
            'dataIn'  => [
                'firstname' => 'Jim-test',
                'lastname'  => '',
                'email'     => 'jim-test.parker.office@xyz.com'
            ],
            'dataOut' => [
                'firstname' => 'Jim-test',
                'lastname'  => 'Parker',
                'email'     => 'jim-test.parker.office@xyz.com'
            ],
        ];
    }
}

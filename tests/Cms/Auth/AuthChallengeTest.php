<?php

namespace Kirby\Cms;

use Kirby\Email\Email;

require_once __DIR__ . '/../mocks.php';

/**
 * @coversDefaultClass Kirby\Cms\Auth
 */
class AuthChallengeTest extends TestCase
{
    protected $app;
    protected $auth;
    protected $fixtures;

    public function setUp(): void
    {
        Email::$debug = true;
        Email::$emails = [];
        $_SERVER['SERVER_NAME'] = 'kirby.test';

        $this->app = new App([
            'options' => [
                'auth.trials' => 2
            ],
            'roots' => [
                'index' => $this->fixtures = __DIR__ . '/fixtures/AuthTest'
            ],
            'users' => [
                [
                    'email' => 'marge@simpsons.com',
                    'id'    => 'marge'
                ],
                [
                    'email' => 'test@exämple.com'
                ]
            ]
        ]);
        $this->app->visitor()->ip('10.1.123.234');
        Dir::make($this->fixtures . '/site/accounts');

        $this->auth = new Auth($this->app);
    }

    public function tearDown(): void
    {
        $this->app->session()->destroy();
        Dir::remove($this->fixtures);

        Email::$debug = false;
        Email::$emails = [];
        unset($_SERVER['SERVER_NAME']);
    }

    /**
     * @covers ::createChallenge
     * @covers ::rateLimitException
     */
    public function testCreateChallenge()
    {
        $session = $this->app->session();

        // existing user
        $this->assertSame('email', $this->auth->createChallenge('marge@simpsons.com'));
        preg_match('/^[0-9]{3} [0-9]{3}$/m', Email::$emails[0]->body()->text(), $codeMatches);

        $this->assertSame(1800, $session->timeout());
        $this->assertSame('marge@simpsons.com', $session->get('kirby.challenge.email'));
        $this->assertSame(0, $session->get('kirby.challenge.tries'));
        $this->assertSame('email', $session->get('kirby.challenge.type'));
        $this->assertTrue(password_verify(str_replace(' ', '', $codeMatches[0]), $session->get('kirby.challenge.code')));
        $this->assertSame(MockTime::$time + 600, $session->get('kirby.challenge.timeout'));

        // non-existing user
        $this->assertNull($this->auth->createChallenge('invalid@example.com'));
        $this->assertSame('invalid@example.com', $session->get('kirby.challenge.email'));
        $this->assertSame(0, $session->get('kirby.challenge.tries'));
        $this->assertSame('email', $session->get('kirby.challenge.type'));

        // verify rate-limiting log
        $data = [
            'by-ip' => [
                '87084f11690867b977a611dd2c943a918c3197f4c02b25ab59' => [
                    'time'   => MockTime::$time,
                    'trials' => 2
                ]
            ],
            'by-email' => [
                'marge@simpsons.com' => [
                    'time'   => MockTime::$time,
                    'trials' => 1
                ]
            ]
        ];
        $this->assertSame($data, $this->auth->log());

        // cannot create challenge when rate-limited
        $this->expectException('Kirby\Exception\PermissionException');
        $this->expectExceptionMessage('Invalid email or password');
        $this->auth->createChallenge('marge@simpsons.com');
    }

    /**
     * @covers ::createChallenge
     */
    public function testCreateChallengeCustomTimeout()
    {
        $app = $this->app->clone([
            'options' => [
                'panel.login.timeout' => 10
            ]
        ]);
        $auth    = $app->auth();
        $session = $app->session();

        $this->assertSame('email', $auth->createChallenge('marge@simpsons.com'));
        $this->assertSame(MockTime::$time + 10, $session->get('kirby.challenge.timeout'));

        $session->destroy();
    }

    /**
     * @covers ::createChallenge
     */
    public function testCreateChallengeLong()
    {
        $session = $this->app->session();

        $this->assertSame('email', $this->auth->createChallenge('marge@simpsons.com', true));
        $this->assertFalse($session->timeout());
    }

    /**
     * @covers ::createChallenge
     */
    public function testCreateChallengeWithPunycodeEmail()
    {
        $session = $this->app->session();

        $this->assertSame('email', $this->auth->createChallenge('test@xn--exmple-cua.com'));
        $this->assertSame('test@exämple.com', $session->get('kirby.challenge.email'));
    }

    /**
     * @covers ::verifyChallenge
     */
    public function testVerifyChallenge()
    {
        $session = $this->app->session();

        // no challenge currently stored
        $this->assertNull($this->auth->verifyChallenge('123456'));

        // invalid email address
        $session->set('kirby.challenge.email', 'invalid@example.com');
        $this->assertNull($this->auth->verifyChallenge('123456'));

        // valid user, but rate-limited
        $session->set('kirby.challenge.email', 'marge@simpsons.com');
        $session->set('kirby.challenge.code', password_hash('123456', PASSWORD_DEFAULT));
        $session->set('kirby.challenge.type', 'email');
        $session->set('kirby.challenge.tries', 2);
        $this->assertNull($this->auth->verifyChallenge('123456'));
        $this->assertNull($this->auth->verifyChallenge('123456'));
        $this->assertNull($this->auth->verifyChallenge('123456'));
        $this->assertSame(5, $session->get('kirby.challenge.tries'));

        // valid user, but time-limited
        $session->set('kirby.challenge.email', 'marge@simpsons.com');
        $session->set('kirby.challenge.code', password_hash('123456', PASSWORD_DEFAULT));
        $session->set('kirby.challenge.type', 'email');
        $session->set('kirby.challenge.tries', 0);
        $session->set('kirby.challenge.timeout', MockTime::$time - 1);
        $this->assertNull($this->auth->verifyChallenge('123456'));

        // valid user
        $session->set('kirby.challenge.email', 'marge@simpsons.com');
        $session->set('kirby.challenge.code', password_hash('123456', PASSWORD_DEFAULT));
        $session->set('kirby.challenge.type', 'email');
        $session->set('kirby.challenge.tries', 0);
        $session->set('kirby.challenge.timeout', MockTime::$time + 1);
        $this->assertSame(
            $this->app->user('marge@simpsons.com'),
            $this->auth->verifyChallenge('123456')
        );
        $this->assertSame(['kirby.userId' => 'marge'], $session->data()->get());

        // valid user, but invalid code
        $session->set('kirby.challenge.email', 'marge@simpsons.com');
        $session->set('kirby.challenge.code', password_hash('123456', PASSWORD_DEFAULT));
        $session->set('kirby.challenge.type', 'email');
        $session->set('kirby.challenge.tries', 0);
        $session->set('kirby.challenge.timeout', MockTime::$time + 1);
        $this->assertNull($this->auth->verifyChallenge('654321'));

        // invalid challenge type
        $this->expectException('Kirby\Exception\LogicException');
        $this->expectExceptionMessage('Invalid authentication challenge: test');
        $session->set('kirby.challenge.email', 'marge@simpsons.com');
        $session->set('kirby.challenge.code', password_hash('123456', PASSWORD_DEFAULT));
        $session->set('kirby.challenge.type', 'test');
        $session->set('kirby.challenge.tries', 0);
        $session->set('kirby.challenge.timeout', MockTime::$time + 1);
        $this->auth->verifyChallenge('123456');
    }
}
